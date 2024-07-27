<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreventiveMaintenance extends Model
{
    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'preventive_maintenances';

    // Menentukan kolom yang dapat diisi
    protected $fillable = [
        'machine_id',
        'no_document',
        'type',
        'dept',
        'shop',
        'effective_date',
        'mfg_date',
        'revision',
        'no_procedure',
        'created_at',
        'updated_at'
    ];

    // Menentukan apakah model ini memiliki kolom timestamps
    public $timestamps = true;
}
