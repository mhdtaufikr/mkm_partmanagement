<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\RepairPart;
use App\Models\Part;
use App\Models\MachineSparePartsInventory;
use App\Models\ChecksheetJourneyLog;
use App\Models\HistoricalProblem;
use App\Exports\MachineTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MachinesImport;
use Exception;
use App\Exports\PartTemplateExport;
use App\Imports\PartsImport;
use App\Models\PreventiveMaintenanceView;
use App\Models\ChecksheetStatusLog;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use DB;

class MstMachinePartController extends Controller
{
    public function index(Request $request, $location = null)
    {
        if ($request->ajax()) {
            $query = Machine::query();

            if ($location) {
                $query->where('plant', $location);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('mfg_date', function ($row) {
                    return $row->mfg_date ? $row->mfg_date : '-';
                })
                ->addColumn('encrypted_id', function ($row) {
                    return encrypt($row->id);
                })
                ->make(true);
        }

        // Fetch all machines to populate the dropdown in the delete modal
        $machines = Machine::all();

        return view('master.machine', compact('machines'));
    }








    public function detail($id) {
        $id = decrypt($id);
        $machine = Machine::with(['inventoryStatus', 'spareParts', 'spareParts.repairs'])->where('id', $id)->first();

        // Get all parts with their total repair quantities
        $parts = Part::withSum('repairs as total_repaired_qty', 'repaired_qty')->get();

        // Fetch historical problems for the specified machine ID
        $historicalProblems = HistoricalProblem::with(['spareParts.part', 'machine'])
            ->where('id_machine', $id)
            ->get();

        // Fetch preventive maintenance records for the specified machine ID
        $query = PreventiveMaintenanceView::select(
            'preventive_maintenance_view.id',
            'preventive_maintenance_view.id_ch',
            'preventive_maintenance_view.machine_no',
            'preventive_maintenance_view.op_name',
            'preventive_maintenance_view.machine_name',
            'preventive_maintenance_view.no_document',
            'preventive_maintenance_view.type',
            'preventive_maintenance_view.dept',
            'preventive_maintenance_view.shop',
            'preventive_maintenance_view.effective_date',
            'preventive_maintenance_view.mfg_date',
            'preventive_maintenance_view.process',
            'preventive_maintenance_view.revision',
            'preventive_maintenance_view.no_procedure',
            'preventive_maintenance_view.plant',
            'preventive_maintenance_view.location',
            'preventive_maintenance_view.line',
            'preventive_maintenance_view.created_at',
            'preventive_maintenance_view.updated_at',
            'checksheet_form_heads.id as checksheet_id',
            'checksheet_form_heads.planning_date',
            'checksheet_form_heads.actual_date',
            'checksheet_form_heads.pic',
            'checksheet_form_heads.pm_status', // Added this line to include pm_status
            'checksheet_form_heads.status',
            'checksheet_form_heads.created_by',
            'checksheet_form_heads.remark',
            'checksheet_form_heads.created_at as checksheet_created_at',
            'checksheet_form_heads.updated_at as checksheet_updated_at'
        )
        ->join('checksheet_form_heads', 'preventive_maintenance_view.id', '=', 'checksheet_form_heads.preventive_maintenances_id')
        ->where('preventive_maintenance_view.machine_id', $id);

        if (Auth::user()->role == "Checker" || Auth::user()->role == "Approval") {
            $preventiveMaintenances = $query->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        } elseif (Auth::user()->role == "user") {
            $preventiveMaintenances = $query->where('checksheet_form_heads.created_by', Auth::user()->name)->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        } else {
            $preventiveMaintenances = $query->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        }

        // Attach logs to each preventive maintenance record
        foreach ($preventiveMaintenances as $pm) {
            $pm->logs = ChecksheetStatusLog::where('checksheet_header_id', $pm->checksheet_id)->orderBy('created_at', 'desc')->get();
        }

        // Combine the data into a single collection
        $combinedData = collect();
        foreach ($historicalProblems as $problem) {
            $combinedData->push((object) [
                'date' => $problem->date,
                'type' => "Daily Report",  // Use the report column as the type
                'data' => $problem,
                'Category' => $problem->report,
                'status_logs' => collect()  // Add an empty collection for historical problems
            ]);
        }

        // Add preventive maintenance records to the collection
        foreach ($preventiveMaintenances as $pm) {
            $combinedData->push((object) [
                'date' => $pm->planning_date,
                'type' => 'Preventive Maintenance',
                'data' => $pm,
                'Category' => 'Preventive Maintenance',
                'status_logs' => $pm->logs  // Attach the logs collection to the pm data
            ]);
        }

        // Sort the combined data by date
        $combinedData = $combinedData->sortBy('date');
        return view('master.dtl_machine', compact('machine', 'parts', 'combinedData'));
    }




    public function getRepairStock($partId)
    {
        $repairStock = RepairPart::where('part_id', $partId)->sum('repaired_qty');
        return response()->json(['repair_stock' => $repairStock]);
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

    public function store(Request $request)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'part_id' => 'required|integer',
        'machine_id' => 'required|integer',
        'estimation_lifetime' => 'required|integer',
        'last_replace' => 'required|date',
        'safety_stock' => 'required|integer',
    ]);

