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
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Exports\HistoricalProblemTemplateExport;
use App\Imports\HistoricalProblemImport;

class HistoryController extends Controller
{
    public function index(Request $request)
{
    $userPlant = auth()->user()->plant;
    $userType = auth()->user()->type;

    if ($request->ajax()) {
        // Modify query to fetch both parent and child records, and join the machines table
        $query = HistoricalProblem::with(['spareParts.part', 'machine', 'children'])
            ->join('machines', 'historical_problems.id_machine', '=', 'machines.id')  // Join machines table
            ->select('historical_problems.*', 'machines.plant')  // Select historical_problems fields and plant from machines
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        // Apply user filters based on plant and type
        if (($userPlant === 'Engine' || $userPlant === 'Stamping') && ($userType === 'Mechanic' || $userType === 'Electric')) {
            $query->whereHas('machine', function($q) use ($userPlant) {
                $q->where('plant', $userPlant)->where('shop', 'ME');
            });
        } elseif (($userPlant === 'Engine' || $userPlant === 'Stamping') && $userType === 'Power House') {
            $query->whereHas('machine', function($q) use ($userPlant) {
                $q->where('plant', $userPlant)->where('shop', 'PH');
            });
        }

        // Process data for DataTables
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('start_time', function ($row) {
                return \Carbon\Carbon::parse($row->start_time)->format('H:i');
            })
            ->editColumn('finish_time', function ($row) {
                return \Carbon\Carbon::parse($row->finish_time)->format('H:i');
            })
            ->editColumn('status', function ($row) {
                $latestStatus = $row;
                while ($latestStatus->parent_id !== null) {
                    $latestStatus = $latestStatus->parent()->first();
                }
                while ($latestStatus->children()->exists()) {
                    $latestStatus = $latestStatus->children()->latest('id')->first();
                }
                return $latestStatus->status;
            })
            ->editColumn('machine.op_no', function ($row) {
                // Combine op_no and machine_name with a line break
                return '<a href="/mst/machine/detail/' . encrypt($row->machine->id) . '" target="_blank">' . $row->machine->op_no . '<br>' . $row->machine->machine_name . '</a>';
            })

            ->addColumn('flag', function ($row) {
                $isChild = !is_null($row->parent_id);
                $hasChildren = $row->children()->exists();
                if ($isChild || $hasChildren) {
                    return '<i class="fas fa-flag" style="color: rgba(0, 103, 127, 1);"></i>';
                }
                return '';
            })
            ->addColumn('action', function($row){
                return '<button class="btn btn-sm btn-primary btn-detail" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#modal-detail">Detail</button>';
            })
            ->rawColumns(['action', 'flag', 'machine.op_no'])
            ->make(true);
    }

    $machinesQuery = Machine::query();
    $linesQuery = Machine::select('line')->distinct();

    if (($userPlant === 'Engine' || $userPlant === 'Stamping') && ($userType === 'Mechanic' || $userType === 'Electric')) {
        $machinesQuery->where('plant', $userPlant)->where('shop', 'ME');
        $linesQuery->where('plant', $userPlant)->where('shop', 'ME');
    } elseif (($userPlant === 'Engine' || $userPlant === 'Stamping') && $userType === 'Power House') {
        $machinesQuery->where('plant', $userPlant)->where('shop', 'PH');
        $linesQuery->where('plant', $userPlant)->where('shop', 'PH');
    }

    $machines = $machinesQuery->get();
    $lines = $linesQuery->get();

    // Fetch open reports logic remains unchanged
    $openReports = HistoricalProblem::whereNull('parent_id')
        ->whereIn('status', ['Not Good', 'Temporary'])
        ->with('children')
        ->whereHas('machine', function ($q) use ($userPlant, $userType) {
            if ($userPlant !== 'All' && $userType !== 'All') {
                $q->where('plant', $userPlant);
                if ($userType !== 'All') {
                    $q->whereHas('historicalProblems', function ($q2) use ($userType) {
                        $q2->where('shop', $userType);
                    });
                }
            }
        })
        ->get();

    // Filter out chains where any descendant has "OK" status
    $openReports = $openReports->filter(function ($report) {
        return !$this->hasOkInDescendants($report);
    });

    return view('history.index', compact('machines', 'lines', 'openReports'));
}


