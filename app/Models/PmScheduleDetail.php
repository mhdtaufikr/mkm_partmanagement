<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmScheduleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pm_schedule_master_id',
        'annual_date',
        'status',
        'actual_date',
        'checksheet_form_heads_id',
        'created_at',
        'updated_at',
    ];

    public function master()
    {
        return $this->belongsTo(PmScheduleMaster::class, 'pm_schedule_master_id');
    }

    public function checksheetFormHead()
    {
        return $this->belongsTo(ChecksheetFormHead::class, 'checksheet_form_heads_id');
    }
}

