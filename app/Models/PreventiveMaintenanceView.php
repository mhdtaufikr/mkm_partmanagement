<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreventiveMaintenanceView extends Model
{
    // Specify the name of the view
    protected $table = 'preventive_maintenance_view';

    // Indicate that the view doesn't have timestamps
    public $timestamps = false;

    // Define fillable columns
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

    // Define the relationship to the Machine model
    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id', 'id');
    }
}
