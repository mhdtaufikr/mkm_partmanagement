<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RepairPart;
use App\Models\Part;

class MstPartRepairController extends Controller
{
    public function repairPart()
{
    $items = RepairPart::with('part')->get();
    $parts = Part::all(); // Retrieve all parts for the dropdown

    return view('master.repair', compact('items', 'parts'));
}
public function store(Request $request)
{
    $validatedData = $request->validate([
        'part_id' => 'required|integer',
        'sloc' => 'required|string|max:45',
        'repaired_qty' => 'required|numeric',
        'repair_date' => 'required|date',
        'notes' => 'nullable|string|max:255',
    ]);

    RepairPart::create($validatedData);

    return redirect()->route('repairParts')->with('status', 'Repair part added successfully!');
}

public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'part_id' => 'required|integer',
        'sloc' => 'required|string|max:45',
        'repaired_qty' => 'required|numeric',
        'repair_date' => 'required|date',
        'notes' => 'nullable|string|max:255',
    ]);

    $repairPart = RepairPart::findOrFail($id);
    $repairPart->update($validatedData);

    return redirect()->route('repairParts')->with('status', 'Repair part updated successfully!');
}

public function destroy($id)
{
    $repairPart = RepairPart::findOrFail($id);
    $repairPart->delete();

    return redirect()->route('repairParts')->with('status', 'Repair part deleted successfully!');
}


}
