<?php

namespace App\Imports;

use App\Models\Machine;
use App\Models\Part;
use App\Models\RepairPart;
use App\Models\MachineSparePartsInventory;
use App\Models\MachineSparePartsInventoryLog; // Include this model for logging
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PartsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        set_time_limit(300);
        $errors = []; // Array to store error messages

        // Start database transaction
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                // Check if the combination of op_no, plant, and line exists in the machine table
                $machine = Machine::where('op_no', $row['op_no_machine_no'])
                    ->where('plant', $row['plant'])
                    ->where('line', $row['line'])
                    ->first();

                if (!$machine) {
                    $errors[] = 'Operation number ' . $row['op_no_machine_no'] . ' does not exist in the given plant and line.';
                    continue; // Continue to the next row
                }

                // Check if the part exists in the parts table
                $part = Part::where('material', $row['material_no'])->first();

                if (!$part) {
                    $errors[] = 'Part ' . $row['material_no'] . ' does not exist in the master parts table.';
                    continue; // Continue to the next row
                }

                // Query the repair_parts table to sum the repaired_qty
                $repair_qty = RepairPart::where('part_id', $part->id)->sum('repaired_qty');

                // Determine safety stock and estimation lifetime with default values if empty
                $safety_stock = $row['safety_stock'] ?? 0;
                $estimation_lifetime = $row['estimation_lifetime'] ?? 0;

                // Transform the date from the Excel file
                $newLastReplaceDate = $this->transformDate($row['date']);

                // Check for an existing entry in MachineSparePartsInventory with the same part_id and machine_id
                $existingEntry = MachineSparePartsInventory::where('part_id', $part->id)
                    ->where('machine_id', $machine->id)
                    ->first();

                if ($existingEntry) {
                    // Compare dates
                    $oldLastReplaceDate = $existingEntry->last_replace;

                    if (Carbon::parse($newLastReplaceDate)->lt(Carbon::parse($oldLastReplaceDate))) {
                        // If the new date is older, log the existing record and update the inventory
                        MachineSparePartsInventoryLog::create([
                            'inventory_id' => $existingEntry->id,
                            'old_last_replace' => $oldLastReplaceDate,
                            'new_last_replace' => $newLastReplaceDate,
                            'old_sap_stock' => $existingEntry->sap_stock,
                            'new_sap_stock' => $part->total_stock,
                            'old_repair_stock' => $existingEntry->repair_stock,
                            'new_repair_stock' => $repair_qty,
                            'qty' => 0, // Assuming no qty change is logged
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Update the inventory with the new date
                        $existingEntry->update([
                            'last_replace' => $newLastReplaceDate,
                            'sap_stock' => $part->total_stock,
                            'repair_stock' => $repair_qty,
                            'updated_at' => now(),
                        ]);
                    } else {
                        // If the old date is older, log the new record instead
                        MachineSparePartsInventoryLog::create([
                            'inventory_id' => $existingEntry->id,
                            'old_last_replace' => $newLastReplaceDate,
                            'new_last_replace' => $oldLastReplaceDate,
                            'old_sap_stock' => $existingEntry->sap_stock,
                            'new_sap_stock' => $part->total_stock,
                            'old_repair_stock' => $existingEntry->repair_stock,
                            'new_repair_stock' => $repair_qty,
                            'qty' => 0, // Assuming no qty change is logged
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    // No existing entry, insert the new record into inventory
                    MachineSparePartsInventory::create([
                        'part_id'              => $part->id,
                        'machine_id'           => $machine->id,
                        'critical_part'        => $part->material_description,
                        'type'                 => $part->type,
                        'estimation_lifetime'  => $estimation_lifetime,
                        'cost'                 => $part->total_value,
                        'last_replace'         => $newLastReplaceDate,
                        'safety_stock'         => $safety_stock,
                        'sap_stock'            => $part->total_stock,
                        'repair_stock'         => $repair_qty,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);
                }
            }

            // If there are errors, throw an exception to rollback
            if (!empty($errors)) {
                throw new \Exception('Errors encountered during import: ' . implode(', ', $errors));
            }

            // Commit transaction if there are no errors
            DB::commit();

        } catch (\Exception $e) {
            // Rollback transaction if there is an exception
            DB::rollBack();

            // Throw exception with combined error message
            throw new \Exception('Import failed with errors: ' . implode(', ', $errors));
        }
    }

    /**
     * Transform Excel date to Carbon instance or formatted string as needed.
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

        // Otherwise, assume the date is in the format d/m/Y
        return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
}
