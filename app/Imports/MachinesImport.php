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
                    'mfg_date'          => $row['mfg_date'],
                    'install_date'      => $row['install_date'],
                    'img'               => null, // Handle file uploads separately if needed
                    'file'              => null, // Handle file uploads separately if needed
                    'electrical_co'     => $row['electrical_control'],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        });
    }




}

