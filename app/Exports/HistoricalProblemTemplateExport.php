<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Comment\Comment;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HistoricalProblemTemplateExport implements FromArray, WithEvents
{
    public function array(): array
    {
        // Return the template headers
        return [
            ['plant', 'line', 'op No.', 'report', 'date', 'shift', 'shop', 'problem', 'cause', 'action', 'start_time', 'finish_time', 'category', 'balance', 'pic', 'remarks', 'status']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Add comments to help guide the user for each column
                $sheet = $event->sheet->getDelegate();

                // Define comments for each header cell
                $comments = [
                    'A1' => "plant: Fill with 'Engine' or 'Stamping'",
                    'B1' => 'line: Fill in the machine line',
                    'C1' => 'op No.: Fill with the machine operation number (OP No.)',
                    'D1' => 'report: Enter the report type or description Daily Report / Follow Up Problem',
                    'E1' => 'date: Fill with date in YYYY-MM-DD format',
                    'F1' => "shift: Fill with 'Day' or 'Night'",
                    'G1' => 'shop: Fill with the machine shop Electric, Mechanic, Power House',
                    'H1' => 'problem: Describe the problem encountered',
                    'I1' => 'cause: Provide the cause of the problem',
                    'J1' => 'action: Describe the action taken to resolve the problem',
                    'K1' => 'start_time: Enter start time in short time format (HH:MM)',
                    'L1' => 'finish_time: Enter finish time in short time format (HH:MM)',
                    'M1' => "category: Fill with 'Sparepart NG', 'Prev. Maintenance', 'Operator Error', or 'Other'",
                    'N1' => 'balance: Enter balance or calculate based on start and finish time',
                    'O1' => 'pic: Enter the name of the person in charge (PIC)',
                    'P1' => 'remarks: Add any additional remarks',
                    'Q1' => "status: Fill with 'OK', 'Not Good', 'Temporary', or 'Next'"
                ];

                // Loop through the comments and add them to the respective cells
                foreach ($comments as $cell => $commentText) {
                    $comment = $sheet->getComment($cell);
                    $comment->getText()->createTextRun($commentText);
                    $comment->setWidth('300px');
                    $comment->setHeight('100px');
                    $comment->getFillColor()->setRGB('FFFFE0'); // Light yellow background for better readability
                }

                // Set the alignment for the header cells to center
                $sheet->getStyle('A1:Q1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Optionally, you can apply styles to make the header stand out (e.g., bold text)
                $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
            }
        ];
    }
}
