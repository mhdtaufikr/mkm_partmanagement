<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineSparePartsInventory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function repairs()
    {
        return $this->hasMany(RepairPart::class, 'part_id', 'part_id');
    }
}
