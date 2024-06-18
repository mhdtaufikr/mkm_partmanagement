<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;

class MstMachinePartController extends Controller
{
    public function index(){
        $items = Machine::get();
       return view('master.machine',compact('items'));
    }

    public function detail($id){
        $id = decrypt($id);
        $machine = Machine::with('spareParts')->where('id', $id)->first();
        return view('master.dtl_machine',compact('machine'));
    }
}
