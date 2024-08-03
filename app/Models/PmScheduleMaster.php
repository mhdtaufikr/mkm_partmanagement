<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmScheduleMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'pm_id',
        'frequency',
        'created_at',
        'updated_at',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function preventiveMaintenance()
    {
        return $this->belongsTo(PreventiveMaintenance::class, 'pm_id');
    }
    public function details()
    {
        return $this->hasMany(PmScheduleDetail::class);
    }
}
