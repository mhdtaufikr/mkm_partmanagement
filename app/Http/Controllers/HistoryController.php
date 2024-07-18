<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricalProblem;
use App\Models\Machine;
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



    public function getParts($machineId)
    {
        $parts = MachineSparePartsInventory::where('machine_id', $machineId)->get();
        return response()->json($parts);
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
            'img' => 'nullable|image'
        ]);

        $machine_no = Machine::where('id',$request->no_machine)->first();
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


            // Store part used in historical problem
            HistoricalProblemPart::create([
                'problem_id' => $historicalProblem->id,
                'part_id' => $partId,
                'qty' => $qty,
                'routes' => $stockType,
            ]);

            // Update stock based on stock type
            $inventory = MachineSparePartsInventory::where('part_id', $partId)->where('machine_id', $validatedData['no_machine'])->first();
            if ($stockType === 'repair') {
                $inventory->repair_stock -= $qty;
            }
            $inventory->save();
        }

        return redirect()->back()->with('status', 'Historical problem and parts added successfully!');
    }
}

