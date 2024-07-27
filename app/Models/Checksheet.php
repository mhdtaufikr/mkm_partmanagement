<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checksheet extends Model
{
    use HasFactory;

    protected $primaryKey = 'checksheet_id'; // Specify the primary key

    protected $guarded = ['checksheet_id']; // Guard the primary key
}