    // Retrieve the part data from the Part model
    $part = Part::find($request->part_id);

    // Create a new MachineSparePartsInventory entry
    $inventory = new MachineSparePartsInventory();
    $inventory->part_id = $validatedData['part_id'];
    $inventory->machine_id = $validatedData['machine_id'];
    $inventory->critical_part = $part->material_description; // Assuming this is the critical part info
    $inventory->type = $part->type;
    $inventory->estimation_lifetime = $validatedData['estimation_lifetime'];
    $inventory->cost = $part->total_value;
    $inventory->last_replace = $validatedData['last_replace'];
    $inventory->safety_stock = $validatedData['safety_stock'];
    $inventory->sap_stock = $part->total_stock;
    $inventory->created_at = now();
    $inventory->updated_at = now();

    // Save the entry to the database
    $inventory->save();

    // Redirect back with a success message
    return redirect()->back()->with('status', 'Part added successfully to machine inventory!');
}
    public function storeMachine(Request $request)
    {
        $validatedData = $request->validate([
            'plant' => 'required|string|max:255',
            'line' => 'required|string|max:255',
            'op_no' => 'required|string|max:255',
            'machine_name' => 'required|string|max:255',
            'process' => 'required|string',
            'maker' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255',
            'date' => 'required|integer|digits:4',
            'control_nc' => 'nullable|string|max:255',
            'control_plc' => 'nullable|string|max:255',
            'control_servo' => 'nullable|string|max:255',
        ]);

        Machine::create($validatedData);

        return redirect()->back()->with('status', 'Machine added successfully!');
    }

    public function machineTemplate()
    {
        return Excel::download(new MachineTemplateExport, 'machine_template.xlsx');
    }

    public function machineUpload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'excel-file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            // Import the data
            Excel::import(new MachinesImport, $request->file('excel-file'));

            return redirect()->back()->with('status', 'Machines imported successfully.');
        } catch (Exception $e) {
            // Return with error message if import fails
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function partTemplate()
    {
        return Excel::download(new PartTemplateExport, 'part_template.xlsx');
    }

    public function partUpload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'excel-file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            // Import the data
            Excel::import(new PartsImport, $request->file('excel-file'));

            return redirect()->back()->with('status', 'Parts imported successfully.');
        } catch (Exception $e) {
            // Return with error message if import fails
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function addImage(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'id' => 'required|exists:machines,id', // Ensure the machine ID exists
        'new_images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image files
    ]);

    // Retrieve the machine
    $machine = Machine::findOrFail($request->id);

    $imagePaths = $machine->img ? json_decode($machine->img, true) : [];

    // Check if the request has any new images
    if ($request->hasFile('new_images')) {
        foreach ($request->file('new_images') as $file) {
            // Generate a unique file name for each image
            $fileName = uniqid() . '_' . $file->getClientOriginalName();

            // Move the uploaded image to the storage directory
            $destinationPath = public_path('assets/img/machine');
            $file->move($destinationPath, $fileName);

            // Store the image path in the array
            $imagePaths[] = 'assets/img/machine/' . $fileName;
        }

        // Save the updated machine with the new image paths
        $machine->img = json_encode($imagePaths); // Convert back to JSON before saving
        $machine->save();
    }

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Images uploaded successfully.');
}

