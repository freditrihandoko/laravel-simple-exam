<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\Group;
use App\Models\Topic;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateRange = $request->input('date_range');
        $dates = explode(' - ', $dateRange);

        $exams = Exam::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%");
        })
            ->when($dateRange, function ($query) use ($dates) {
                return $query->whereBetween('exam_start', [$dates[0], $dates[1]]);
            })
            ->latest()
            ->paginate(10);

        return view('exams.index', compact('exams', 'search', 'dateRange'));
    }

    public function create()
    {
        $topics = Topic::all();
        $groups = Group::withoutAdmin()->get();
        return view('exams.create', compact('topics', 'groups'));
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'exam_duration' => 'required|integer',
    //         'exam_start' => 'required|date',
    //         'exam_end' => 'required|date|after:exam_start',
    //         'shuffle_questions' => 'boolean',
    //         'shuffle_answers' => 'boolean',
    //         'topics' => 'required|array',
    //         'topics.*' => 'exists:topics,id',
    //         'groups' => 'required|array',
    //         'groups.*' => 'exists:groups,id',
    //     ]);

    //     dd($request->all());
    //     // Begin database transaction
    //     DB::beginTransaction();

    //     try {
    //         // Create the exam
    //         $exam = Exam::create($validated);

    //         // Sync groups with the exam
    //         $exam->groups()->sync($request->groups);

    //         // Process each topic and attach questions
    //         foreach ($request->topics as $topic_id => $value) {
    //             $num_questions = $request->input('num_questions.' . $topic_id, 0);
    //             $exam->topics()->attach($topic_id, ['num_questions' => $num_questions]);

    //             // Ensure we only attach questions if num_questions > 0
    //             if ($num_questions > 0) {
    //                 // Get random questions for the topic
    //                 $questions = Question::where('topic_id', $topic_id)
    //                     ->inRandomOrder()
    //                     ->take($num_questions)
    //                     ->get();

    //                 // Attach questions to the exam with pivot data
    //                 foreach ($questions as $question) {
    //                     $exam->questions()->attach($question->id, ['topic_id' => $topic_id]);
    //                 }
    //             }
    //         }

    //         // Commit transaction
    //         DB::commit();

    //         return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
    //     } catch (\Exception $e) {
    //         // Rollback transaction on error
    //         DB::rollBack();

    //         return redirect()->route('exams.index')->with('error', 'Failed to create exam. ' . $e->getMessage());
    //     }
    // }
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_duration' => 'required|integer',
            'exam_start' => 'required|date',
            'exam_end' => 'required|date|after:exam_start',
            'shuffle_questions' => 'sometimes|in:on,null',
            'shuffle_answers' => 'sometimes|in:on,null',
            'show_score' => 'sometimes|in:on,null',
            'topics' => 'required|array',
            'topics.*' => 'exists:topics,id',
            'num_questions' => 'nullable|array', // Allow null if no num_questions are provided
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,id',
            'question_types' => 'required|array', // New validation for question types
        ]);

        // Convert shuffle values to boolean
        $validated['shuffle_questions'] = filter_var($request->input('shuffle_questions'), FILTER_VALIDATE_BOOLEAN);
        $validated['shuffle_answers'] = filter_var($request->input('shuffle_answers'), FILTER_VALIDATE_BOOLEAN);
        $validated['show_score'] = filter_var($request->input('show_score'), FILTER_VALIDATE_BOOLEAN);

        // Begin database transaction
        DB::beginTransaction();

        try {
            $exam = Exam::create($validated);
            $exam->groups()->sync($request->groups);

            foreach ($request->topics as $topic_id) {
                $num_questions = isset($request->num_questions[$topic_id]) ? $request->num_questions[$topic_id] : 0;
                $exam->topics()->attach($topic_id, ['num_questions' => $num_questions]);

                if ($num_questions > 0) {
                    $questionTypes = $request->question_types[$topic_id] ?? [];

                    $questionsQuery = Question::where('topic_id', $topic_id);

                    if (in_array('multiple_choice', $questionTypes)) {
                        $questionsQuery->where('question_type', 'multiple_choice');
                    }

                    if (in_array('essay', $questionTypes)) {
                        $questionsQuery->orWhere('question_type', 'essay');
                    }

                    $questions = $questionsQuery->inRandomOrder()->take($num_questions)->get();

                    foreach ($questions as $question) {
                        $exam->questions()->attach($question->id, ['topic_id' => $topic_id]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('exams.show', $exam->id)
                ->with('success', 'Exam created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('exams.index')->with('error', 'Failed to create exam. ' . $e->getMessage());
        }
    }



    public function show($id)
    {
        $exam = Exam::with('topics', 'groups')->findOrFail($id);
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $groups = Group::withoutAdmin()->get();
        $topics = Topic::all();
        $examHasResults = $exam->results()->exists();

        return view('exams.edit', compact('exam', 'groups', 'topics', 'examHasResults'));
    }

    public function update(Request $request, Exam $exam)
    {
        $examHasResults = $exam->results()->exists();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_duration' => 'required|integer',
            'exam_start' => 'required|date',
            'exam_end' => 'required|date|after:exam_start',
            'shuffle_questions' => 'sometimes|in:on,null',
            'shuffle_answers' => 'sometimes|in:on,null',
            'show_score' => 'sometimes|in:on,null',
            // 'topics' => $examHasResults ? 'nullable|array' : 'required|array',
            'topics' => 'nullable|array', // Make topics nullable if exam has results
            'topics.*' => 'exists:topics,id',
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,id',
            'num_questions' => 'nullable|array',
            'question_type' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $exam->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'exam_duration' => $validated['exam_duration'],
                'exam_start' => $validated['exam_start'],
                'exam_end' => $validated['exam_end'],
                'shuffle_questions' => $request->has('shuffle_questions'),
                'shuffle_answers' => $request->has('shuffle_answers'),
                'show_score' => $request->has('show_score'),
            ]);

            $exam->groups()->sync($request->groups);

            if (!$examHasResults && isset($request->topics)) {
                // Detach only topics not in the request
                $existingTopics = $exam->topics->pluck('id')->toArray();
                $topicsToAttach = array_diff($request->topics, $existingTopics);

                foreach ($topicsToAttach as $topic_id) {
                    $num_questions = isset($request->num_questions[$topic_id]) ? $request->num_questions[$topic_id] : 0;
                    $exam->topics()->attach($topic_id, ['num_questions' => $num_questions]);

                    if ($num_questions > 0) {
                        $questionTypes = $request->question_type[$topic_id] ?? [];

                        $questionsQuery = Question::where('topic_id', $topic_id);

                        if (in_array('multiple_choice', $questionTypes)) {
                            $questionsQuery->where('question_type', 'multiple_choice');
                        }

                        if (in_array('essay', $questionTypes)) {
                            $questionsQuery->orWhere('question_type', 'essay');
                        }

                        $questions = $questionsQuery->inRandomOrder()->take($num_questions)->get();

                        foreach ($questions as $question) {
                            $exam->questions()->attach($question->id, ['topic_id' => $topic_id]);
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('exams.edit', $exam->id)
                ->withErrors('An error occurred while updating the exam. Please try again.' . $e->getMessage());
        }

        return redirect()->route('exams.show', $exam->id)
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->results()->exists()) {
            return redirect()->route('exams.index')->with('error', 'Cannot delete exam with results.');
        }

        try {
            $exam->groups()->detach();
            $exam->topics()->detach();
            $exam->questions()->detach();
            $exam->delete();
            return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('exams.index')->with('error', 'An error occurred while deleting the exam. Please try again.');
        }
    }

    public function bulkReschedule(Request $request)
    {
        $request->validate([
            'exam_ids' => 'required|array',
            'exam_ids.*' => 'exists:exams,id',
            'reschedule_action' => 'required|string|in:+1day,-1day,+1hour,-1hour,+7days,-7days',
        ]);

        $action = $request->reschedule_action;

        foreach ($request->exam_ids as $exam_id) {
            $exam = Exam::findOrFail($exam_id);
            $exam->exam_start = Carbon::parse($exam->exam_start);
            $exam->exam_end = Carbon::parse($exam->exam_end);

            switch ($action) {
                case '+1day':
                    $exam->exam_start->addDay();
                    $exam->exam_end->addDay();
                    break;
                case '-1day':
                    $exam->exam_start->subDay();
                    $exam->exam_end->subDay();
                    break;
                case '+1hour':
                    $exam->exam_start->addHour();
                    $exam->exam_end->addHour();
                    break;
                case '-1hour':
                    $exam->exam_start->subHour();
                    $exam->exam_end->subHour();
                    break;
                case '+7days':
                    $exam->exam_start->addDays(7);
                    $exam->exam_end->addDays(7);
                    break;
                case '-7days':
                    $exam->exam_start->subDays(7);
                    $exam->exam_end->subDays(7);
                    break;
            }

            $exam->save();
        }

        return back()->with('success', 'Exams rescheduled successfully.');
    }
}
