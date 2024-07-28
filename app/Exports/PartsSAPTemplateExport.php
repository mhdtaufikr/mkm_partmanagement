<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class PartsSAPTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'material',
            'material_description',
            'type',
            'plnt',
            'sloc',
            'vendor',
            'bun',
            'begining_qty',
            'begining_value',
            'received_qty',
            'received_value',
            'consumed_qty',
            'consumed_value',
            'total_stock',
            'total_value',
            'currency',
        ];
    }
}

