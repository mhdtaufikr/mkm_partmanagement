<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\RepairPart;

class MstMachinePartController extends Controller
{
    public function index(){
        $items = Machine::get();
       return view('master.machine',compact('items'));
    }

    public function detail($id){
        $id = decrypt($id);
        $machine = Machine::with(['inventoryStatus', 'spareParts', 'spareParts.repairs'])->where('id', $id)->first();
        return view('master.dtl_machine', compact('machine'));
    }


    public function repair(Request $request)
    {
        $id_machine = $request->encrypted_id;

        // Validate the request data
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:parts,id',
            'qty' => 'required|numeric',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'remark' => 'nullable|string|max:255',
        ]);



        // Create a new record in the repair_parts table
        $repairPart = new RepairPart();
        $repairPart->part_id = $validatedData['id'];
        $repairPart->repaired_qty = $validatedData['qty'];
        $repairPart->repair_date = $validatedData['date'];
        $repairPart->sloc = $validatedData['location'];
        $repairPart->notes = $validatedData['remark'];
        $repairPart->save();

        // Redirect back to the machine detail page with the encrypted ID
        return redirect()->back()->with('status', 'Repair part record created successfully');
    }
}
