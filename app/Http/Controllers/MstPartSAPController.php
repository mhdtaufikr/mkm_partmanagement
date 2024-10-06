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
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;



class MstPartSAPController extends Controller
{
    public function sapPart(Request $request, $plnt = null)
    {
        if ($request->ajax()) {
            $query = Part::select(['*']);

            if ($plnt) {
                $query->where('plnt', $plnt);
            }

            $items = $query->get()->map(function ($item) {
                $item->encrypted_id = encrypt($item->id); // Add encrypted id for row URL

                // Calculate total value per unit
                $item->total_value_per_unit = $item->total_stock > 0 ? $item->total_value / $item->total_stock : 0;

                return $item;
            });

            return DataTables::of($items)
                ->addIndexColumn()
                ->make(true);
        }

        // Fetch all parts to pass to the view for the delete modal
        $parts = Part::all();

        return view('master.sap', compact('parts')); // Pass the parts variable to the view
    }







    public function sapTemplate()
    {
        return Excel::download(new PartsSAPTemplateExport, 'parts_sap_template.xlsx');
    }

    public function sapUpload(Request $request)
    {
        try {
            // Increase the execution time to handle large files
            set_time_limit(1200);

            // Start database transaction
            DB::beginTransaction();

            // Initialize error log array
            $errorLog = [];

            // Import the file
            Excel::import(new PartsSAPImport($errorLog), $request->file('excel-file'));

            // If there are any errors, rollback the transaction and return the error log
            if (!empty($errorLog)) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Failed to import file. Errors: ' . implode(", ", $errorLog));
            }

            // Commit the transaction if no errors
            DB::commit();
            return redirect()->back()->with('status', 'File imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
        // Get the machine IDs that are already associated with the given part_id
        $associatedMachineIds = MachineSparePartsInventory::where('part_id', $id)
        ->pluck('machine_id')
        ->toArray();

        // Retrieve all machines that are not in the associatedMachineIds array
        $machines = Machine::whereNotIn('id', $associatedMachineIds)->get();

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

    public function store(Request $request)
    {
        $machineIds = $request->input('machine_id');
        $criticalParts = $request->input('critical_part');
        $types = $request->input('type');
        $estimationLifetimes = $request->input('estimation_lifetime');
        $costs = $request->input('cost');
        $lastReplaces = $request->input('last_replace');
        $safetyStocks = $request->input('safety_stock');

        for ($i = 0; $i < count($machineIds); $i++) {
            // Save each set of machine data
            MachinePart::create([
                'machine_id' => $machineIds[$i],
                'critical_part' => $criticalParts[$i],
                'type' => $types[$i],
                'estimation_lifetime' => $estimationLifetimes[$i],
                'cost' => $costs[$i],
                'last_replace' => $lastReplaces[$i],
                'safety_stock' => $safetyStocks[$i],
                'part_id' => $request->input('part_id'),
                'sap_stock' => $request->input('sap_stock'),
                'repair_stock' => $request->input('repair_stock')
            ]);
        }

        return redirect()->back()->with('success', 'Machine parts added successfully.');
    }

    public function sapPartDelete(Request $request)
    {
        // Validate request input
        $request->validate([
            'parts' => 'required|array', // Ensure 'parts' is an array
            'parts.*' => 'exists:parts,id' // Each part ID must exist in the parts table
        ]);

        // Get the array of part IDs to delete
        $partIds = $request->input('parts');

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Step 1: Delete related entries from dependent tables

            // Delete related records in machine_spare_parts_inventories
            DB::table('machine_spare_parts_inventories')->whereIn('part_id', $partIds)->delete();

            // Delete related records in historical_problem_parts
            DB::table('historical_problem_parts')->whereIn('part_id', $partIds)->delete();

            // Since the `historical_problems` table does not directly reference `parts`, no action is needed here
            // unless you want to handle other business logic tied to the parts

            // Step 2: Delete the parts
            DB::table('parts')->whereIn('id', $partIds)->delete();

            // Commit the transaction
            DB::commit();

            // Return a success response
            return redirect()->back()->with('status', 'Selected parts and their dependencies have been deleted successfully.');

        } catch (\Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollback();

            // Return an error response
            return redirect()->back()->with('failed', 'An error occurred while deleting parts. Please try again. ' . $e->getMessage());
        }
    }

    public function partInfo(Request $request)
{
    if ($request->ajax()) {
        // Fetch the authenticated user's plant
        $userPlant = Auth::user()->plant;

        // Fetch parts and their repair quantities
        $parts = Part::select('parts.*')
            ->leftJoin('repair_parts', 'repair_parts.part_id', '=', 'parts.id')
            ->selectRaw('SUM(repair_parts.repaired_qty) as repair_qty');

        // Apply plant filtering based on the user's plant
        if ($userPlant == 'Stamping') {
            $parts->where('parts.plnt', 'P300');
        } elseif ($userPlant == 'Engine') {
            $parts->where('parts.plnt', 'P400');
        }

        // Group by part ID and retrieve the results
        $parts = $parts->groupBy('parts.id')->get();

        return DataTables::of($parts)
            ->addIndexColumn()
            ->editColumn('total_stock', function($part) {
                return number_format($part->total_stock, 2);
            })
            ->addColumn('repair_qty', function($part) {
                return number_format($part->repair_qty ?? 0, 2);
            })
            ->addColumn('total_qty', function($part) {
                return number_format($part->total_stock + ($part->repair_qty ?? 0), 2);
            })
            ->make(true);
    }

    return view('master.info');
}

}
