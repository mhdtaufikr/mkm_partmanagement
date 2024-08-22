<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Part;
use App\Models\RepairPart;
use App\Models\Machine;
use App\Models\MachineSparePartsInventory;
use App\Exports\PartsSAPTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PartsSAPImport;
use Illuminate\Support\Facades\DB;


class MstPartSAPController extends Controller
{
    public function sapPart(){
        $item = Part::get();
        return view('master.sap',compact('item'));
    }

    public function sapTemplate()
    {
        return Excel::download(new PartsSAPTemplateExport, 'parts_sap_template.xlsx');
    }

    public function sapUpload(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Excel::import(new PartsSAPImport, $request->file('excel-file'));
            });
            return redirect()->back()->with('status', 'File imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Failed to import file: ' . $e->getMessage());
        }
    }

    public function sapPartDetail($id) {
        $id = decrypt($id);

        // Fetch part details
        $part = Part::find($id);

        // Fetch repair part details
        $repairParts = RepairPart::where('part_id', $id)->get();
        $repairPartsTotalQty = RepairPart::where('part_id', $id)->sum('repaired_qty');


        // Fetch machine details using the part
        $machineParts = MachineSparePartsInventory::where('part_id', $id)->with('machine')->get();
        $machines = Machine::all();

        return view('master.sapdtl', compact('part', 'repairParts', 'machineParts','machines','repairPartsTotalQty'));
    }

    public function addImage(Request $request)
    {

        // Validate the incoming request
        $request->validate([
            'id' => 'required', // Ensure the machine ID exists
            'new_images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image files
        ]);

        // Retrieve the machine
        $machine = Part::findOrFail($request->id);

        $imagePaths = $machine->img ? json_decode($machine->img, true) : [];

        // Check if the request has any new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                // Generate a unique file name for each image
                $fileName = uniqid() . '_' . $file->getClientOriginalName();

                // Move the uploaded image to the storage directory
                $destinationPath = public_path('assets/img/parts');
                $file->move($destinationPath, $fileName);

                // Store the image path in the array
                $imagePaths[] = 'assets/img/parts/' . $fileName;
            }

            // Save the updated machine with the new image paths
            $machine->img = json_encode($imagePaths); // Convert back to JSON before saving
            $machine->save();
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Images uploaded successfully.');
    }

    public function sapPartDetailStore(Request $request)
    {

        // Validate the request data if needed
        $request->validate([
            'machine_id' => 'required|integer',
            'part_id' => 'required|integer',
            'critical_part' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'estimation_lifetime' => 'required|integer',
            'cost' => 'required|numeric',
            'last_replace' => 'required|date',
            'safety_stock' => 'required|integer',
            'sap_stock' => 'required|numeric',
            'repair_stock' => 'required|numeric',
        ]);


        // Store the data in the database
        $machinePart = MachineSparePartsInventory::create([
            'machine_id' => $request->machine_id,
            'part_id' => $request->part_id,
            'critical_part' => $request->critical_part,
            'type' => $request->type,
            'estimation_lifetime' => $request->estimation_lifetime,
            'cost' => $request->cost,
            'last_replace' => $request->last_replace,
            'safety_stock' => $request->safety_stock,
            'sap_stock' => $request->sap_stock,
            'repair_stock' => $request->repair_stock,
        ]);

        // Redirect back with a success message or return a response
        return redirect()->back()->with('status', 'Machine part added successfully.');
    }


}
