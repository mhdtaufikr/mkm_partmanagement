<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PartTemplateExport implements WithHeadings, FromArray, WithEvents
{
    public function headings(): array
    {
        return [
            'shop',
            'date',
            'material no',
            'description',
            'plant',
            'line',
            'op_no (machine no)'
        ];
    }

    public function array(): array
    {
        return [
            // Include any example data if necessary
            // Or return an empty array to have only the headers
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Add a note to cell A1
                $event->sheet->getDelegate()->getComment('A1')->getText()->createTextRun('ME/PH');
                // Add a note to cell B1
                $event->sheet->getDelegate()->getComment('B1')->getText()->createTextRun('Short Date Format');
            },
        ];
    }
}
