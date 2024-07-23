<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function spareParts()
    {
        return $this->hasMany(MachineSparePartsInventory::class, 'machine_id');
    }

    public function inventoryStatus()
    {
        return $this->hasManyThrough(
            InventoryStatus::class,
            MachineSparePartsInventory::class,
            'machine_id', // Foreign key on MachineSparePartsInventory table
            'part_id',    // Foreign key on InventoryStatus table
            'id',         // Local key on Machine table
            'part_id'     // Local key on MachineSparePartsInventory table
        );
    }

    public function historicalProblems()
    {
        return $this->hasMany(HistoricalProblem::class, 'id_machine');
    }
}
