<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalProblem extends Model
{
    protected $guarded = ['id'];

    public function partsUsed()
    {
        return $this->hasMany(HistoricalProblemPart::class, 'historical_problem_id');
    }

    public function spareParts()
    {
        return $this->hasMany(HistoricalProblemPart::class, 'problem_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'id_machine');
    }
    public function children()
{
    return $this->hasMany(HistoricalProblem::class, 'parent_id');
}

}
