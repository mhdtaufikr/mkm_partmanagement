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
use Illuminate\Support\Facades\Log;

class ChecksheetImport implements ToCollection, WithHeadingRow
{
    private $currentPreventiveMaintenanceId = null;
    private $currentChecksheetId = null;

    public function collection(Collection $rows)
    {
        set_time_limit(300);
        DB::beginTransaction(); // Start a transaction

        $errorMessages = [];

        try {
            foreach ($rows as $row) {
                try {
                    Log::info('Processing row: ', $row->toArray());

                    // Skip the row if essential fields are missing
                    if (empty($row['no_op_no']) || empty($row['plant']) || empty($row['line'])) {
                        Log::warning('Skipping row due to missing essential data: ', $row->toArray());
                        continue;
                    }

                    // Transform dates
                    $effectiveDate = $this->transformDate($row['effective_date']);
                    $mfgDate = $this->transformDate($row['manufacture_date']);

                    // Check if the machine exists
                    $machine = Machine::where('op_no', $row['no_op_no'])
                        ->where('plant', $row['plant'])
                        ->where('line', $row['line'])
                        ->first();

                    if (!$machine) {
                        throw new \Exception('Operation number '.$row['no_op_no'].' does not exist in the machine table for the given plant and line.');
                    }

                    // Preventive Maintenance record creation or update
                    $preventiveMaintenance = PreventiveMaintenance::updateOrCreate(
                        [
                            'machine_id' => $machine->id,
                            'type' => $row['type'], // Ensure unique type for the machine
                        ],
                        [
                            'no_document' => $row['document_no'],
                            'dept' => $row['department'],
                            'shop' => $row['shop'],
                            'effective_date' => $effectiveDate,
                            'mfg_date' => $mfgDate,
                            'revision' => $row['revision'],
                            'no_procedure' => $row['procedure_no'],
                            'updated_at' => now(),
                        ]
                    );

                    $this->currentPreventiveMaintenanceId = $preventiveMaintenance->id;

                    // Update or create Checksheet record
                    $checksheet = Checksheet::updateOrCreate(
                        [
                            'preventive_maintenances_id' => $this->currentPreventiveMaintenanceId,
                            'checksheet_category' => $row['checksheet_type'],
                            'checksheet_type' => $row['checksheet_category'],
                        ],
                        [
                            'updated_at' => now(),
                        ]
                    );

                    $this->currentChecksheetId = $checksheet->checksheet_id;

                    // Update or create Checksheet Items
                    ChecksheetItem::updateOrCreate(
                        [
                            'checksheet_id' => $this->currentChecksheetId,
                            'item_name' => $row['item_name'],
                        ],
                        [
                            'preventive_maintenances_id' => $this->currentPreventiveMaintenanceId,
                            'spec' => $row['item_spec'],
                            'updated_at' => now(),
                        ]
                    );

                } catch (\Exception $e) {
                    $errorMessages[] = $e->getMessage();
                    Log::error('Error in row: '.$e->getMessage());
                }
            }

            // If errors occurred, rollback the transaction
            if (!empty($errorMessages)) {
                DB::rollBack();
                throw new \Exception('Import failed with errors: '.implode("\n", $errorMessages));
            }

            DB::commit(); // Commit the transaction if successful
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of an error
            Log::error('Transaction failed: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Transform Excel date to a Carbon instance.
     *
     * @param mixed $value
     * @return \Carbon\Carbon|null
     */
    private function transformDate($value)
    {
        if (empty($value) || $value === '-') {
            Log::info('Empty or invalid date value, returning null.');
            return null;
        }

        if (is_numeric($value)) {
            Log::info('Transforming Excel date: ' . $value);
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }

        if (preg_match('/^\d{4}$/', $value)) {
            Log::info('Year detected, converting to full date: ' . $value);
            return Carbon::createFromFormat('Y-m-d', $value . '-01-01');
        }

        $indonesianMonths = [
            'Januari' => 'January',
            'Februari' => 'February',
            'Maret' => 'March',
            'April' => 'April',
            'Mei' => 'May',
            'Juni' => 'June',
            'Juli' => 'July',
            'Agustus' => 'August',
            'September' => 'September',
            'Oktober' => 'October',
            'November' => 'November',
            'Desember' => 'December',
        ];

        foreach ($indonesianMonths as $indoMonth => $engMonth) {
            if (strpos($value, $indoMonth) !== false) {
                $value = str_replace($indoMonth, $engMonth, $value);
                break;
            }
        }

        $formats = ['d F Y', 'd/m/Y', 'Y-m-d'];

        foreach ($formats as $format) {
            try {
                Log::info('Attempting to parse date with format ' . $format . ': ' . $value);
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {
                Log::warning('Date format mismatch for format ' . $format . ': ' . $e->getMessage());
            }
        }

        Log::error('Unable to parse date: ' . $value);
        throw new \Exception('Date format is invalid: ' . $value);
    }
}
