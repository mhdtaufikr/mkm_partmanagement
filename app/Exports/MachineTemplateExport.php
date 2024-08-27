<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MachineTemplateExport implements WithHeadings, FromArray, WithEvents
{
    public function headings(): array
    {
        return [
            'shop',
            'plant',
            'location',
            'line',
            'op_no (machine no)',
            'qr_no',
            'asset_no',
            'machine_name',
            'process',
            'maker',
            'model / Type',
            'serial_number',
            'Mfg. Date',
            'Install Date',
            'electrical control',
            'Power Cap. (KVA)'
        ];
    }

    public function array(): array
    {
        return [
            // You can include some example data if necessary
            // Or return an empty array to have only the headers
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Initialize a comment for cell A1
                $comment = $event->sheet->getDelegate()->getComment('A1');

                // Set the text of the comment
                $comment->getText()->createTextRun('ME/PH');
            },
        ];
    }
}
