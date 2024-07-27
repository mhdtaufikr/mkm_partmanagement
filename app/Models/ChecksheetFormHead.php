<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecksheetFormHead extends Model
{
    use HasFactory;

    protected $guarded=[
        'id'
    ];

    public function preventiveMaintenance()
    {
        return $this->belongsTo(PreventiveMaintenanceView::class, 'preventive_maintenances_id', 'id');
    }
}
