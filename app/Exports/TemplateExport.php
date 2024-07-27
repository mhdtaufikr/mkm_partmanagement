<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class TemplateExport implements WithHeadings, FromArray
{
    /**
     * Return the headings for the Excel sheet.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Barcode No.',
            'No./ Op No.',
            'Name',
            'Plant',
            'Location',
            'Line',
            'Department',
            'Shop',
            'Document No.',
            'Type',
            'Effective Date',
            'Manufacture Date',
            'Process',
            'Revision',
            'Procedure No.',
            'Checksheet Category',
            'Checksheet Type',
            'Item Name',
            'Item Spec',
        ];
    }

    /**
     * Return an empty array for the body of the Excel sheet.
     *
     * @return array
     */
    public function array(): array
    {
        return [];
    }
}
