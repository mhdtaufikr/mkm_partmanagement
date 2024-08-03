<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreventiveMaintenance extends Model
{
    use HasFactory;

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
        'updated_at',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
