<?php

namespace App\Exports;

use App\Models\FJobProgress;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class JobsOverviewExport implements FromView, WithStyles
{
    protected $jobprogress;

    public function __construct(FJobProgress $jobprogress)
    {
        $this->jobprogress = $jobprogress;
    }

    public function view(): View
    {
        return view('exports.joboverviews', [
            'jobprogress' => $this->jobprogress,
            'months' => $this->generateMonths($this->jobprogress->start_date, $this->jobprogress->end_date),
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $start = Carbon::parse($this->jobprogress->start_date);
        $end   = Carbon::parse($this->jobprogress->end_date);
        $period = CarbonPeriod::create($start, $end);

        // Dates start after these fixed columns: Task, Notes, Assigned To, Progress, Start, End
        $dateStartCol = 7; // column G
        $taskRow = 3;      // assuming header takes 2 rows, data starts at row 3

        foreach ($this->jobprogress->tasks as $task) {
            $taskStart = Carbon::parse($task->start_date);
            $taskEnd   = Carbon::parse($task->end_date);

            $colIndex = $dateStartCol;
            foreach ($period as $date) {
                $cell = Coordinate::stringFromColumnIndex($colIndex) . $taskRow;

                if ($date->between($taskStart, $taskEnd)) {
                    $color = null;
                    switch ($task->status) {
                        case 'completed':
                            $color = '92D050'; // green
                            break;
                        case 'in_progress':
                            $color = 'FFD966'; // yellow
                            break;
                        case 'not_started':
                            $color = '5BC0DE'; // blue
                            break;
                        case 'on_hold':
                        case 'cancelled':
                            $color = '343A40'; // dark grey
                            break;
                    }

                    if ($color) {
                        $sheet->getStyle($cell)->getFill()->applyFromArray([
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => $color],
                        ]);
                    }
                }

                $colIndex++;
            }

            $taskRow++;
        }
    }

    private function generateMonths($start, $end)
    {
        $months = [];
        $period = CarbonPeriod::create($start, $end);
        foreach ($period as $date) {
            $monthName = $date->format('M Y');
            $months[$monthName][] = $date->format('d');
        }
        return $months;
    }
}
