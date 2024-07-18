<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricalProblem;
use App\Models\Machine;
use App\Models\RepairPart;
use App\Models\HistoricalProblemPart;
use App\Models\MachineSparePartsInventory;

class HistoryController extends Controller
{
    public function index()
    {
        $machines = Machine::get();
        $items = HistoricalProblem::with(['spareParts.part'])->get();
        return view('history.index', compact('items', 'machines'));
    }



 // HistoryController.php

public function getParts($machineId)
{
    $parts = MachineSparePartsInventory::where('machine_id', $machineId)->get();
    return response()->json($parts);
}

// HistoryController.php
public function getRepairLocations($partId)
{
    try {
        $locations = RepairPart::where('part_id', $partId)->get(['sloc', 'repaired_qty']);
        if ($locations->isEmpty()) {
            return response()->json(['error' => 'No locations found for the given part ID'], 404);
        }
        return response()->json($locations);
    } catch (\Exception $e) {
        \Log::error('Failed to retrieve locations', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to retrieve locations', 'details' => $e->getMessage()], 500);
    }
}





public function store(Request $request)
{

    // Validate the incoming request data
    $validatedData = $request->validate([
        'no_machine' => 'required|string|max:50',
        'date' => 'required|date',
        'shift' => 'required|string|max:10',
        'shop' => 'required|string|max:50',
        'problem' => 'required|string',
        'cause' => 'required|string',
        'action' => 'required|string',
        'start_time' => 'required|date_format:H:i',
        'finish_time' => 'required|date_format:H:i',
        'balance' => 'required|integer',
        'pic' => 'nullable|string|max:100',
        'remarks' => 'nullable|string',
        'status' => 'required|string|max:20',
        'part_no' => 'required|array',
        'part_no.*' => 'required|integer',
        'part_qty' => 'required|array',
        'part_qty.*' => 'required|integer',
        'stock_type' => 'required|array',
        'stock_type.*' => 'required|string|in:sap,repair',
        'repair_location' => 'required_if:stock_type,repair|array',
        'repair_location.*' => 'required_if:stock_type,repair|string',
        'img' => 'nullable|image'
    ]);

    $machine_no = Machine::where('id', $request->no_machine)->first();

    // Handle file upload
    $imgPath = null;
    if ($request->hasFile('img')) {
        $file = $request->file('img');
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('assets/img/history');
        $file->move($destinationPath, $fileName);
        $imgPath = 'assets/img/history/' . $fileName;
    }

    // Store the historical problem
    $historicalProblem = HistoricalProblem::create([
        'no_machine' => $machine_no->op_no,
        'date' => $validatedData['date'],
        'shift' => $validatedData['shift'],
        'shop' => $validatedData['shop'],
        'problem' => $validatedData['problem'],
        'cause' => $validatedData['cause'],
        'action' => $validatedData['action'],
        'start_time' => $validatedData['start_time'],
        'finish_time' => $validatedData['finish_time'],
        'balance' => $validatedData['balance'],
        'pic' => $validatedData['pic'],
        'remarks' => $validatedData['remarks'],
        'img' => $imgPath,
        'status' => $validatedData['status'],
    ]);

    // Store the parts used and update stock
    foreach ($validatedData['part_no'] as $index => $partId) {
        $qty = $validatedData['part_qty'][$index];
        $stockType = $validatedData['stock_type'][$index];
        $repairLocation = $stockType === 'repair' ? $validatedData['repair_location'][$index] : null;

        // Store part used in historical problem
        HistoricalProblemPart::create([
            'problem_id' => $historicalProblem->id,
            'part_id' => $partId,
            'qty' => $qty,
            'location' => $repairLocation,
            'routes' => $stockType,
        ]);

        if ($stockType === 'repair') {
            // Update repair stock in repair_parts table
            $repairPart = RepairPart::where('part_id', $partId)
                                    ->where('sloc', $repairLocation)
                                    ->first();

            if ($repairPart) {
                $repairPart->repaired_qty -= $qty;
                $repairPart->save();
            } else {
                // Handle case where repair part is not found (optional)
                throw new \Exception("Repair part not found for part ID $partId and location $repairLocation");
            }
        }
    }

    return redirect()->back()->with('status', 'Historical problem and parts added successfully!');
}


}

