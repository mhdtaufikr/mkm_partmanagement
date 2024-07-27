<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreventiveMaintenanceView extends Model
{
    // Menentukan nama tabel view
    protected $table = 'preventive_maintenance_view';

    // Menentukan bahwa tabel ini tidak memiliki kolom timestamps
    public $timestamps = false;

    // Menentukan kolom-kolom yang dapat diisi
    protected $fillable = [
        'id',
        'machine_no',
        'op_name',
        'machine_name',
        'no_document',
        'type',
        'dept',
        'shop',
        'effective_date',
        'mfg_date',
        'process',
        'revision',
        'no_procedure',
        'plant',
        'location',
        'line',
        'created_at',
        'updated_at'
    ];
}
