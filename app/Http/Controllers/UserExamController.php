<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use App\Models\ExamResultDetail;

use function Laravel\Prompts\error;

class UserExamController extends Controller
{
    // public function index()
    // {

    //     $user = auth()->user();
    //     $exams = Exam::whereHas('groups.users', function ($query) use ($user) {
    //         $query->where('users.id', $user->id);
    //     })->with(['groups', 'results' => function ($query) use ($user) {
    //         $query->where('user_id', $user->id);
    //     }])->get();

    //     return view('user_exams.index', compact('exams'));
    // }
    // public function index()
    // {
    //     $user = auth()->user();
    //     $exams = Exam::whereHas('groups.users', function ($query) use ($user) {
    //         $query->where('users.id', $user->id);
    //     })->with(['groups', 'results' => function ($query) use ($user) {
    //         $query->where('user_id', $user->id);
    //     }])->get();

    //     foreach ($exams as $exam) {
    //         $examResult = $exam->results->first();
    //         if (!$examResult) {
    //             $exam->status = 'Not Started';
    //         } elseif ($examResult->status == 'completed') {
    //             $exam->status = 'Completed';
    //         } else {
    //             $exam->status = 'In Progress';
    //         }
    //     }

    //     return view('user_exams.index', compact('exams'));
    // }
    // public function index()
    // {
    //     $user = auth()->user();
    //     $currentDateTime = now();

    //     // Fetch all exams for the user
    //     $exams = Exam::whereHas('groups.users', function ($query) use ($user) {
    //         $query->where('users.id', $user->id);
    //     })->with(['groups', 'results' => function ($query) use ($user) {
    //         $query->where('user_id', $user->id);
    //     }, 'topics'])->get();

    //     // Separate exams into past, available, and upcoming
    //     $pastExams = [];
    //     $availableExams = [];
    //     $upcomingExams = [];

    //     foreach ($exams as $exam) {
    //         $examResult = $exam->results->first();
    //         if (!$examResult) {
    //             $exam->status = 'Not Started';
    //         } elseif ($examResult->status == 'completed') {
    //             $exam->status = 'Completed';
    //         } else {
    //             $exam->status = 'In Progress';
    //         }

    //         if ($currentDateTime > $exam->exam_end) {
    //             $pastExams[] = $exam;
    //         } elseif ($currentDateTime >= $exam->exam_start && $currentDateTime <= $exam->exam_end) {
    //             $availableExams[] = $exam;
    //         } else {
    //             $upcomingExams[] = $exam;
    //         }
    //     }

    //     return view('user_exams.index', compact('pastExams', 'availableExams', 'upcomingExams'));
    // }

    public function index()
    {
        $user = auth()->user();
        $currentDateTime = now();

        // Fetch all exams for the user
        $exams = Exam::whereHas('groups.users', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->with(['groups', 'results' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }, 'topics'])->get();

        // Separate exams into past, available, and upcoming
        $pastExams = [];
        $availableExams = [];
        $upcomingExams = [];

        foreach ($exams as $exam) {
            $examResult = $exam->results->first();
            if (!$examResult) {
                $exam->status = 'Not Started';
            } elseif ($examResult->status == 'completed') {
                $exam->status = 'Completed';
                $exam->show_score = $exam->show_score;
                $exam->score = $examResult->score;
                $exam->has_essay = $exam->topics->some(function ($topic) {
                    return $topic->questions->some(function ($question) {
                        return $question->question_type == 'essay';
                    });
                });
            } else {
                $exam->status = 'In Progress';
            }

            if ($currentDateTime > $exam->exam_end) {
                $pastExams[] = $exam;
            } elseif ($currentDateTime >= $exam->exam_start && $currentDateTime <= $exam->exam_end) {
                $availableExams[] = $exam;
            } else {
                $upcomingExams[] = $exam;
            }
        }

        return view('user_exams.index', compact('pastExams', 'availableExams', 'upcomingExams'));
    }



    // public function startExam(Exam $exam)
    // {
    //     $user = auth()->user();
    //     $totalQuestions = $exam->questions()->count();

    //     $examResult = ExamResult::firstOrCreate(
    //         ['exam_id' => $exam->id, 'user_id' => $user->id],
    //         ['status' => 'in_progress', 'start_time' => now(), 'total_questions' => $totalQuestions]
    //     );

    //     if ($exam->shuffle_questions) {
    //         $questions = $exam->questions->shuffle();
    //     } else {
    //         $questions = $exam->questions;
    //     }

    //     foreach ($questions as $question) {
    //         if ($question->question_type === 'multiple_choice' && $exam->shuffle_answers) {
    //             $question->answers = $question->answers->shuffle();
    //         }
    //         ExamResultDetail::firstOrCreate(
    //             ['exam_result_id' => $examResult->id, 'question_id' => $question->id],
    //             ['is_correct' => null ?? 0, 'answer_id' => null, 'essay_answer' => null]
    //         );
    //     }

