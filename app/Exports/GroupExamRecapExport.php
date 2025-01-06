<?php

namespace App\Exports;

use App\Models\Exam;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class GroupExamRecapExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    protected $groupId;
    protected $dateRange;
    protected $exams;

    public function __construct($groupId, $dateRange)
    {
        $this->groupId = $groupId;
        $this->dateRange = $dateRange;

        $dates = explode(' - ', $this->dateRange);
        $startDate = Carbon::createFromFormat('Y-m-d', trim($dates[0]))->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', trim($dates[1]))->endOfDay();

        $this->exams = Exam::whereHas('results.user.groups', function ($query) {
            $query->where('group_id', $this->groupId);
        })->whereBetween('exam_start', [$startDate, $endDate])->get();
    }

    public function collection()
    {
        return User::whereHas('groups', function ($query) {
            $query->where('group_id', $this->groupId);
        })->with(['examResults' => function ($query) {
            $query->whereIn('exam_id', $this->exams->pluck('id'));
        }])->get();
    }

    public function headings(): array
    {
        $examTitles = $this->exams->pluck('title')->toArray();
        return array_merge(['No', 'Nama', 'Average Score'], $examTitles);
    }

    public function map($user): array
    {
        $examResults = $user->examResults->keyBy('exam_id');
        $totalExams = $this->exams->count();
        $totalScore = $this->exams->sum(function ($exam) use ($examResults) {
            return $examResults->has($exam->id) ? $examResults[$exam->id]->score : 0;
        });

        $averageScore = $totalExams > 0 ? $totalScore / $totalExams : 0;

        $examScores = $this->exams->map(function ($exam) use ($examResults) {
            return $examResults->has($exam->id) ? $examResults[$exam->id]->score : 0;
        })->toArray();

        return array_merge([
            $user->id,
            $user->name,
            $averageScore,
        ], $examScores);
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $group = Group::find($this->groupId);
                $dates = explode(' - ', $this->dateRange);
                $startDate = Carbon::createFromFormat('Y-m-d', trim($dates[0]))->format('d-m-Y');
                $endDate = Carbon::createFromFormat('Y-m-d', trim($dates[1]))->format('d-m-Y');

                $event->sheet->setCellValue('A1', 'Recap exam');
                $event->sheet->setCellValue('A2', 'Group Name: ' . $group->name);
                $event->sheet->setCellValue('A3', 'Date Range: ' . $startDate . ' - ' . $endDate);

                $event->sheet->mergeCells('A1:F1');
                $event->sheet->mergeCells('A2:F2');
                $event->sheet->mergeCells('A3:F3');

                $event->sheet->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getStyle('A3')->getFont()->setBold(true);

                $headingRange = 'A4:' . $event->sheet->getHighestColumn() . '4';
                $event->sheet->getStyle($headingRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFFF0'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Adjust row height
                $event->sheet->getRowDimension(4)->setRowHeight(30);

                // Auto size for 'No' and 'Nama'
                $event->sheet->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getColumnDimension('B')->setAutoSize(true);

                // Fixed width for 'Average Score' and exam titles
                $event->sheet->getColumnDimension('C')->setWidth(15);
                foreach (range('D', $event->sheet->getHighestColumn()) as $columnID) {
                    $event->sheet->getColumnDimension($columnID)->setWidth(15);
                }

                // Center align 'Average Score' and exam scores
                $dataRange = 'C5:' . $event->sheet->getHighestColumn() . $event->sheet->getHighestRow();
                $event->sheet->getStyle($dataRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Border for data cells
                $event->sheet->getStyle('A5:' . $event->sheet->getHighestColumn() . $event->sheet->getHighestRow())
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
