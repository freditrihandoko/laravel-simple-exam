<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\User;
use App\Models\Group;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use App\Exports\ExamResultsExport;
use Maatwebsite\Excel\Facades\Excel;


class AdminExamResultController extends Controller
{
    public function index(Request $request)
    {
        $examId = $request->input('exam');
        $groupId = $request->input('group');
        $dateRange = $request->input('date_range');
        $sortField = $request->input('sort_field', 'start_time');
        $sortDirection = $request->input('sort_direction', 'asc');

        $query = ExamResult::with(['exam', 'user.groups'])->latest();

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        if ($groupId) {
            $query->whereHas('user.groups', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            });
        }

        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            $startDate = Carbon::createFromFormat('Y-m-d', trim($dates[0]))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', trim($dates[1]))->endOfDay();
            $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        // Handle sorting
        if ($sortField == 'exam_duration') {
            $query->join('exams', 'exam_results.exam_id', '=', 'exams.id')
                ->orderBy('exams.exam_duration', $sortDirection);
        } elseif ($sortField == 'user.name') {
            $query->join('users', 'exam_results.user_id', '=', 'users.id')
                ->orderBy('users.name', $sortDirection);
        } elseif ($sortField == 'start_time') {
            $query->orderBy('start_time', $sortDirection);
        } elseif ($sortField == 'status') {
            $query->orderBy('status', $sortDirection);
        } elseif ($sortField == 'score') {
            $query->orderBy('score', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $examResults = $query->paginate(10);

        $exams = Exam::all();
        $groups = Group::all();

        return view('exam_results.index', compact('examResults', 'exams', 'groups', 'sortField', 'sortDirection'));
    }


    public function show($id)
    {
        $examResult = ExamResult::with(['exam', 'user', 'details.question', 'details.answer'])->findOrFail($id);

        return view('exam_results.show', compact('examResult'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $selected = $request->input('selected', []);
        $extendMinutes = $request->input('extend_minutes', 0);

        if (empty($selected) || empty($action)) {
            return redirect()->route('exam_results.index')->with('error', 'No action or items selected.');
        }

        switch ($action) {
            case 'delete':
                ExamResult::whereIn('id', $selected)->delete();
                break;
            case 'stop':
                ExamResult::whereIn('id', $selected)->update(['status' => 'completed']);
                break;
            case 'open':
                ExamResult::whereIn('id', $selected)->update(['status' => 'in_progress', 'start_time' => Carbon::now()]);
                break;
            case 'extend':
                $extendMinutes = (int) $request->input('extend_minutes', 0);
                if ($extendMinutes > 0) {
                    foreach ($selected as $examResultId) {
                        $examResult = ExamResult::find($examResultId);
                        if ($examResult) {
                            $examResult->start_time = Carbon::parse($examResult->start_time)->addMinutes($extendMinutes);
                            $examResult->save();
                        }
                    }
                }
                break;
            default:
                return redirect()->route('exam_results.index')->with('error', 'Invalid action.');
        }

        return back()->with('success', 'Action applied successfully.');
    }

    public function export(Request $request)
    {
        $examId = $request->input('exam');
        $groupId = $request->input('group');
        $dateRange = $request->input('date_range');

        return Excel::download(new ExamResultsExport($examId, $groupId, $dateRange), 'exam_results.xlsx');
    }
}
