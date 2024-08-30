<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricalProblem;
use App\Models\Machine;
use App\Models\Dropdown;
use App\Models\RepairPart;
use App\Models\Part;
use App\Models\MachineSparePartsInventoryLog;
use App\Models\PreventiveMaintenance;
use App\Models\ChecksheetFormHead;
use App\Models\HistoricalProblemPart;
use App\Models\ChecksheetStatusLog;
use App\Models\MachineSparePartsInventory;
use DB;
use Yajra\DataTables\DataTables;

class HistoryController extends Controller
{
    public function index(Request $request)
{
    // Dapatkan plant dan type dari pengguna yang sedang login
    $userPlant = auth()->user()->plant;
    $userType = auth()->user()->type;

    if ($request->ajax()) {
        $query = HistoricalProblem::with(['spareParts.part', 'machine'])
            ->orderBy('date', 'desc')       // Urutkan berdasarkan tanggal secara menurun
            ->orderBy('start_time', 'desc'); // Lalu urutkan berdasarkan waktu mulai secara menurun

        // Terapkan filter berdasarkan plant dan type pengguna
        if (($userPlant === 'Engine' || $userPlant === 'Stamping') && ($userType === 'Mechanic' || $userType === 'Electric')) {
            $query->whereHas('machine', function($q) use ($userPlant) {
                $q->where('plant', $userPlant)->where('shop', 'ME');
            });
        } elseif (($userPlant === 'Engine' || $userPlant === 'Stamping') && $userType === 'Power House') {
            $query->whereHas('machine', function($q) use ($userPlant) {
                $q->where('plant', $userPlant)->where('shop', 'PH');
            });
        }
        // Jika plant pengguna adalah 'All' atau tipe tidak sesuai dengan kasus khusus, tidak ada filter tambahan yang diterapkan

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('start_time', function ($row) {
                return \Carbon\Carbon::parse($row->start_time)->format('H:i');
            })
            ->editColumn('finish_time', function ($row) {
                return \Carbon\Carbon::parse($row->finish_time)->format('H:i');
            })
            ->addColumn('action', function($row){
                return '<button class="btn btn-sm btn-primary btn-detail" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#modal-detail">Detail</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Terapkan filter untuk kueri machines dan lines berdasarkan plant dan type pengguna
    $machinesQuery = Machine::query();
    $linesQuery = Machine::select('line')->distinct();

    if (($userPlant === 'Engine' || $userPlant === 'Stamping') && ($userType === 'Mechanic' || $userType === 'Electric')) {
        $machinesQuery->where('plant', $userPlant)->where('shop', 'ME');
        $linesQuery->where('plant', $userPlant)->where('shop', 'ME');
    } elseif (($userPlant === 'Engine' || $userPlant === 'Stamping') && $userType === 'Power House') {
        $machinesQuery->where('plant', $userPlant)->where('shop', 'PH');
        $linesQuery->where('plant', $userPlant)->where('shop', 'PH');
    }
    // Jika plant pengguna adalah 'All', tidak ada filter yang diterapkan

    $machines = $machinesQuery->get();
    $lines = $linesQuery->get();

    return view('history.index', compact('machines', 'lines'));
}



    // HistoryController.php
        public function showDetail($id)
        {
            $data = HistoricalProblem::with(['spareParts.part', 'machine'])->findOrFail($id);

            // Assuming you have a Blade partial for rendering the modal content
            return view('history.partials', compact('data'));
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

    $dropdown = Dropdown::where('category','Problem')->get();

    // Fetch all parts instead of just those related to MachineSparePartsInventory
    $spareParts = Part::all(); // Fetch all parts directly from the 'parts' table

    return view('history.form', compact('no_machine', 'date', 'shift', 'spareParts','dropdown'));
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
        'report' => 'required|string',
        'shop' => 'required|string',
        'problem' => 'required|string',
        'cause' => 'required|string',
        'action' => 'required|string',
        'spare_part.*' => 'nullable|integer',
        'stock_type.*' => 'nullable|string',
        'used_qty.*' => 'nullable|integer',
        'start_time' => 'required|date_format:H:i',
        'finish_time' => 'required|date_format:H:i',
        'balance' => 'required|numeric',
        'pic' => 'required|string',
        'status' => 'required|string',
        'remarks' => 'nullable|string',
        'img' => 'nullable|image|max:2048',
        'checksheet_head_id' => 'nullable|integer',
        'category' => 'required|string', // Add validation for category
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
        'category' => $validatedData['category'], // Save the category
        'report' => $validatedData['report'], // Save the report
    ]);

    // Save the spare parts data and update the repair parts inventory if stock type is repair
    foreach ($validatedData['spare_part'] as $index => $sparePartId) {
        // Skip this iteration if the part ID is null
        if (is_null($sparePartId)) {
            continue;
        }

        $stockType = $validatedData['stock_type'][$index];
        $usedQty = $validatedData['used_qty'][$index];
        $location = null;

        // Check if part exists for the specific machine in machine_spare_parts_inventories
        $machinePart = MachineSparePartsInventory::where('part_id', $sparePartId)
            ->where('machine_id', $validatedData['id_machine'])
            ->first();

        if (!$machinePart) {
            // Part does not exist for this machine, create it with default values
            $repairStock = RepairPart::where('part_id', $sparePartId)->sum('repaired_qty');
            $sapStock = Part::where('id', $sparePartId)->value('begining_qty');

            $machinePart = MachineSparePartsInventory::create([
                'part_id' => $sparePartId,
                'machine_id' => $validatedData['id_machine'],
                'critical_part' => 'Default', // Adjust as necessary
                'type' => 'Default', // Adjust as necessary
                'estimation_lifetime' => 5,
                'cost' => 0.00, // Adjust as necessary
                'last_replace' => $validatedData['date'],
                'safety_stock' => 10,
                'sap_stock' => $sapStock,
                'repair_stock' => $repairStock,
            ]);
        } else {
            // Part exists, log the current state before updating
            MachineSparePartsInventoryLog::create([
                'inventory_id' => $machinePart->id,
                'old_last_replace' => $machinePart->last_replace,
                'new_last_replace' => $validatedData['date'],
                'old_sap_stock' => $machinePart->sap_stock,
                'new_sap_stock' => Part::where('id', $sparePartId)->value('begining_qty'),
                'old_repair_stock' => $machinePart->repair_stock,
                'new_repair_stock' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'),
                'qty' => $usedQty,
            ]);

            // Update the existing part with new values
            $machinePart->update([
                'last_replace' => $validatedData['date'],
                'sap_stock' => Part::where('id', $sparePartId)->value('begining_qty'),
                'repair_stock' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'),
            ]);
        }

        if ($stockType === 'repair') {
            if (!isset($validatedData['repair_location'][$index])) {
                return redirect()->back()->withErrors(['repair_location' => 'Repair location is required when stock type is repair.']);
            }

            $repairPartId = $validatedData['repair_location'][$index];
            $repairPart = RepairPart::find($repairPartId);

            if ($repairPart) {
                $location = $repairPart->sloc;
                $repairPart->repaired_qty -= $usedQty;
                $repairPart->save();
            }
        } else if ($stockType === 'sap') {
            $location = 'SAP';
        }

        HistoricalProblemPart::create([
            'problem_id' => $problem->id,
            'part_id' => $sparePartId,
            'qty' => $usedQty,
            'location' => $location,
            'routes' => $stockType,
        ]);
    }

    // If checksheet_head_id exists, log the status change
    if ($request->has('checksheet_head_id')) {
        ChecksheetStatusLog::create([
            'historical_id' => $problem->id,
            'checksheet_header_id' => $request->checksheet_head_id,
            'created_by' => $validatedData['pic'],
            'change_date' => $validatedData['date'],
        ]);

        ChecksheetFormHead::where('id', $request->checksheet_head_id)
            ->update(['pm_status' => 'Close']);
    }

    return redirect()->route('history')->with('status', 'Historical problem recorded successfully');
}


public function storehpStatus(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'id_machine' => 'required|integer',
        'date' => 'required|date',
        'shift' => 'required|string',
        'report' => 'required|string',
        'shop' => 'required|string',
        'problem' => 'required|string',
        'cause' => 'required|string',
        'action' => 'required|string',
        'spare_part.*' => 'nullable|integer',
        'stock_type.*' => 'nullable|string',
        'used_qty.*' => 'nullable|integer',
        'start_time' => 'required|date_format:H:i',
        'finish_time' => 'required|date_format:H:i',
        'balance' => 'required|numeric',
        'pic' => 'required|string',
        'status' => 'required|string',
        'remarks' => 'nullable|string',
        'img' => 'nullable|image|max:2048',
        'checksheet_head_id' => 'nullable|integer',
        'category' => 'required|string', // Add validation for category
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
        'category' => $validatedData['category'], // Save the category
        'report' => $validatedData['report'], // Save the report
    ]);

