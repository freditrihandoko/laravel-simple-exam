<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Exam;
use App\Models\Topic;
use App\Models\Answer;
use App\Models\Question;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use App\Imports\QuestionsImport;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TopicRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\TopicQuestionRequest;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $topics = Topic::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(10);

        return view('topics.index', compact('topics', 'search'));
    }

    public function show(Request $request, $id)
    {
        try {
            $topic = Topic::findOrFail($id);
            $search = $request->input('search');

            // Paginate questions and eager load answers with search functionality
            $questions = $topic->questions()
                ->where(function ($query) use ($search) {
                    $query->where('question_text', 'like', "%{$search}%")
                        ->orWhereHas('answers', function ($q) use ($search) {
                            $q->where('answer_text', 'like', "%{$search}%");
                        });
                })
                ->with('answers')
                ->latest()
                ->paginate(5);

            return view('topics.show', compact('topic', 'questions', 'search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Topic not found');
        }
    }
    public function store(TopicRequest $request)
    {
        $topic = new Topic();
        $topic->name = $request->topic_name;
        $topic->description = $request->topic_description;
        $topic->status = $request->topic_status;
        $topic->save();

        return response()->json(['message' => 'Topic added successfully']);
    }

    public function update(TopicRequest $request, $id)
    {
        $topic = Topic::findOrFail($id);
        $topic->name = $request->topic_name;
        $topic->description = $request->topic_description;
        $topic->status = $request->topic_status;
        $topic->save();

        return response()->json(['message' => 'Topic updated successfully']);
    }

    public function addQuestion(TopicQuestionRequest $request, $id)
    {

        $topic = Topic::findOrFail($id);

        $question = new Question();
        $question->topic_id = $topic->id;
        $question->question_text = $request->question_text;
        $question->question_type = $request->question_type;

        // Handle question image upload
        if ($request->hasFile('question_image')) {
            $image = $request->file('question_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/question_images', $imageName);
            $question->question_image = $imageName;
        }

        $question->save();

        if ($request->question_type == 'multiple_choice') {
            foreach ($request->answers as $index => $answer) {
                $newAnswer = new Answer();
                $newAnswer->question_id = $question->id;
                $newAnswer->answer_text = $answer['text'];
                $newAnswer->is_correct = $answer['correct'] ?? 0;

                // Handle answer image upload
                if (isset($request->file('answers')[$index]['image'])) {
                    $image = $request->file('answers')[$index]['image'];
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->storeAs('public/answer_images', $imageName);
                    $newAnswer->answer_image = $imageName;
                }

                $newAnswer->save();
            }
        }

        return response()->json(['message' => 'Question added successfully']);
    }

    public function deleteQuestion(Topic $topic, Question $question)
    {
        try {
            // Menggunakan transaction untuk memastikan keberhasilan operasi atau rollback jika gagal
            DB::beginTransaction();

            // Cek apakah ada ujian yang terkait dengan topik ini
            $relatedExams = DB::table('exam_topic')
                ->where('topic_id', $topic->id)
                ->exists();

            if ($relatedExams) {
                return response()->json(['message' => 'Cannot delete question, there are exams related to this topic'], 403);
            }

            // Cek apakah ada hasil ujian terkait dengan pertanyaan ini
            $relatedResults = DB::table('exam_questions')
                ->where('question_id', $question->id)
                ->exists();

            if ($relatedResults) {
                return response()->json(['message' => 'Cannot delete question, there are exam results related to this question'], 403);
            }

            // Hapus jawaban terkait jika ada (jika pertanyaan adalah pilihan ganda)
            if ($question->question_type === 'multiple_choice') {
                $question->answers()->delete();
            }

            // Hapus pertanyaan itu sendiri
            $question->delete();

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return response()->json(['message' => 'Question deleted successfully']);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Memberikan respons kesalahan umum
            return response()->json(['message' =>  $e->getMessage()]);
        }
    }



    public function editQuestion(Topic $topic, Question $question)
    {
        // Mengambil pertanyaan berdasarkan $question yang diberikan
        //return view('topics.question_edit', compact('question'));
        return response()->json($question->load('answers'));
    }


    public function updateQuestion(Request $request, Topic $topic, Question $question)
    {
        $validatedData = $request->validate([
            'edit_question_text' => 'required|string',
            'edit_question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'answers.*.text' => 'required_if:question_type,multiple_choice',
            'answers.*.correct' => 'nullable|boolean',
            'answers.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah ada ujian yang terkait dengan topik ini
            $relatedExams = DB::table('exam_topic')
                ->where('topic_id', $topic->id)
                ->exists();

            // Cek apakah ada hasil ujian terkait dengan pertanyaan ini
            $relatedResults = DB::table('exam_questions')
                ->where('question_id', $question->id)
                ->exists();

            // Jika pertanyaan adalah multiple choice dan terkait dengan ujian atau hasil ujian, tidak bisa diubah
            if ($question->question_type === 'multiple_choice' && ($relatedExams || $relatedResults)) {
                return response()->json(['message' => 'Cannot edit multiple choice question, it is related to an exam or has exam results'], 403);
            }

            // Update question text
            $question->question_text = $validatedData['edit_question_text'];

            // Handle question image update
            if ($request->hasFile('edit_question_image')) {
                // Delete old image if exists
                if ($question->question_image) {
                    Storage::delete('public/question_images/' . $question->question_image);
                }

                $image = $request->file('edit_question_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/question_images', $imageName);
                $question->question_image = $imageName;
            }

            if ($question->question_type === 'multiple_choice') {
                // Delete old answers and their images
                foreach ($question->answers as $oldAnswer) {
                    if ($oldAnswer->answer_image) {
                        Storage::delete('public/answer_images/' . $oldAnswer->answer_image);
                    }
                }
                $question->answers()->delete();

                // Add new answers
                foreach ($request->answers as $index => $answerData) {
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->answer_text = $answerData['text'];
                    $answer->is_correct = isset($answerData['correct']) ? 1 : 0;

                    // Handle answer image
                    if (isset($request->file('answers')[$index]['image'])) {
                        $image = $request->file('answers')[$index]['image'];
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('public/answer_images', $imageName);
                        $answer->answer_image = $imageName;
                    }

                    $answer->save();
                }
            }

            $question->save();
            DB::commit();

            return response()->json(['message' => 'Question updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function importQuestions(Request $request, $id)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        $topic = Topic::findOrFail($id);

        try {
            Excel::import(new QuestionsImport($topic->id), $request->file('excel_file'));
            return redirect()->route('topics.show', $topic->id)->with('success', 'Questions imported successfully.');
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }
}