    private function hasOkInDescendants($report) {
        // If this report has a status of "OK", exclude the chain
        if ($report->status == 'OK') {
            return true;
        }

        // Recursively check each child
        foreach ($report->children as $child) {
            if ($this->hasOkInDescendants($child)) {
                return true;
            }
        }

        // If no descendants have "OK", include the chain
        return false;
    }





    public function showDetail($id)
    {
        // Fetch the current record (which could be parent or child) with spare parts and machine details
        $data = HistoricalProblem::with(['spareParts.part', 'machine', 'children'])->findOrFail($id);

        // Check if the current record has a parent
        $parent = null;
        if ($data->parent_id) {
            $parent = HistoricalProblem::with(['spareParts.part', 'machine'])->find($data->parent_id);
        }

        // Fetch the latest child if it exists
        $latestChild = $data->children()->latest()->first();

        // Return the view with the current, parent, and latest child data
        return view('history.partials', compact('data', 'parent', 'latestChild'));
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

public function form()
{
    // Get the currently logged-in user's plant and type
    $userPlant = auth()->user()->plant;
    $userType = auth()->user()->type;

    // Prepare the query for lines
    $linesQuery = Machine::select('line')->distinct();

    // Apply filtering based on the plant and type of the user
    if (($userPlant === 'Engine' || $userPlant === 'Stamping') && ($userType === 'Mechanic' || $userType === 'Electric')) {
        $linesQuery->where('plant', $userPlant)->where('shop', 'ME');
    } elseif (($userPlant === 'Engine' || $userPlant === 'Stamping') && $userType === 'Power House') {
        $linesQuery->where('plant', $userPlant)->where('shop', 'PH');
    }
    // Fetch the filtered lines
    $lines = $linesQuery->get();

    // Fetch the dropdown options and spare parts
    $dropdown = Dropdown::where('category', 'Problem')->get();
    $spareParts = Part::select('id', 'material', 'material_description')->distinct()->get();
    $user = auth()->user();
    // Return the view with the variables
    return view('history.form', compact('spareParts', 'dropdown', 'lines','user'));
}




public function fetchParts(Request $request)
{
    try {
        $searchTerm = $request->input('term'); // Get the search term from the AJAX request
        $stockType = $request->input('stock_type'); // Get the selected stock type

        Log::info('Fetching parts with searchTerm: ' . $searchTerm . ' and stockType: ' . $stockType);

        $results = [];
        $hasMorePages = false; // Initialize a variable to track pagination

        if ($stockType == 'sap') {
            // Fetch unique parts from the parts table for SAP stock type
            $query = Part::query();

            if (!empty($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('material', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('material_description', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            $spareParts = $query->distinct('id')->paginate(10); // Ensure distinct results by id

            foreach ($spareParts as $part) {
                $results[] = [
                    'id' => $part->id,
                    'text' => $part->material . ' - ' . $part->material_description
                ];
            }

            $hasMorePages = $spareParts->hasMorePages(); // Set pagination flag based on paginator instance

        } elseif ($stockType == 'repair') {
            // Fetch unique parts based on part_id for repair stock type
            $query = RepairPart::with('part')
                ->selectRaw('MIN(repair_parts.id) as id, part_id') // Use MIN or MAX on columns not in GROUP BY
                ->groupBy('part_id'); // Group by part_id

            if (!empty($searchTerm)) {
                $query->whereHas('part', function ($q) use ($searchTerm) {
                    $q->where('material', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('material_description', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            $repairParts = $query->paginate(10);

            foreach ($repairParts as $repair) {
                if ($repair->part) {
                    $results[] = [
                        'id' => $repair->id, // Use aggregated id
                        'text' => $repair->part->material . ' - ' . $repair->part->material_description
                    ];
                } else {
                    Log::warning('No part associated with repair ID: ' . $repair->id); // Log if no associated part
                }
            }

            $hasMorePages = $repairParts->hasMorePages(); // Set pagination flag based on paginator instance
        }

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $hasMorePages]
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching parts: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch parts'], 500);
    }
}



public function getSapQuantity(Request $request)
{
    $partId = $request->input('part_id'); // Get the part ID from the request

    // Fetch the SAP quantity from the database
    $part = Part::find($partId);

    if ($part) {
        return response()->json(['sap_quantity' => $part->begining_qty]);
    } else {
        return response()->json(['error' => 'Part not found'], 404);
    }
}


public function getRepairLocationsForPart($repair_part_id)
{
    try {
        Log::info('Fetching repair locations for repair part ID: ' . $repair_part_id);

        // Get the part_id using the provided repair_part_id
        $repairPart = RepairPart::find($repair_part_id);

        if ($repairPart) {
            $part_id = $repairPart->part_id; // Get the part_id from the repair part

            // Fetch all repair parts associated with this part_id without filtering out duplicates
            $locations = RepairPart::where('part_id', $part_id)->get(['id', 'sloc', 'repaired_qty']); // Fetch all locations

            Log::info('Locations fetched: ', $locations->toArray());

            return response()->json($locations);
        } else {
            Log::info('No repair part found for ID: ' . $repair_part_id);
            return response()->json([]); // Return an empty array if no repair part is found
        }
    } catch (\Exception $e) {
        Log::error('Error fetching repair locations: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to retrieve locations'], 500);
    }
}

public function storehp(Request $request)
{
    // Begin a database transaction
    DB::beginTransaction();

    try {
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
            'spare_part_sap.*' => 'nullable|integer',
            'spare_part_repair.*' => 'nullable|integer',
            'stock_type.*' => 'nullable|string',
            'used_qty_sap.*' => 'nullable|integer',
            'used_qty_repair.*' => 'nullable|integer',
            'start_time' => 'required|date_format:H:i',
            'finish_time' => 'required|date_format:H:i',
            'balance' => 'required|numeric',
            'pic' => 'required|string',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'img' => 'nullable|image|max:2048',
            'checksheet_head_id' => 'nullable|integer',
            'category' => 'required|string',
            'part_type.*' => 'nullable|string',
            'repair_location.*' => 'nullable|integer',
            'other_part_name.*' => 'nullable|string',
            'other_part_name_description.*' => 'nullable|string',
            'bun.*' => 'nullable|string',
            'other_part_location.*' => 'nullable|string',
            'Cost.*' => 'nullable|numeric',
            'other_part_quantity.*' => 'nullable|integer',
            'parent_id' => 'nullable|integer',
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

        $includeKpi = $request->has('include_kpi') ? 'A' : '';

        // Check if parent_id exists in the request
        $parentId = $request->input('parent_id', null);

        // Save the main problem data, including the parent_id if provided
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
            'category' => $validatedData['category'],
            'report' => $validatedData['report'],
            'kpi' => $includeKpi,  // Include KPI value
            'parent_id' => $parentId,  // Include parent_id if exists
        ]);

        // Process SAP parts if any
        if (!empty(array_filter($validatedData['spare_part_sap'] ?? []))) {
            foreach ($validatedData['spare_part_sap'] as $index => $sparePartId) {
                if (is_null($sparePartId)) continue;

                $usedQty = $validatedData['used_qty_sap'][$index] ?? null;
                if (is_null($usedQty)) continue;

                $location = 'SAP';

                // Check if part exists for the specific machine in machine_spare_parts_inventories
                $machinePart = MachineSparePartsInventory::where('part_id', $sparePartId)
                    ->where('machine_id', $validatedData['id_machine'])
                    ->first();

                if (!$machinePart) {
                    $sapStock = Part::where('id', $sparePartId)->value('begining_qty');

                    $machinePart = MachineSparePartsInventory::create([
                        'part_id' => $sparePartId,
                        'machine_id' => $validatedData['id_machine'],
                        'critical_part' => 'Default',
                        'type' => 'Default',
                        'estimation_lifetime' => 5,
                        'cost' => 0.00,
                        'last_replace' => $validatedData['date'],
                        'safety_stock' => 10,
                        'sap_stock' => $sapStock,
                        'repair_stock' => 0
                    ]);
                } else {
                    MachineSparePartsInventoryLog::create([
                        'inventory_id' => $machinePart->id,
                        'old_last_replace' => $machinePart->last_replace,
                        'new_last_replace' => $validatedData['date'],
                        'old_sap_stock' => $machinePart->sap_stock,
                        'new_sap_stock' => Part::where('id', $sparePartId)->value('begining_qty'),
                        'old_repair_stock' => $machinePart->repair_stock,
                        'new_repair_stock' => $machinePart->repair_stock,
                        'qty' => $usedQty,
                    ]);

                    $machinePart->update([
                        'last_replace' => $validatedData['date'],
                        'sap_stock' => Part::where('id', $sparePartId)->value('begining_qty'),
                    ]);
                }

                // Create an entry for each SAP spare part
                HistoricalProblemPart::create([
                    'problem_id' => $problem->id,
                    'part_id' => $sparePartId,
                    'qty' => $usedQty,
                    'location' => $location,
                    'routes' => 'sap',
                ]);
            }
        }

        // Process Repair parts if any
        if (!empty(array_filter($validatedData['spare_part_repair'] ?? []))) {
            foreach ($validatedData['spare_part_repair'] as $index => $sparePartId) {
                if (is_null($sparePartId)) continue;

                $usedQty = $validatedData['used_qty_repair'][$index] ?? null;
                if (is_null($usedQty)) continue;

                if (!isset($validatedData['repair_location'][$index])) {
                    return redirect()->back()->withErrors(['repair_location' => 'Repair location is required when stock type is repair.']);
                }

                $repairPartId = $validatedData['repair_location'][$index];
                $repairPart = RepairPart::find($repairPartId);

                if ($repairPart) {
                    $location = $repairPart->sloc;
                    $repairPart->repaired_qty -= $usedQty;
                    $repairPart->save();
                } else {
                    continue; // Skip if repair part not found
                }

                // Check if part exists for the specific machine in machine_spare_parts_inventories
                $machinePart = MachineSparePartsInventory::where('part_id', $sparePartId)
                    ->where('machine_id', $validatedData['id_machine'])
                    ->first();

                if (!$machinePart) {
                    $repairStock = RepairPart::where('part_id', $sparePartId)->sum('repaired_qty');

                    $machinePart = MachineSparePartsInventory::create([
                        'part_id' => $sparePartId,
                        'machine_id' => $validatedData['id_machine'],
                        'critical_part' => 'Default',
                        'type' => 'Repair',
                        'estimation_lifetime' => 5,
                        'cost' => 0.00,
                        'last_replace' => $validatedData['date'],
                        'safety_stock' => 10,
                        'sap_stock' => 0,
                        'repair_stock' => $repairStock
                    ]);
                } else {
                    MachineSparePartsInventoryLog::create([
                        'inventory_id' => $machinePart->id,
                        'old_last_replace' => $machinePart->last_replace,
                        'new_last_replace' => $validatedData['date'],
                        'old_sap_stock' => $machinePart->sap_stock,
                        'new_sap_stock' => $machinePart->sap_stock,
                        'old_repair_stock' => $machinePart->repair_stock,
                        'new_repair_stock' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'),
                        'qty' => $usedQty,
                    ]);

                    $machinePart->update([
                        'last_replace' => $validatedData['date'],
                        'repair_stock' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'),
                    ]);
                }

                // Create an entry for each repair spare part
                HistoricalProblemPart::create([
                    'problem_id' => $problem->id,
                    'part_id' => $sparePartId,
                    'qty' => $usedQty,
                    'location' => $location,
                    'routes' => 'repair',
                ]);
            }
        }

        // Process Other Parts if any
        if ($request->has('part_type') && in_array('other', $validatedData['part_type'])) {
            foreach ($validatedData['other_part_name'] as $index => $otherPartName) {
                // Generate material name by the system
                $materialName = 'MAT-' . strtoupper(substr($otherPartName, 0, 3)) . '-' . uniqid();

                // Create a new entry in the parts table with default values for some fields
                $part = Part::create([
                    'material' => $materialName, // Auto-generated material name
                    'material_description' => $otherPartName, // User-provided description
                    'type' => 'Other', // Set type as 'Other'
                    'plnt' => 'OTHER', // Default value for plant
                    'sloc' => 'OTHER', // Default value for storage location
                    'vendor' => 'N/A', // Default vendor
                    'bun' => 'PCS', // Default unit of measurement
                    'begining_qty' => 0, // Default starting quantity
                    'begining_value' => 0, // Default starting value
                    'received_qty' => 0, // Default received quantity
                    'received_value' => 0, // Default received value
                    'consumed_qty' => 0, // Default consumed quantity
                    'consumed_value' => 0, // Default consumed value
                    'total_stock' => $validatedData['other_part_quantity'][$index] ?? 0, // User-provided quantity
                    'total_value' => 0, // Default total value
                    'currency' => 'IDR', // Currency
                    'img' => null, // No image provided
                ]);

                // Add the new part to the machine inventory
                MachineSparePartsInventory::create([
                    'part_id' => $part->id,
                    'machine_id' => $validatedData['id_machine'],
                    'critical_part' => 'Default',
                    'type' => 'Other',
                    'estimation_lifetime' => 5, // Default estimation lifetime
                    'cost' => 0.00, // Default cost
                    'last_replace' => $validatedData['date'], // Date of last replacement
                    'safety_stock' => 10, // Default safety stock
                    'sap_stock' => 0, // Default SAP stock
                    'repair_stock' => 0, // Default repair stock
                ]);

                // Create an entry in the historical_problem_parts table
                HistoricalProblemPart::create([
                    'problem_id' => $problem->id,
                    'part_id' => $part->id,
                    'qty' => $validatedData['other_part_quantity'][$index] ?? 0, // User-provided quantity
                    'location' => 'OTHER', // Default location
                    'routes' => 'other', // Route as 'other'
                ]);
            }
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

        // Commit the transaction
        DB::commit();

        return redirect()->route('history')->with('status', 'Historical problem recorded successfully');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors explicitly
        DB::rollBack();

        \Log::error('Validation error occurred in storehp method: ' . json_encode($e->errors()));

        // Return validation errors to the form with input
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();

        \Log::error('Error occurred in storehp method: ' . $e->getMessage());

        return redirect()->back()->withErrors(['error' => 'Data Process Failed! An error occurred while processing your request. Please try again. Error Details: ' . $e->getMessage()]);
    }
}





public function storehpStatus(Request $request)
{
    // Begin a database transaction
    DB::beginTransaction();

    try {
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
            'spare_part_sap.*' => 'nullable|integer',
            'spare_part_repair.*' => 'nullable|integer',
            'stock_type.*' => 'nullable|string',
            'used_qty_sap.*' => 'nullable|integer',
            'used_qty_repair.*' => 'nullable|integer',
            'start_time' => 'required|date_format:H:i',
            'finish_time' => 'required|date_format:H:i',
            'balance' => 'required|numeric',
            'pic' => 'required|string',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'img' => 'nullable|image|max:2048',
            'checksheet_head_id' => 'nullable|integer',
            'category' => 'required|string',
            'part_type.*' => 'nullable|string',
            'repair_location.*' => 'nullable|integer',
            'other_part_name.*' => 'nullable|string',
            'other_part_name_description.*' => 'nullable|string',
            'bun.*' => 'nullable|string',
            'other_part_location.*' => 'nullable|string',
            'Cost.*' => 'nullable|numeric',
            'other_part_quantity.*' => 'nullable|integer',
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

        $includeKpi = $request->has('include_kpi') ? 'A' : '';

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
            'category' => $validatedData['category'],
            'report' => $validatedData['report'],
            'kpi' => $includeKpi,  // Include KPI value here
        ]);

        // Process SAP parts if any
        if ($request->has('spare_part_sap') && !empty(array_filter($validatedData['spare_part_sap']))) {
            foreach ($validatedData['spare_part_sap'] as $index => $sparePartId) {
                if (is_null($sparePartId)) {
                    continue;
                }

                $stockType = 'sap'; // Stock type is SAP for these parts
                $usedQty = $validatedData['used_qty_sap'][$index] ?? null;

                if (is_null($usedQty)) {
                    continue;
                }

                $location = 'SAP';

                // Check if part exists for the specific machine in machine_spare_parts_inventories
                $machinePart = MachineSparePartsInventory::where('part_id', $sparePartId)
                    ->where('machine_id', $validatedData['id_machine'])
                    ->first();

                if (!$machinePart) {
                    $sapStock = Part::where('id', $sparePartId)->value('begining_qty');

                    $machinePart = MachineSparePartsInventory::create([
                        'part_id' => $sparePartId,
                        'machine_id' => $validatedData['id_machine'],
                        'critical_part' => 'Default',
                        'type' => 'Default',
                        'estimation_lifetime' => 5,
                        'cost' => 0.00,
                        'last_replace' => $validatedData['date'],
                        'safety_stock' => 10,
                        'sap_stock' => $sapStock,
                        'repair_stock' => 0,
                        'total' => $sapStock, // Ensure total is calculated correctly
                        'status' => 'Active',
                    ]);
                } else {
                    MachineSparePartsInventoryLog::create([
                        'inventory_id' => $machinePart->id,
                        'old_last_replace' => $machinePart->last_replace,
                        'new_last_replace' => $validatedData['date'],
                        'old_sap_stock' => $machinePart->sap_stock,
                        'new_sap_stock' => Part::where('id', $sparePartId)->value('begining_qty'),
                        'old_repair_stock' => $machinePart->repair_stock,
                        'new_repair_stock' => $machinePart->repair_stock,
                        'qty' => $usedQty,
                    ]);

                    $machinePart->update([
                        'last_replace' => $validatedData['date'],
                        'sap_stock' => Part::where('id', $sparePartId)->value('begining_qty'),
                        'total' => Part::where('id', $sparePartId)->value('begining_qty'), // Ensure total is calculated correctly
                    ]);
                }

                // Create an entry for each SAP spare part
                HistoricalProblemPart::create([
                    'problem_id' => $problem->id,
                    'part_id' => $sparePartId,
                    'qty' => $usedQty,
                    'location' => $location,
                    'routes' => $stockType,
                ]);
            }
        }

        // Process Repair parts if any
        if ($request->has('spare_part_repair') && !empty(array_filter($validatedData['spare_part_repair']))) {
            foreach ($validatedData['spare_part_repair'] as $index => $sparePartId) {
                if (is_null($sparePartId)) {
                    continue;
                }

                $stockType = 'repair'; // Stock type is Repair for these parts
                $usedQty = $validatedData['used_qty_repair'][$index] ?? null;

                if (is_null($usedQty)) {
                    continue;
                }

                if (!isset($validatedData['repair_location'][$index])) {
                    return redirect()->back()->withErrors(['repair_location' => 'Repair location is required when stock type is repair.']);
                }

                $repairPartId = $validatedData['repair_location'][$index];
                $repairPart = RepairPart::find($repairPartId);

                if ($repairPart) {
                    $location = $repairPart->sloc;
                    $repairPart->repaired_qty -= $usedQty;
                    $repairPart->save();
                } else {
                    continue; // Skip if repair part not found
                }

                // Check if part exists for the specific machine in machine_spare_parts_inventories
                $machinePart = MachineSparePartsInventory::where('part_id', $sparePartId)
                    ->where('machine_id', $validatedData['id_machine'])
                    ->first();

                if (!$machinePart) {
                    $repairStock = RepairPart::where('part_id', $sparePartId)->sum('repaired_qty');

                    $machinePart = MachineSparePartsInventory::create([
                        'part_id' => $sparePartId,
                        'machine_id' => $validatedData['id_machine'],
                        'critical_part' => 'Default',
                        'type' => 'Repair',
                        'estimation_lifetime' => 5,
                        'cost' => 0.00,
                        'last_replace' => $validatedData['date'],
                        'safety_stock' => 10,
                        'sap_stock' => 0,
                        'repair_stock' => $repairStock,
                        'total' => $repairStock, // Ensure total is calculated correctly
                        'status' => 'Active',
                    ]);
                } else {
                    MachineSparePartsInventoryLog::create([
                        'inventory_id' => $machinePart->id,
                        'old_last_replace' => $machinePart->last_replace,
                        'new_last_replace' => $validatedData['date'],
                        'old_sap_stock' => $machinePart->sap_stock,
                        'new_sap_stock' => $machinePart->sap_stock,
                        'old_repair_stock' => $machinePart->repair_stock,
                        'new_repair_stock' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'),
                        'qty' => $usedQty,
                    ]);

                    $machinePart->update([
                        'last_replace' => $validatedData['date'],
                        'repair_stock' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'),
                        'total' => RepairPart::where('part_id', $sparePartId)->sum('repaired_qty'), // Ensure total is calculated correctly
                    ]);
                }

                // Create an entry for each repair spare part
                HistoricalProblemPart::create([
                    'problem_id' => $problem->id,
                    'part_id' => $sparePartId,
                    'qty' => $usedQty,
                    'location' => $location,
                    'routes' => $stockType,
                ]);
            }
        }

        // Process Other Parts if any
        if ($request->has('part_type') && in_array('other', $validatedData['part_type'])) {
            foreach ($validatedData['other_part_name'] as $index => $otherPartName) {
                // Generate material name by the system
                $materialName = 'MAT-' . strtoupper(substr($otherPartName, 0, 3)) . '-' . uniqid();

                // Create a new entry in the parts table with default values for some fields
                $part = Part::create([
                    'material' => $materialName, // Auto-generated material name
                    'material_description' => $otherPartName, // User-provided description
                    'type' => 'Other', // Set type as 'Other'
                    'plnt' => 'OTHER', // Default value for plant
                    'sloc' => 'OTHER', // Default value for storage location
                    'vendor' => 'N/A', // Default vendor
                    'bun' => 'PCS', // Default unit of measurement
                    'begining_qty' => 0, // Default starting quantity
                    'begining_value' => 0, // Default starting value
                    'received_qty' => 0, // Default received quantity
                    'received_value' => 0, // Default received value
                    'consumed_qty' => 0, // Default consumed quantity
                    'consumed_value' => 0, // Default consumed value
                    'total_stock' => $validatedData['other_part_quantity'][$index] ?? 0, // User-provided quantity
                    'total_value' => 0, // Default total value
                    'currency' => 'IDR', // Currency
                    'img' => null, // No image provided
                ]);

                // Add the new part to the machine inventory
                MachineSparePartsInventory::create([
                    'part_id' => $part->id,
                    'machine_id' => $validatedData['id_machine'],
                    'critical_part' => 'Default',
                    'type' => 'Other',
                    'estimation_lifetime' => 5, // Default estimation lifetime
                    'cost' => 0.00, // Default cost
                    'last_replace' => $validatedData['date'], // Date of last replacement
                    'safety_stock' => 10, // Default safety stock
                    'sap_stock' => 0, // Default SAP stock
                    'repair_stock' => 0, // Default repair stock
                ]);

                // Create an entry in the historical_problem_parts table
                HistoricalProblemPart::create([
                    'problem_id' => $problem->id,
                    'part_id' => $part->id,
                    'qty' => $validatedData['other_part_quantity'][$index] ?? 0, // User-provided quantity
                    'location' => 'OTHER', // Default location
                    'routes' => 'other', // Route as 'other'
                ]);
            }
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

        // Commit the transaction
        DB::commit();

        return redirect()->route('machine')->with('status', 'Historical problem recorded successfully');
    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();

        \Log::error('Error occurred in storehpStatus method: ' . $e->getMessage());

        return redirect()->back()->withErrors(['error' => 'Data Process Failed! An error occurred while processing your request. Please try again. Error Details: ' . $e->getMessage()]);
    }
}




public function formStatus($no_machine, $date, $shift, $pm_id, $checksheet_head_id)
{
    $no_machine = decrypt($no_machine);

    // Fetch machine information
    $machine = Machine::find($no_machine);

    // Fetch dropdown options for 'Problem' category
    $dropdown = Dropdown::where('category', 'Problem')->get();

    // Fetch preventive maintenance item based on the provided ID
    $pm_item = PreventiveMaintenance::find($pm_id);

    // Fetch all unique parts directly from the 'parts' table
    $spareParts = Part::select('id', 'material', 'material_description')->distinct()->get();

    // Return the view with the necessary data
    return view('history.formStatus', compact('dropdown', 'pm_item', 'machine', 'no_machine', 'date', 'shift', 'spareParts', 'pm_id', 'checksheet_head_id'));
}


public function formUpdate($id)
{

    $parent_id = decrypt($id);


    // Keep iterating through the parent-child chain until no more children are found
    while (true) {
        // Find the first child where 'parent_id' matches the current parent_id
        $data_id = HistoricalProblem::where('parent_id', $parent_id)->first();

        // If no child is found, break out of the loop
        if (!$data_id) {
            break;
        }

        // Update the parent_id to the child's id and continue the loop to check for further children
        $parent_id = $data_id->id;
    }

    // Now $parent_id holds the latest child's id or the original parent_id if no children exist

    $data = HistoricalProblem::with('machine')->where('id', $parent_id)->first(); // Include machine relation

    // Fetch the dropdown data
    $lines = Machine::select('line')->distinct()->get();
    $dropdown = Dropdown::where('category', 'Problem')->get(); // Assuming dropdown for problems

    return view('history.formUpdate', compact('data', 'lines', 'dropdown','parent_id'));
}


public function uploadBulk(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'excel-file' => 'required|file|mimes:xlsx,csv',
        ]);

        $errorLog = [];  // Initialize an error log array

        // Try to import the uploaded file using the HistoricalProblemImport class
        try {
            Excel::import(new HistoricalProblemImport($errorLog), $request->file('excel-file'));

            // If there are errors, pass them to the response
            if (!empty($errorLog)) {
                return redirect()->back()->with('failed', 'Import failed. Check the error logs for details.')->with('errorLogs', $errorLog);
            }

            // If successful, return a success message
            return redirect()->back()->with('success', 'Historical problems imported successfully.');
        } catch (\Exception $e) {
            // Return the exception message in case of a failure
            return redirect()->back()->with('failed', 'Import failed: ' . $e->getMessage());
        }
    }

    public function templateBulk()
    {
        // Return an Excel file as a template for historical problems
        return Excel::download(new HistoricalProblemTemplateExport, 'historical_problems_template.xlsx');
    }

}
