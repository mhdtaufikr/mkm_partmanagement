<?php

namespace App\Imports;

use App\Models\Part;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PartsSAPImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Validate required fields
                if (empty($row['material']) || empty($row['material_description'])) {
                    // Rollback transaction and return with an error message
                    DB::rollBack();
                    throw new \Exception('Material and Material Description are required.');
                }

                // Use updateOrCreate to either update an existing part or create a new one
                Part::updateOrCreate(
                    ['material' => $row['material']],
                    [
                        'material_description' => $row['material_description'],
                        'type' => $row['type'],
                        'plnt' => $row['plnt'],
                        'sloc' => $row['sloc'],
                        'vendor' => $row['vendor'],
                        'bun' => $row['bun'],
                        'begining_qty' => $row['begining_qty'],
                        'begining_value' => $row['begining_value'],
                        'received_qty' => $row['received_qty'],
                        'received_value' => $row['received_value'],
                        'consumed_qty' => $row['consumed_qty'],
                        'consumed_value' => $row['consumed_value'],
                        'total_stock' => $row['total_stock'],
                        'total_value' => $row['total_value'],
                        'currency' => $row['currency'],
                        'updated_at' => now(),
                    ]
                );
            }
        });
    }
}

