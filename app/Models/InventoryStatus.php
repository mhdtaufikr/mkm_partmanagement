<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStatus extends Model
{
    use HasFactory;

    protected $table = 'inventory_status';

    // This model does not have timestamps
    public $timestamps = false;

    // Define the columns that can be mass assigned
    protected $fillable = [
        'id',
        'part_id',
        'material',
        'material_description',
        'sap_stock',
        'repair_stock',
        'safety_stock',
        'total',
        'status'
    ];
}
