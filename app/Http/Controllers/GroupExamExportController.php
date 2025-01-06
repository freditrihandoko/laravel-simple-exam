<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\ExamResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GroupExamRecapExport;

class GroupExamExportController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('group_exam_export.index', compact('groups'));
    }

    public function export(Request $request)
    {
        $groupId = $request->input('group');
        $dateRange = $request->input('date_range');

        return Excel::download(new GroupExamRecapExport($groupId, $dateRange), 'group_exam_recap.xlsx');
    }
}
