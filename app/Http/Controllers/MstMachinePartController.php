<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\RepairPart;
use App\Models\Part;
use App\Models\MachineSparePartsInventory;
use App\Models\HistoricalProblem;
use App\Exports\MachineTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MachinesImport;
use Exception;
use App\Exports\PartTemplateExport;
use App\Imports\PartsImport;

class MstMachinePartController extends Controller
{
    public function index(){
        $items = Machine::get();
       return view('master.machine',compact('items'));
    }

    public function detail($id){
        $id = decrypt($id);
        $machine = Machine::with(['inventoryStatus', 'spareParts', 'spareParts.repairs'])->where('id', $id)->first();

        // Get all parts with their total repair quantities
        $parts = Part::withSum('repairs as total_repaired_qty', 'repaired_qty')->get();

        // Get all historical problems with their related spare parts and machine data
        $items = HistoricalProblem::with(['spareParts.part', 'machine'])->where('id_machine',$id)->get();

        return view('master.dtl_machine', compact('machine', 'parts', 'items'));
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


}
