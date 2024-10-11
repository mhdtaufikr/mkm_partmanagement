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
        // Start a transaction
        DB::beginTransaction();

        $errorMessages = []; // Array to collect error messages

        try {
            foreach ($rows as $row) {
                try {
                    // Log the row data for debugging purposes
                    Log::info('Processing row: ', $row->toArray());

                    // Skip the row if essential fields are null
                    if (empty($row['no_op_no']) || empty($row['plant']) || empty($row['line'])) {
                        Log::warning('Skipping row due to missing essential data: ', $row->toArray());
                        continue; // Skip this row
                    }

                    // Handle dates from the row
                    $effectiveDate = $this->transformDate($row['effective_date']);
                    $mfgDate = $this->transformDate($row['manufacture_date']);

                    // Check if the op_no exists in the machine table on the specified plant and line
                    $machine = Machine::where('op_no', $row['no_op_no'])
                        ->where('plant', $row['plant'])
                        ->where('line', $row['line'])
                        ->first();

                    if (!$machine) {
                        throw new \Exception('Operation number '.$row['no_op_no'].' does not exist in the machine table for the given plant and line.');
                    }

                    // Preventive Maintenance record creation
                    if ($this->currentPreventiveMaintenanceId === null || $machine->id !== $previousMachineId) {
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

                    // Checksheet record creation
                    if ($this->currentChecksheetId === null || $row['checksheet_category'] !== $previousChecksheetCategory || $row['checksheet_type'] !== $previousChecksheetType) {
                        $checksheet = Checksheet::updateOrCreate(
                            [
                                'preventive_maintenances_id' => $this->currentPreventiveMaintenanceId,
                                'checksheet_category' => $row['checksheet_type'],
                                'checksheet_type' => $row['checksheet_category'],
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
                    $previousChecksheetCategory = $row['checksheet_type'];
                    $previousChecksheetType = $row['checksheet_category'];
                } catch (\Exception $e) {
                    // Collect the error message but continue processing the next row
                    $errorMessages[] = $e->getMessage();
                    Log::error('Error in row: '.$e->getMessage());
                }
            }

            // Check if there were any errors
            if (!empty($errorMessages)) {
                // Rollback the transaction and log all errors
                DB::rollBack();
                throw new \Exception('Import failed with errors: '.implode("\n", $errorMessages));
            }

            // Commit the transaction if no errors
            DB::commit();
        } catch (\Exception $e) {
            // In case of any exception, rollback the transaction
            DB::rollBack();
            Log::error('Transaction failed: '.$e->getMessage());
            throw $e;
        }
    }



    /**
     * Transform Excel date to a Carbon instance.
     *
     * @param mixed $value
     * @return \Carbon\Carbon
     */
    private function transformDate($value)
{
    // If the value is null, empty, or invalid like '-', return null
    if (empty($value) || $value === '-') {
        \Log::info('Empty or invalid date value, returning null.');
        return null;
    }

    // Check if the value is numeric (Excel date)
    if (is_numeric($value)) {
        \Log::info('Transforming Excel date: ' . $value);
        return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
    }

    // Check if the value is a four-digit year
    if (preg_match('/^\d{4}$/', $value)) {
        // If it's only a year, append '-01-01' to make it a full date
        \Log::info('Year detected, converting to full date: ' . $value);
        return Carbon::createFromFormat('Y-m-d', $value . '-01-01');
    }

    // Map Indonesian month names to English
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

    // Replace Indonesian month names with English ones
    foreach ($indonesianMonths as $indoMonth => $engMonth) {
        if (strpos($value, $indoMonth) !== false) {
            $value = str_replace($indoMonth, $engMonth, $value);
            break;
        }
    }

    // Try different date formats
    $formats = ['d F Y', 'd/m/Y', 'Y-m-d']; // Add as many formats as needed

    foreach ($formats as $format) {
        try {
            \Log::info('Attempting to parse date with format ' . $format . ': ' . $value);
            return Carbon::createFromFormat($format, $value);
        } catch (\Exception $e) {
            \Log::warning('Date format mismatch for format ' . $format . ': ' . $e->getMessage());
        }
    }

    // Log an error if none of the formats worked and throw an exception
    \Log::error('Unable to parse date: ' . $value);
    throw new \Exception('Date format is invalid: ' . $value);
}



}

