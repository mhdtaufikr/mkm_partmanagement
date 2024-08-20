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
                    // Check if the existing machine's data matches the new data
                    $updatedData = [
                        'machine_no'        => $row['qr_no'],
                        'asset_no'          => $row['asset_no'],
                        'machine_name'      => $row['machine_name'],
                        'process'           => $row['process'],
                        'maker'             => $row['maker'],
                        'model'             => $row['model_type'],
                        'serial_number'     => $row['serial_number'],
                        'mfg_date'          => $row['mfg_date'],
                        'install_date'      => $row['install_date'],
                        'electrical_co'     => $row['electrical_control'],
                        'updated_at'        => now(),
                    ];

                    // Compare the existing data with the new data
                    $existingData = $existingMachine->only(array_keys($updatedData));
                    if ($existingData == $updatedData) {
                        // Skip the update if no data has changed
                        continue;
                    }

                    // Update the existing machine if data has changed
                    $existingMachine->update($updatedData);
                } else {
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
            }
        });
    }

}

