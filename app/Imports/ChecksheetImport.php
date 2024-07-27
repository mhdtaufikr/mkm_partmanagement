<?php

namespace App\Imports;

use App\Models\Machine;
use App\Models\Checksheet;
use App\Models\ChecksheetItem;
use App\Models\PreventiveMaintenance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChecksheetImport implements ToCollection, WithHeadingRow
{
    private $currentPreventiveMaintenanceId = null;
    private $currentChecksheetId = null;

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Handle dates from the row
                $effectiveDate = $this->transformDate($row['effective_date']);
                $mfgDate = $this->transformDate($row['manufacture_date']);

                // Check if the op_no exists in the machine table on the specified plant and line
                $machine = Machine::where('op_no', $row['no_op_no'])
                    ->where('plant', $row['plant'])
                    ->where('line', $row['line'])
                    ->first();

                if (!$machine) {
                    // Rollback transaction and return error message
                    DB::rollBack();
                    throw new \Exception('Operation number '.$row['no_op_no'].' does not exist in the machine table for the given plant and line.');
                }

                // Check if we need to create a new preventive maintenance record
                if ($this->currentPreventiveMaintenanceId === null || $machine->id !== $previousMachineId) {
                    // Insert or update preventive maintenance
                    $preventiveMaintenance = PreventiveMaintenance::updateOrCreate(
                        [
                            'machine_id' => $machine->id,
                            'no_document' => $row['document_no'],
                            'type' => $row['type'],
                            'dept' => $row['department'],
                            'shop' => $row['shop'],
                            'effective_date' => $effectiveDate,
                            'mfg_date' => $mfgDate,
                            'revision' => $row['revision'],
                            'no_procedure' => $row['procedure_no'],
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    $this->currentPreventiveMaintenanceId = $preventiveMaintenance->id;
                }

                // Check if we need to create a new checksheet record
                if ($this->currentChecksheetId === null || $row['checksheet_category'] !== $previousChecksheetCategory || $row['checksheet_type'] !== $previousChecksheetType) {
                    // Insert or update checksheet and get the ID directly
                    $checksheet = Checksheet::updateOrCreate(
                        [
                            'preventive_maintenances_id' => $this->currentPreventiveMaintenanceId,
                            'checksheet_category' => $row['checksheet_category'],
                            'checksheet_type' => $row['checksheet_type'],
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    $this->currentChecksheetId = $checksheet->checksheet_id;
                }

                // Insert or update checksheet items
                ChecksheetItem::updateOrCreate(
                    [
                        'checksheet_id' => $this->currentChecksheetId,
                        'item_name' => $row['item_name'],
                    ],
                    [
                        'preventive_maintenances_id' => $this->currentPreventiveMaintenanceId,
                        'spec' => $row['item_spec'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // Set previous values for the next iteration
                $previousMachineId = $machine->id;
                $previousChecksheetCategory = $row['checksheet_category'];
                $previousChecksheetType = $row['checksheet_type'];
            }
        });
    }

    /**
     * Transform Excel date to a Carbon instance.
     *
     * @param mixed $value
     * @return \Carbon\Carbon
     */
    private function transformDate($value)
    {
        // Check if the value is a valid Excel date
        if (is_numeric($value)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }

        // If not, assume the date is in d/m/Y format
        return Carbon::createFromFormat('d/m/Y', $value);
    }
}
