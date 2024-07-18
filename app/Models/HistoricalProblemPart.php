<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalProblemPart extends Model
{
    protected $guarded = ['id'];

    public function historicalProblem()
    {
        return $this->belongsTo(HistoricalProblem::class, 'problem_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }
}

