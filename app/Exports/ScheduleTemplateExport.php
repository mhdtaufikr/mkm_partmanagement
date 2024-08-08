<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class ScheduleTemplateExport implements WithHeadings
{
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'OP No.',
            'PM Type',
            'Frequency',
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'Agust',
            'September',
            'Oktober',
            'November',
            'December',
        ];
    }
}