public function deleteImage(Request $request)
{
    // Retrieve the image path and machine ID from the request data
    $imgPath = $request->input('img_path');
    $machineId = $request->input('id');

    // Find the machine by ID
    $machine = Machine::findOrFail($machineId);

    // Decode the image paths from JSON
    $imagePaths = json_decode($machine->img, true);

    // Find the index of the image path to delete
    $index = array_search($imgPath, $imagePaths);

    // If the image path exists, remove it from the array
    if ($index !== false) {
        unset($imagePaths[$index]);

        // Update the image paths in the database
        $machine->img = json_encode(array_values($imagePaths));
        $machine->save();

        // Delete the image file from the server
        $imageFilePath = public_path($imgPath);
        if (file_exists($imageFilePath)) {
            unlink($imageFilePath);
        }
    }

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Image deleted successfully.');
}

public function deleteMachine(Request $request)
{
    // Validate request input
    $request->validate([
        'machines' => 'required|array', // Ensure 'machines' is an array
        'machines.*' => 'exists:machines,id' // Each machine ID must exist in the machines table
    ]);

    // Get the array of machine IDs to delete
    $machineIds = $request->input('machines');

    try {
        // Start a database transaction
        DB::beginTransaction();

        // Step 1: Delete related entries from dependent tables

        // Delete related records in historical_problems first
        DB::table('historical_problems')->whereIn('id_machine', $machineIds)->delete();

        // Delete from checksheet_status_logs where checksheet_header_id is related to the machines
        $checksheetHeaderIds = DB::table('preventive_maintenances')
            ->whereIn('machine_id', $machineIds)
            ->join('checksheet_form_heads', 'preventive_maintenances.id', '=', 'checksheet_form_heads.preventive_maintenances_id')
            ->pluck('checksheet_form_heads.id');

        DB::table('checksheet_status_logs')->whereIn('checksheet_header_id', $checksheetHeaderIds)->delete();

        // Delete from checksheet_journey_logs where checksheet_id is related to the machines
        DB::table('checksheet_journey_logs')->whereIn('checksheet_id', $checksheetHeaderIds)->delete();

        // Delete from checksheet_form_details where id_header is related to the machines
        DB::table('checksheet_form_details')->whereIn('id_header', $checksheetHeaderIds)->delete();

        // Delete from checksheet_form_heads
        DB::table('checksheet_form_heads')->whereIn('id', $checksheetHeaderIds)->delete();

        // Delete from pm_schedule_details
        $pmScheduleMasterIds = DB::table('preventive_maintenances')
            ->whereIn('machine_id', $machineIds)
            ->join('pm_schedule_masters', 'preventive_maintenances.id', '=', 'pm_schedule_masters.pm_id')
            ->pluck('pm_schedule_masters.id');

        DB::table('pm_schedule_details')->whereIn('pm_schedule_master_id', $pmScheduleMasterIds)->delete();

        // Delete from pm_schedule_masters
        DB::table('pm_schedule_masters')->whereIn('id', $pmScheduleMasterIds)->delete();

        // Delete from preventive_maintenances
        DB::table('preventive_maintenances')->whereIn('machine_id', $machineIds)->delete();

        // Delete from machine_spare_parts_inventories
        DB::table('machine_spare_parts_inventories')->whereIn('machine_id', $machineIds)->delete();

        // Step 2: Delete the machines
        DB::table('machines')->whereIn('id', $machineIds)->delete();

        // Commit the transaction
        DB::commit();

        // Return a success response
        return redirect()->back()->with('status', 'Selected machines and their dependencies have been deleted successfully.');

    } catch (\Exception $e) {
        // Rollback the transaction in case of any error
        DB::rollback();

        // Return an error response
        return redirect()->back()->with('failed', 'An error occurred while deleting machines. Please try again.'. $e->getMessage());
    }
}




}
