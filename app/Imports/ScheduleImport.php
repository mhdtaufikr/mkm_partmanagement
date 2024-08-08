<?php

namespace App\Imports;

use App\Models\PreventiveMaintenance;
use App\Models\Machine;
use App\Models\PmScheduleMaster;
use App\Models\PmScheduleDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ScheduleImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Get the machine by op_no
            $machine = Machine::where('op_no', $row['op_no'])->first();

            if (!$machine) {
                throw new \Exception('Machine not found: ' . $row['op_no']);
            }

            // Get the preventive maintenance by machine_id and type
            $preventiveMaintenance = PreventiveMaintenance::where('machine_id', $machine->id)
                ->where('type', $row['pm_type'])
                ->first();

            if (!$preventiveMaintenance) {
                throw new \Exception('Preventive Maintenance not found for machine: ' . $row['op_no'] . ' and type: ' . $row['pm_type']);
            }

            // Check if a pm_schedule_master with the same pm_id and frequency already exists
            $pmScheduleMaster = PmScheduleMaster::where('pm_id', $preventiveMaintenance->id)
                ->where('frequency', $row['frequency'])
                ->first();

            if (!$pmScheduleMaster) {
                // Insert into pm_schedule_masters if it does not exist
                $pmScheduleMaster = PmScheduleMaster::create([
                    'pm_id' => $preventiveMaintenance->id,
                    'frequency' => $row['frequency'],
                ]);
            }

            // Map month names to numbers
            $months = [
                'januari' => 1,
                'februari' => 2,
                'march' => 3,
                'april' => 4,
                'may' => 5,
                'june' => 6,
                'july' => 7,
                'agustus' => 8,
                'september' => 9,
                'oktober' => 10,
                'november' => 11,
                'december' => 12,
            ];

            // Insert into pm_schedule_details for each month with a value
            foreach ($months as $monthName => $monthNumber) {
                if (!empty($row[$monthName])) {
                    $annualDate = \DateTime::createFromFormat('Y-m-d', date('Y') . '-' . $monthNumber . '-12'); // Set to 12th day
                    $existingDetail = PmScheduleDetail::where('pm_schedule_master_id', $pmScheduleMaster->id)
                        ->where('annual_date', $annualDate)
                        ->first();

                    if (!$existingDetail) {
                        PmScheduleDetail::create([
                            'pm_schedule_master_id' => $pmScheduleMaster->id,
                            'annual_date' => $annualDate,
                            'status' => 'planned',
                        ]);
                    }
                }
            }
        }
    }
}
