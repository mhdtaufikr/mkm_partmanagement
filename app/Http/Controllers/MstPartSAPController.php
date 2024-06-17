<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Part;
use App\Models\RepairPart;

class MstPartSAPController extends Controller
{
    public function sapPart(){
        $item = Part::get();
        return view('master.sap',compact('item'));
    }

    public function repairPart(){
        $items = RepairPart::with('part')->get();

        return view('master.repair', compact('items'));
    }
}
