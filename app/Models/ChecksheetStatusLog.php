<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecksheetStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'historical_id',
        'checksheet_header_id',
        'created_by',
        'change_date',
    ];

    public function historicalProblem()
    {
        return $this->belongsTo(HistoricalProblem::class, 'historical_id');
    }

    public function checksheetFormHead()
    {
        return $this->belongsTo(ChecksheetFormHead::class, 'checksheet_header_id');
    }
}
