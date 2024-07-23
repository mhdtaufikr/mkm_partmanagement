<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricalProblem;
use App\Models\Machine;
use App\Models\RepairPart;
use App\Models\HistoricalProblemPart;
use App\Models\MachineSparePartsInventory;
use DB;

class HistoryController extends Controller
{
    public function index()
    {
        $machines = Machine::all();
        $lines = Machine::select('line')->distinct()->get();
        $items = HistoricalProblem::with(['spareParts.part', 'machine'])->get();
        return view('history.index', compact('items', 'machines', 'lines'));
    }

    public function getOpNos($line)
    {
        try {
            \Log::info('Fetching op_no for line: ' . $line);
            $machines = Machine::where('line', $line)->select('id', 'op_no')->get();
            \Log::info('Machines found: ' . $machines->count());
            if ($machines->isEmpty()) {
                return response()->json(['error' => 'No machines found for the specified line'], 404);
            }
            return response()->json($machines);
        } catch (\Exception $e) {
            \Log::error('Error fetching op_no: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
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
    // Get the necessary parameters from the request
    $noMachine = encrypt($request->input('no_machine'));
    $date = $request->input('date');
    $shift = $request->input('shift');

    // Redirect to the specified URL with the parameters
    return redirect()->route('form', ['no_machine' => $noMachine, 'date' => $date, 'shift' => $shift]);

}

public function form($no_machine, $date, $shift)
    {
        $no_machine = decrypt($no_machine);
        $machine_name =

        // Fetch spare parts related to the machine with part details
        $spareParts = MachineSparePartsInventory::where('machine_id', $no_machine)
                        ->with('part')
                        ->get();

        return view('history.form', compact('no_machine', 'date', 'shift', 'spareParts'));
    }

    public function getRepairLocationsForPart($part_id)
{
    try {
        $locations = RepairPart::where('part_id', $part_id)->get(['id', 'sloc', 'repaired_qty']);
        return response()->json($locations);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to retrieve locations', 'details' => $e->getMessage()], 500);
    }
}


public function storehp(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'id_machine' => 'required|integer',
            'date' => 'required|date',
            'shift' => 'required|string',
            'shop' => 'required|string',
            'problem' => 'required|string',
            'cause' => 'required|string',
            'action' => 'required|string',
            'spare_part.*' => 'required|integer',
            'stock_type.*' => 'required|string',
            'repair_location.*' => 'required|integer',
            'used_qty.*' => 'required|integer',
            'start_time' => 'required|date_format:H:i',
            'finish_time' => 'required|date_format:H:i',
            'balance' => 'required|numeric',
            'pic' => 'required|string',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'img' => 'nullable|image|max:2048',
        ]);

        // Handle the image upload if it exists
        $imgPath = null;
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('assets/img/history');
            $file->move($destinationPath, $fileName);
            $imgPath = 'assets/img/history/' . $fileName;
        }

        // Save the main problem data
        $problem = HistoricalProblem::create([
            'id_machine' => $validatedData['id_machine'],
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
            'status' => $validatedData['status'],
            'remarks' => $validatedData['remarks'],
            'img' => $imgPath,
        ]);

        // Save the spare parts data and update the repair parts inventory if stock type is repair
        foreach ($validatedData['spare_part'] as $index => $sparePartId) {
            $repairPartId = $validatedData['repair_location'][$index];
            $repairPart = RepairPart::find($repairPartId);

            $location = $repairPart ? $repairPart->sloc : null;

            HistoricalProblemPart::create([
                'problem_id' => $problem->id,
                'part_id' => $sparePartId,
                'qty' => $validatedData['used_qty'][$index],
                'location' => $location,
                'routes' => $validatedData['stock_type'][$index],
            ]);

            // Update the repair parts inventory only if the stock type is repair
            if ($validatedData['stock_type'][$index] === 'repair' && $repairPart) {
                $repairPart->repaired_qty -= $validatedData['used_qty'][$index];
                $repairPart->save();
            }
        }

        return redirect()->route('history')->with('status', 'Historical problem recorded successfully');
    }





}

