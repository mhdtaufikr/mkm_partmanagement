<?php

namespace App\Imports;

use App\Models\Machine;
use App\Models\Part;
use App\Models\RepairPart;
use App\Models\MachineSparePartsInventory;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PartsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function() use ($rows) {
            foreach ($rows as $row) {
                // Check if the combination of op_no, plant, and line exists in the machine table
                $machine = Machine::where('op_no', $row['op_no_machine_no'])
                    ->where('plant', $row['plant'])
                    ->where('line', $row['line'])
                    ->first();

                if (!$machine) {
                    // Rollback transaction and return with an error message
                    throw new \Exception('Operation number ' . $row['op_no_machine_no'] . ' does not exist in the given plant and line.');
                }

                // Check if the part exists in the parts table
                $part = Part::where('material', $row['material_no'])
                    ->where('material_description', $row['description'])
                    ->first();

                if (!$part) {
                    // Rollback transaction and return with an error message
                    throw new \Exception('Part ' . $row['material_no'] . ' does not exist in the master parts table.');
                }

                // Query the repair_parts table to sum the repaired_qty
                $repair_qty = RepairPart::where('part_id', $part->id)->sum('repaired_qty');

                // Determine safety stock and estimation lifetime with default values if empty
                $safety_stock = $row['safety_stock'] ?? 0;
                $estimation_lifetime = $row['estimation_lifetime'] ?? 0;

                // Insert the new machine spare part inventory data
                MachineSparePartsInventory::create([
                    'part_id'              => $part->id,
                    'machine_id'           => $machine->id,
                    'critical_part'        => $part->material_description,
                    'type'                 => $part->type,
                    'estimation_lifetime'  => $estimation_lifetime,
                    'cost'                 => $part->total_value,
                    'last_replace'         => $this->transformDate($row['date']),
                    'safety_stock'         => $safety_stock,
                    'sap_stock'            => $part->total_stock,
                    'repair_stock'         => $repair_qty,
                    'created_at'           => now(),
                    'updated_at'           => now(),
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



