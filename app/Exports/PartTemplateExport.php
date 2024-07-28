<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class PartTemplateExport implements WithHeadings, FromArray
{
    public function headings(): array
    {
        return [
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
}