    // Save the spare parts data and update the repair parts inventory if stock type is repair
    foreach ($validatedData['spare_part'] as $index => $sparePartId) {
        // Skip processing if the spare part ID is null
        if (is_null($sparePartId)) {
            continue;
        }

        $stockType = $validatedData['stock_type'][$index];
        $usedQty = $validatedData['used_qty'][$index];
        $location = null;

        // Check if part exists for the specific machine in machine_spare_parts_inventories
        $machinePart = MachineSparePartsInventory::where('part_id', $sparePartId)
            ->where('machine_id', $validatedData['id_machine'])
            ->first();

        if (!$machinePart) {
            // Part does not exist for this machine, create it with default values
            $repairStock = RepairPart::where('part_id', $sparePartId)->sum('repaired_qty');
            $sapStock = Part::where('id', $sparePartId)->value('begining_qty');

            $machinePart = MachineSparePartsInventory::create([
                'part_id' => $sparePartId,
                'machine_id' => $validatedData['id_machine'],
                'critical_part' => 'Default', // Adjust as necessary
                'type' => 'Default', // Adjust as necessary
                'estimation_lifetime' => 5,
                'cost' => 0.00, // Adjust as necessary
                'last_replace' => $validatedData['date'],
                'safety_stock' => 10,
                'sap_stock' => $sapStock,
                'repair_stock' => $repairStock,
            ]);
        } else {
            // Part exists, log the current state before updating
            MachineSparePartsInventoryLog::create([
                'inventory_id' => $machinePart->id,
                'old_last_replace' => $machinePart->last_replace,
                'new_last_replace' => $validatedData['date'],
                'old_sap_stock' => $machinePart->sap_stock,
                'new_sap_stock' => Part::where('id', $sparePartId)->value('begining_qty'),
                'old_repair_stock' => $machinePart->repair_stock,
                'new_repair_stock' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'),
                'qty' => $usedQty,
            ]);

            // Update the existing part with new values
            $machinePart->update([
                'last_replace' => $validatedData['date'],
                'sap_stock' => Part::where('id', $sparePartId)->value('begining_qty'),
                'repair_stock' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'),
            ]);
        }

        if ($stockType === 'repair') {
            if (!isset($validatedData['repair_location'][$index])) {
                return redirect()->back()->withErrors(['repair_location' => 'Repair location is required when stock type is repair.']);
            }

            $repairPartId = $validatedData['repair_location'][$index];
            $repairPart = RepairPart::find($repairPartId);

            if ($repairPart) {
                $location = $repairPart->sloc;
                $repairPart->repaired_qty -= $usedQty;
                $repairPart->save();
            }
        } else if ($stockType === 'sap') {
            $location = 'SAP';
        }

        HistoricalProblemPart::create([
            'problem_id' => $problem->id,
            'part_id' => $sparePartId,
            'qty' => $usedQty,
            'location' => $location,
            'routes' => $stockType,
        ]);
    }

    // If checksheet_head_id exists, log the status change
    if ($request->has('checksheet_head_id')) {
        ChecksheetStatusLog::create([
            'historical_id' => $problem->id,
            'checksheet_header_id' => $request->checksheet_head_id,
            'created_by' => $validatedData['pic'],
            'change_date' => $validatedData['date'],
        ]);

        ChecksheetFormHead::where('id', $request->checksheet_head_id)
            ->update(['pm_status' => 'Close']);
    }

    return redirect()->route('machine')->with('status', 'Historical problem recorded successfully');
}



    public function formStatus($no_machine, $date, $shift,$pm_id,$checksheet_head_id){
        $no_machine = decrypt($no_machine);
        $machine = Machine::where('id',$no_machine)->first();
        $dropdown = Dropdown::where('category','Problem')->get();
        $pm_item = PreventiveMaintenance::where('id',$pm_id)->first();
        // Fetch spare parts related to the machine with part details
        $spareParts = Part::all();

        return view('history.formStatus', compact('dropdown','pm_item','machine','no_machine', 'date', 'shift', 'spareParts','pm_id','checksheet_head_id'));

    }

}
