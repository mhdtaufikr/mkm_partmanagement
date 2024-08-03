<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmFilterView extends Model
{
    // Specify the table name, since it doesn't follow the default Laravel naming convention
    protected $table = 'pm_filter_view';

    // Since this is a view, we need to indicate that the model doesn't have timestamps
    public $timestamps = false;

    // Specify the columns that can be mass assignable (if needed)
    protected $fillable = [
        'type',
        'plant',
        'shop',
        'op_no',
        'machine_name',
    ];
}
