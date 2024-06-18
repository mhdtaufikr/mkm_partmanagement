<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RepairPart;

class MstPartRepairController extends Controller
{
    public function repairPart(){
        $items = RepairPart::with('part')->get();

        return view('master.repair', compact('items'));
    }
}