    //     return view('user_exams.start', compact('exam', 'examResult', 'questions'));
    // }
    public function startExam(Exam $exam)
    {
        $user = auth()->user();
        $currentDateTime = now();

        // Periksa apakah ujian ini tersedia untuk pengguna
        $userInGroup = $exam->groups()->whereHas('users', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->exists();

        if (!$userInGroup) {
            return redirect()->route('user_exams.index')->with('error', 'Anda tidak memiliki akses ke ujian ini.');
        }

        if ($currentDateTime < $exam->exam_start || $currentDateTime > $exam->exam_end) {
            return redirect()->route('user_exams.index')->with('error', 'Ujian ini tidak tersedia saat ini.');
        }

        // Periksa apakah pengguna sudah menyelesaikan ujian ini
        $examResult = ExamResult::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->first();

        if ($examResult && $examResult->status == 'completed') {
            return redirect()->route('user_exams.index')->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        // Buat atau ambil hasil ujian
        $totalQuestions = $exam->questions()->count();

        $examResult = ExamResult::firstOrCreate(
            ['exam_id' => $exam->id, 'user_id' => $user->id],
            ['status' => 'in_progress', 'start_time' => now(), 'total_questions' => $totalQuestions]
        );

        // Acak soal jika diperlukan
        if ($exam->shuffle_questions) {
            $questions = $exam->questions->shuffle();
        } else {
            $questions = $exam->questions;
        }

        // Simpan detail hasil ujian
        foreach ($questions as $question) {
            if ($question->question_type === 'multiple_choice' && $exam->shuffle_answers) {
                $question->answers = $question->answers->shuffle();
            }
            ExamResultDetail::firstOrCreate(
                ['exam_result_id' => $examResult->id, 'question_id' => $question->id],
                ['is_correct' => null ?? 0, 'answer_id' => null, 'essay_answer' => null]
            );
        }

        return view('user_exams.start', compact('exam', 'examResult', 'questions'));
    }



    public function submitExam(Request $request, Exam $exam)
    {
        $user = auth()->user();
        $examResult = ExamResult::where('exam_id', $exam->id)->where('user_id', $user->id)->firstOrFail();
        $examResult->end_time = now();
        $examResult->status = 'completed';

        $score = 0;
        $totalQuestions = $exam->questions()->count();

        foreach ($exam->questions as $question) {
            $detail = $examResult->details->where('question_id', $question->id)->first();

            if ($question->question_type == 'multiple_choice') {
                if ($detail->is_correct) {
                    $score++;
                }
            }
        }

        // Menghitung skor dalam skala 1-100
        $examResult->score = round(($score / $totalQuestions) * 100);
        $examResult->save();

        return redirect()->route('user_exams.index')->with('success', 'Exam submitted successfully.');
    }


    public function saveAnswer(Request $request)
    {
        $request->validate([
            'exam_result_id' => 'required|exists:exam_results,id',
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required',
        ]);

        $examResultId = $request->input('exam_result_id');
        $questionId = $request->input('question_id');
        $answer = $request->input('answer');

        $examResultDetail = ExamResultDetail::where('exam_result_id', $examResultId)
            ->where('question_id', $questionId)
            ->firstOrFail();

        $question = Question::with('answers')->findOrFail($questionId);

        if ($question->question_type === 'multiple_choice') {
            $selectedAnswer = $answer;
            $isCorrect = $question->answers->where('id', $selectedAnswer)->where('is_correct', true)->count() > 0;

            $examResultDetail->update([
                'answer_id' => $selectedAnswer,
                'is_correct' => $isCorrect,
                'essay_answer' => null,
            ]);
        } elseif ($question->question_type === 'essay') {
            $examResultDetail->update([
                'essay_answer' => $answer,
                'is_correct' => null ?? 0, // to be graded manually
                'answer_id' => null,
            ]);
        }

        return response()->json(['message' => 'Answer saved successfully.']);
    }
    // public function saveAnswer(Request $request)
    // {
    //     $request->validate([
    //         'exam_id' => 'required|exists:exams,id',
    //         'exam_result_id' => 'required|exists:exam_results,id',
    //         'question_id' => 'required|exists:questions,id',
    //         'answer' => 'required',
    //     ]);

    //     $user = auth()->user();
    //     $questionId = $request->question_id;
    //     $answer = $request->answer;
    //     $examResultId = $request->exam_result_id;

    //     // Pastikan pertanyaan milik exam yang sedang dikerjakan
    //     $question = Question::findOrFail($questionId);
    //     if (!$question->exam->id == $request->exam_id) {
    //         abort(404);
    //     }

    //     // Periksa tipe pertanyaan dan simpan jawaban sesuai
    //     if ($question->question_type === 'multiple_choice') {
    //         $selectedAnswer = $answer;

    //         $isCorrect = $question->answers()
    //             ->where('id', $selectedAnswer)
    //             ->where('is_correct', true)
    //             ->exists();

    //         ExamResultDetail::updateOrCreate(
    //             [
    //                 'exam_result_id' => $examResultId,
    //                 'question_id' => $questionId,
    //             ],
    //             [
    //                 'answer_id' => $selectedAnswer,
    //                 'is_correct' => $isCorrect,
    //                 'essay_answer' => null,
    //             ]
    //         );
    //     } elseif ($question->question_type === 'essay') {
    //         ExamResultDetail::updateOrCreate(
    //             [
    //                 'exam_result_id' => $examResultId,
    //                 'question_id' => $questionId,
    //             ],
    //             [
    //                 'essay_answer' => $answer,
    //                 'is_correct' => null ?? 0, // to be graded manually
    //                 'answer_id' => null ?? 0,
    //             ]
    //         );
    //     }

    //     return response()->json(['message' => 'Answer saved successfully.']);
    // }



    public function show($id)
    {
        return redirect()->route('user_exams.index');
    }

    public function checkStatus(Request $request)
    {
        $examResultId = $request->input('exam_result_id');

        $examResult = ExamResult::find($examResultId);

        if (!$examResult) {
            return response()->json(['error' => 'Exam result not found.'], 404);
        }

        // Ambil status ujian dari database
        $status = $examResult->status;

        return response()->json(['status' => $status]);
    }
}
