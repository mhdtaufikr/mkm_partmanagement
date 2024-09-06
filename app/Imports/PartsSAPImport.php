<?php

namespace App\Imports;

use App\Models\Part;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PartsSAPImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $errorLog;

    public function __construct(&$errorLog)
    {
        $this->errorLog = &$errorLog; // Pass by reference to retain errors in controller
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowNumber => $row) {
            try {
                // Validate required fields
                if (empty($row['material']) || empty($row['description'])) {
                    throw new \Exception('Material and Description are required.');
                }

                // Use updateOrCreate to either update an existing part or create a new one
                Part::updateOrCreate(
                    ['material' => $row['material']], // Check if the material already exists
                    [
                        'material_description' => $row['description'],
                        'plnt' => $row['plnt'] ?? 'default_plant',
                        'sloc' => $row['sloc'] ?? 'default_sloc',
                        'total_stock' => $row['unrestr'] ?? 0,
                        'total_value' => $row['total_value'] ?? 0,
                        'type' => $row['type'] ?? 'default_type',
                        'vendor' => $row['vendor'] ?? 'default_vendor',
                        'bun' => $row['bun'] ?? 'default_bun',
                        'begining_qty' => $row['begining_qty'] ?? 0,
                        'begining_value' => $row['begining_value'] ?? 0,
                        'received_qty' => $row['received_qty'] ?? 0,
                        'received_value' => $row['received_value'] ?? 0,
                        'consumed_qty' => $row['consumed_qty'] ?? 0,
                        'consumed_value' => $row['consumed_value'] ?? 0,
                        'currency' => $row['currency'] ?? 'default_currency',
                        'updated_at' => now(),
                    ]
                );
            } catch (\Exception $e) {
                // Log error with row number, column, and error message
                $this->errorLog[] = "Row $rowNumber: " . $e->getMessage();
            }
        }
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
