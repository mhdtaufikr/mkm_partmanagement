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
        'machine_id',  // Added machine_id to fillable
        'type',        // Added type to fillable
        'created_at',
        'updated_at',
    ];

    /**
     * Get the machine associated with the PM schedule.
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');  // Specify the foreign key
    }

    /**
     * Get the preventive maintenance associated with the PM schedule.
     */
    public function preventiveMaintenance()
    {
        return $this->belongsTo(PreventiveMaintenance::class, 'pm_id');  // Ensure 'pm_id' is the correct foreign key
    }

    /**
     * Get the schedule details associated with the PM schedule.
     */
    public function details()
    {
        return $this->hasMany(PmScheduleDetail::class);
    }
}
