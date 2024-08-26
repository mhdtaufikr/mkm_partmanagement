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
        set_time_limit(300);
        $errors = []; // Array untuk menyimpan pesan kesalahan

        // Memulai transaksi database
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                // Periksa apakah kombinasi op_no, plant, dan line ada di tabel mesin
                $machine = Machine::where('op_no', $row['op_no_machine_no'])
                    ->where('plant', $row['plant'])
                    ->where('line', $row['line'])
                    ->first();

                if (!$machine) {
                    // Tambahkan pesan kesalahan ke array errors
                    $errors[] = 'Operation number ' . $row['op_no_machine_no'] . ' does not exist in the given plant and line.';
                    continue; // Lanjutkan ke baris berikutnya
                }

                // Periksa apakah bagian ada di tabel parts
                $part = Part::where('material', $row['material_no'])
                    ->first();

                if (!$part) {
                    // Tambahkan pesan kesalahan ke array errors
                    $errors[] = 'Part ' . $row['material_no'] . ' does not exist in the master parts table.';
                    continue; // Lanjutkan ke baris berikutnya
                }

                // Query tabel repair_parts untuk menjumlahkan repaired_qty
                $repair_qty = RepairPart::where('part_id', $part->id)->sum('repaired_qty');

                // Tentukan safety stock dan estimasi lifetime dengan nilai default jika kosong
                $safety_stock = $row['safety_stock'] ?? 0;
                $estimation_lifetime = $row['estimation_lifetime'] ?? 0;

                // Masukkan data inventory spare part mesin baru
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

            // Jika ada error, throw exception untuk rollback
            if (!empty($errors)) {
                throw new \Exception('Errors encountered during import: ' . implode(', ', $errors));
            }

            // Commit transaksi jika tidak ada error
            DB::commit();

        } catch (\Exception $e) {
            // Rollback transaksi jika ada exception
            DB::rollBack();

            // Throw exception dengan pesan error gabungan
            throw new \Exception('Import failed with errors: ' . implode(', ', $errors));
        }
    }

    /**
     * Transformasi tanggal Excel ke instance Carbon atau format sesuai kebutuhan.
     *
     * @param mixed $value
     * @return string|null
     */
    private function transformDate($value)
    {
        if (!$value) {
            return null;
        }

        // Periksa apakah nilai adalah tanggal Excel yang valid
        if (is_numeric($value)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
        }

        // Jika tidak, asumsikan tanggal dalam format d/m/Y
        return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
}
