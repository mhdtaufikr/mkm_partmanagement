<?php

namespace App\Imports;

use App\Models\PreventiveMaintenance;
use App\Models\Machine;
use App\Models\PmScheduleMaster;
use App\Models\PmScheduleDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class ScheduleImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
{
    // Initialize an array to collect errors
    $errors = [];

    foreach ($rows as $row) {
        try {
            // Extract data from the row
            $type = $row['type'];
            $plant = $row['plant'];
            $op_no = $row['op_no'];
            $frequency = (int) $row['frequency']; // Frequency can now be any value from 1 to 12
            $startMonth = (int) $row['month']; // Start month from the row
            $date = (int) $row['day']; // Day of the month for scheduling

            // Validate data
            if (empty($type) || empty($plant) || empty($op_no) || $frequency < 1 || $frequency > 12 || $startMonth < 1 || $startMonth > 12 || $date < 1 || $date > 31) {
                $errors[] = "Invalid data in row: " . json_encode($row); // Log error
                continue; // Skip invalid rows
            }

            // Find the machine using plant and op_no
            $machine = Machine::where('plant', $plant)->where('op_no', $op_no)->first();

            if (!$machine) {
                $errors[] = "Machine not found for plant: {$plant} and OP No: {$op_no}";
                continue; // Skip if no machine found
            }

            $machine_id = $machine->id;

            // Find the preventive maintenance ID based on type and machine
            $preventiveMaintenance = PreventiveMaintenance::where('type', $type)
                ->where('machine_id', $machine_id)
                ->first();

            $pm_id = $preventiveMaintenance->id ?? null;

            // Create PmScheduleMaster record
            $pmScheduleMaster = PmScheduleMaster::create([
                'pm_id' => $pm_id,
                'machine_id' => $machine_id,
                'type' => $type,
                'frequency' => $frequency,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Calculate schedule dates starting from the provided month and cutting off in December
            $year = now()->year;
            $currentMonth = $startMonth; // Start from the provided month
            $scheduleDetails = [];

            // Generate schedule dates until December
            while ($currentMonth <= 12) {
                // Create the date for each schedule
                $annual_date = Carbon::createFromDate($year, $currentMonth, $date)->toDateString();

                $scheduleDetails[] = [
                    'pm_schedule_master_id' => $pmScheduleMaster->id,
                    'annual_date' => $annual_date,
                    'status' => 'Scheduled',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Increment the current month by the frequency and stop if it exceeds December
                $currentMonth += $frequency;

                // Ensure that the month doesn't go beyond December (cutoff)
                if ($currentMonth > 12) {
                    break;
                }
            }

            // Insert PmScheduleDetail records
            PmScheduleDetail::insert($scheduleDetails);

        } catch (Exception $e) {
            $errors[] = "Error in row: " . $e->getMessage(); // Log the error with row information
        }
    }

    // Throw an exception if there are any errors to trigger rollback in the controller
    if (!empty($errors)) {
        throw new Exception(implode(", ", $errors));
    }
}

}
