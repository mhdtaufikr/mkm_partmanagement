<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class MachineTemplateExport implements WithHeadings, FromArray
{
    public function headings(): array
    {
        return [
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
}

