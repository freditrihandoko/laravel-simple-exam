<?php

namespace App\Exports;

use App\Models\ExamResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ExamResultsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $examId;
    protected $groupId;
    protected $dateRange;

    public function __construct($examId, $groupId, $dateRange)
    {
        $this->examId = $examId;
        $this->groupId = $groupId;
        $this->dateRange = $dateRange;
    }

    public function collection()
    {
        $query = ExamResult::with(['exam', 'user.groups']);

        if ($this->examId) {
            $query->where('exam_id', $this->examId);
        }

        if ($this->groupId) {
            $query->whereHas('user.groups', function ($q) {
                $q->where('group_id', $this->groupId);
            });
        }

        if ($this->dateRange) {
            $dates = explode(' - ', $this->dateRange);
            $startDate = Carbon::createFromFormat('Y-m-d', trim($dates[0]))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', trim($dates[1]))->endOfDay();
            $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No', 'Exam Name', 'Start Time', 'Duration', 'Group Name', 'User Name', 'Score', 'Status'
        ];
    }

    public function map($examResult): array
    {
        static $counter = 1;
        return [
            $counter++,
            $examResult->id,
            $examResult->exam->title,
            $examResult->start_time,
            $examResult->exam->exam_duration,
            $examResult->user->groups->first()->name,
            $examResult->user->name,
            $examResult->score,
            $examResult->status,
        ];
    }
}
