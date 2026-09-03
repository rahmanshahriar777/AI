<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Contracts\View\View;

class VehicleOverviewExport implements FromView, WithStyles
{
    protected $vehicle, $vehiclechecklists, $overviewDates, $vehicleoverviews, $overviewMap;

    public function __construct($vehicle, $vehiclechecklists, $overviewDates, $vehicleoverviews, $overviewMap)
    {
        $this->vehicle = $vehicle;
        $this->vehiclechecklists = $vehiclechecklists;
        $this->overviewDates = $overviewDates;
        $this->vehicleoverviews = $vehicleoverviews;
        $this->overviewMap = $overviewMap;
    }

    public function view(): View
    {
        return view('exports.vehicleoverviews', [
            'vehicle' => $this->vehicle,
            'vehiclechecklists' => $this->vehiclechecklists,
            'overviewDates' => $this->overviewDates,
            'vehicleoverviews' => $this->vehicleoverviews,
            'overviewMap' => $this->overviewMap,
        ]);
    }

    public function styles(Worksheet $sheet)
{
    $highestRow = $sheet->getHighestRow();
    $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

    // 🔹 Style header row (row 1)
    $sheet->getStyle("A1:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColIndex) . "1")
        ->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD700'], // Gold
            ]
        ]);

    // 🔹 Style all rows (justified)
    $sheet->getStyle("A2:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColIndex) . $highestRow)
        ->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                'wrapText'   => true,
            ]
        ]);

    // 🔹 Conditional formatting per cell
    for ($row = 2; $row <= $highestRow; $row++) {
        for ($colIndex = 5; $colIndex <= $highestColIndex; $colIndex++) { // start at column E (index 5)
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $cellValue = trim((string) $sheet->getCell("{$colLetter}{$row}")->getValue());

            if (stripos($cellValue, 'OK') === 0) { // starts with OK
                $sheet->getStyle("{$colLetter}{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('C6EFCE'); // Light green
            } elseif (stripos($cellValue, '') === 0) { // starts with Note
                $sheet->getStyle("{$colLetter}{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEB9C'); // Light yellow
            } elseif (stripos($cellValue, 'N/A') === 0) { // starts with Fail
                $sheet->getStyle("{$colLetter}{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('F8CBAD'); // Light red
            }
        }
    }

    return [];
}

}
