<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ScheduleTemplateExport implements WithHeadings, FromArray, WithEvents
{
    public function headings(): array
    {
        return [
            'Type',           // The type of schedule (e.g., Mechanic, Electric)
            'Plant',          // The plant where the machine is located
            'OP No',          // The operation number
            'Frequency',      // The frequency of the maintenance schedule (1-12 months)
            'Month',
            'Day',           // The date of the maintenance schedule (1-31)
        ];
    }

    public function array(): array
    {
        return [
            ['Mechanic', 'ENGINE', 'AGV-01','2','1', '12'], // Example row to show the user how to fill the template
            ['Electric', 'STAMPING', 'PRE-01','3', '3', '15'],
            ['Powerhouse', 'STAMPING', 'PRE-01','1', '2', '15'],
            // Add more example rows if needed
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Add notes to the Excel sheet to help users understand how to fill the template
                $event->sheet->getDelegate()->getComment('A1')->getText()->createTextRun('Enter the schedule type (e.g., Mechanic, Electric).');
                $event->sheet->getDelegate()->getComment('B1')->getText()->createTextRun('Enter the plant name where the machine is located.');
                $event->sheet->getDelegate()->getComment('C1')->getText()->createTextRun('Enter the operation number (OP No) associated with the machine.');
                $event->sheet->getDelegate()->getComment('D1')->getText()->createTextRun('Enter the frequency of the maintenance schedule in months (1-12)');
                $event->sheet->getDelegate()->getComment('E1')->getText()->createTextRun('Enter the Month.');
                $event->sheet->getDelegate()->getComment('F1')->getText()->createTextRun('Enter the day of the month for the maintenance (1-31).');

                // Optionally, you can set column width to fit the text
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
            },
        ];
    }
}
