<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairPart extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'repair_parts'; // Ensure this matches your table name

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id', 'id');
    }
}

