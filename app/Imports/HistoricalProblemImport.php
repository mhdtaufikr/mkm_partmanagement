<?php
namespace App\Imports;

use App\Models\HistoricalProblem;
use App\Models\Machine;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoricalProblemImport implements ToCollection, WithHeadingRow
{
    protected $errorLog;  // Store error logs

    public function __construct(&$errorLog)
    {
        $this->errorLog = &$errorLog;  // Pass the error log by reference
    }

    public function collection(Collection $rows)
    {
        // Start database transaction
        DB::beginTransaction();

        try {
            foreach ($rows as $rowIndex => $row) {
                // Skip the row if any required column is missing
                if (empty($row['plant']) || empty($row['line']) || empty($row['op_no'])) {
                    $this->errorLog[] = "Row {$rowIndex}: Missing plant, line, or OP No.";
                    continue;
                }

                // Query the machine ID using plant, line, and op_no
                $machine = Machine::where('plant', $row['plant'])
                    ->where('line', $row['line'])
                    ->where('op_no', $row['op_no'])
                    ->first();

                // If machine is not found, log the error and continue to the next row
                if (!$machine) {
                    $this->errorLog[] = "Row {$rowIndex}: Machine not found for plant '{$row['plant']}', line '{$row['line']}', and op_no '{$row['op_no']}'";
                    continue;
                }

                // Validate the date format
                try {
                    $date = is_numeric($row['date'])
                        ? Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d'))
                        : Carbon::parse($row['date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $this->errorLog[] = "Row {$rowIndex}: Invalid date format for date '{$row['date']}'";
                    continue;
                }

                // Validate time formats for start_time and finish_time
                if (!preg_match('/^\s*\d{1,2}:\d{2}\s*$/', $row['start_time']) || !preg_match('/^\s*\d{1,2}:\d{2}\s*$/', $row['finish_time'])) {
                    $this->errorLog[] = "Row {$rowIndex}: Invalid time format for start_time or finish_time";
                    continue;
                }

                // Insert historical problem into the database
                HistoricalProblem::create([
                    'id_machine' => $machine->id,
                    'parent_id' => null,  // Adjust this if needed
                    'report' => $row['report'],
                    'date' => $date,
                    'shift' => $row['shift'],
                    'shop' => $row['shop'],
                    'problem' => $row['problem'],
                    'cause' => $row['cause'],
                    'action' => $row['action'],
                    'start_time' => $row['start_time'],
                    'finish_time' => $row['finish_time'],
                    'category' => $row['category'],
                    'balance' => $row['balance'],
                    'pic' => $row['pic'],
                    'remarks' => $row['remarks'],
                    'status' => $row['status'],
                ]);
            }

            // If there are errors, rollback the transaction
            if (!empty($this->errorLog)) {
                DB::rollBack();
            } else {
                DB::commit();  // Commit the transaction only if no errors
            }

        } catch (\Exception $e) {
            // Rollback and store the error in the errorLog
            DB::rollBack();
            $this->errorLog[] = 'Import failed due to an error: ' . $e->getMessage();
        }
    }
}
