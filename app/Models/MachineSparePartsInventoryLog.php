<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineSparePartsInventoryLog extends Model
{
    use HasFactory;

    // Specify the table name if it's not pluralized or different from the class name
    protected $table = 'machine_spare_parts_inventories_logs';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'inventory_id',
        'old_last_replace',
        'new_last_replace',
        'old_sap_stock',
        'new_sap_stock',
        'old_repair_stock',
        'new_repair_stock',
        'qty',
        'updated_at',
    ];

    /**
     * Relationship with MachineSparePartsInventory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function machineSparePartsInventory()
    {
        return $this->belongsTo(MachineSparePartsInventory::class, 'inventory_id');
    }
}
