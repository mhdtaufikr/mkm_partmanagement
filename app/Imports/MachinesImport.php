<?php

namespace App\Imports;

use App\Models\Machine;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MachinesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function() use ($rows) {
            foreach ($rows as $row) {
                // Check if the combination of op_no, line, location, and plant already exists
                $existingMachine = Machine::where('op_no', $row['op_no_machine_no'])
                    ->where('line', $row['line'])
                    ->where('location', $row['location'])
                    ->where('plant', $row['plant'])
                    ->first();

                if ($existingMachine) {
                    // Rollback transaction and return with an error message
                    throw new \Exception('Operation number ' . $row['op_no_machine_no'] . ' already exists in the given plant, location, and line.');
                }

                // Insert the new machine data
                Machine::create([
                    'machine_no'        => $row['qr_no'],
                    'op_no'             => $row['op_no_machine_no'],
                    'plant'             => $row['plant'],
                    'location'          => $row['location'],
                    'line'              => $row['line'],
                    'asset_no'          => $row['asset_no'],
                    'machine_name'      => $row['machine_name'],
                    'process'           => $row['process'],
                    'maker'             => $row['maker'],
                    'model'             => $row['model_type'],
                    'serial_number'     => $row['serial_number'],
                    'mfg_date'          => $this->transformDate($row['mfg_date']),
                    'install_date'      => $this->transformDate($row['install_date']),
                    'img'               => null, // Handle file uploads separately if needed
                    'file'              => null, // Handle file uploads separately if needed
                    'electrical_co'     => $row['electrical_control'],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        });
    }

    /**
     * Transform Excel date to a Carbon instance or format as needed.
     *
     * @param mixed $value
     * @return string|null
     */
    private function transformDate($value)
    {
        if (!$value) {
            return null;
        }

        // Check if the value is a valid Excel date
        if (is_numeric($value)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
        }

        // If not, assume the date is in d/m/Y format
        return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
}

