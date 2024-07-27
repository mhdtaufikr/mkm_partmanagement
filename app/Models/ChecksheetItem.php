<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecksheetItem extends Model
{
    protected $table = 'checksheet_items';

    // Menentukan primary key yang benar
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'preventive_maintenances_id',
        'checksheet_id',
        'item_name',
        'spec',
        'created_at',
        'updated_at'
    ];
}
