<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreventiveMaintenance;
use App\Models\PreventiveMaintenanceView;
use App\Models\Checksheet;
use App\Models\ChecksheetItem;
use App\Models\Dropdown;
use App\Models\PmScheduleMaster;
use App\Models\Machine;
use App\Models\PmScheduleDetail;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ChecksheetImport;
use App\Imports\ScheduleImport;
use App\Exports\ScheduleTemplateExport;
use Illuminate\Support\Facades\Validator;
use DB;

class PreventiveController extends Controller
{
    public function index()
{
    // Get machine IDs that are already in the preventive_maintenances table
    $existingMachineIds = PreventiveMaintenance::pluck('machine_id')->toArray();

    // Get the list of machines excluding those that already exist in preventive_maintenances
    $machines = Machine::whereNotIn('id', $existingMachineIds)->get();

    // Join PreventiveMaintenance with Machine to get related machine details
    $item = PreventiveMaintenance::join('machines', 'preventive_maintenances.machine_id', '=', 'machines.id')
        ->select(
            'preventive_maintenances.*',
            'machines.machine_no',
            'machines.op_no',
            'machines.machine_name'
        )
        ->get();

    // Fetch dropdown values for category
    $dropdown = Dropdown::where('category', 'Category')->get();

    return view('master.preventive.index', compact('item', 'dropdown', 'machines'));
}





    public function store(Request $request) {
        // Validate the request data
        $request->validate([
            'id' => 'required|integer|exists:machines,id', // Validasi machine_id dari request
            'type' => 'required|string',
            'dept' => 'required|string',
            'shop' => 'required|string',
            'no_document' => 'required|string',
            'effective_date' => 'required|date',
            'mfg_date' => 'required|date',
            'revision' => 'nullable|string',
            'no_procedure' => 'nullable|string',
        ]);

        // Check if the machine exists in the database
        $machine = Machine::find($request->id);

        if (!$machine) {
            // Handle the case where the machine does not exist
            return redirect()->back()->with('failed', 'Machine does not exist.');
        }

        // Check if the preventive maintenance record already exists
        $existingRecord = PreventiveMaintenance::where('machine_id', $request->id)
        ->where('type',$request->type)
        ->first();
        if ($existingRecord) {
            // Handle the case where the preventive maintenance record already exists
            return redirect()->back()->with('failed', 'Preventive maintenance record for this machine already exists.');
        }

        // Create a new preventive maintenance record
        PreventiveMaintenance::create([
            'machine_id' => $request->id,
            'type' => $request->type,
            'dept' => $request->dept,
            'shop' => $request->shop,
            'no_document' => $request->no_document,
            'effective_date' => $request->effective_date,
            'mfg_date' => $request->mfg_date,
            'revision' => $request->revision,
            'no_procedure' => $request->no_procedure,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect the user after successfully storing the data
        return redirect()->back()->with('status', 'Preventive maintenance record created successfully.');
    }





    public function detail($id)
{
    $id = decrypt($id);

    // Fetch the machine details by joining PreventiveMaintenance with Machine
    $machine = PreventiveMaintenance::join('machines', 'preventive_maintenances.machine_id', '=', 'machines.id')
        ->select(
            'preventive_maintenances.*',
            'machines.machine_no',
            'machines.op_no',
            'machines.machine_name'
        )
        ->where('preventive_maintenances.id', $id)
        ->first();

    // Fetch the checksheet and checksheet items related to the preventive maintenance ID
    $checksheet = Checksheet::where('preventive_maintenances_id', $id)->get();
    $checksheetItem = ChecksheetItem::where('preventive_maintenances_id', $id)->get();

    // Fetch dropdown values for checksheet types
    $dropdown = Dropdown::where('category', 'ChecksheetType')->get();

    return view('master.preventive.detail', compact('machine', 'checksheet', 'checksheetItem', 'dropdown'));
}


    public function storeChecksheet(Request $request){
        // Validate the request data
        $request->validate([
            'preventive_maintenances_id' => 'required|exists:preventive_maintenances,id',
            'mechine' => 'required|string',
            'type' => 'required|string',
        ]);

        // Check if a checksheet with the specified machine name already exists
        $existingChecksheet = CheckSheet::where('checksheet_category', $request->mechine)->first();

        // Create a new checksheet master record
        $checksheet = new CheckSheet();
        $checksheet->preventive_maintenances_id = $request->preventive_maintenances_id;
        $checksheet->checksheet_category = $request->mechine;
        $checksheet->checksheet_type = $request->type;
        $checksheet->save();

        // Redirect the user after successfully storing the data
        return redirect()->back()->with('status', 'Checksheet items stored successfully');
    }



    public function storeItemChecksheet(Request $request){
        // Dump the request data for debugging
        // dd($request->all());

        // Validate the incoming request data
        $request->validate([
            'preventive_maintenances_id' => 'required|numeric|exists:preventive_maintenances,id', // Validasi preventive_maintenances_id
            'type' => 'required|numeric', // Asumsi type adalah numeric
            'mechine.*' => 'required|string', // Validasi elemen array mechine sebagai string
            'spec.*' => 'required|string' // Validasi elemen array spec sebagai string
        ]);

        // Capitalize the first letter of each word in the 'mechine' and 'spec' arrays
        $mechineFormatted = array_map(function($item) {
            return ucwords(strtolower($item));
        }, $request->mechine);

        $specFormatted = array_map(function($item) {
            return ucwords(strtolower($item));
        }, $request->spec);

        // Check if any of the items already exist in the database
        foreach ($mechineFormatted as $key => $itemName) {
            $existingItem = ChecksheetItem::where('item_name', $itemName)
                ->where('preventive_maintenances_id', $request->preventive_maintenances_id)
                ->where('checksheet_id', $request->type)
                ->first();

            // If the item already exists, throw a validation exception
            if ($existingItem) {
                return redirect()->route('machine.detail', ['id' => encrypt($request->preventive_maintenances_id)])->with('failed', 'The item "'.$itemName.'" already exists.');
            }

            // Create a new checksheet item instance
            $checksheetItem = new ChecksheetItem();

            // Assign values to the checksheet item instance
            $checksheetItem->preventive_maintenances_id = $request->preventive_maintenances_id;
            $checksheetItem->checksheet_id = $request->type; // Asumsi 'type' corresponds to 'checksheet_id'
            $checksheetItem->item_name = $itemName;
            $checksheetItem->spec = $specFormatted[$key]; // Assign item spec

            // Save the checksheet item instance to the database
            $checksheetItem->save();
        }

        // Redirect the user after successfully storing the data
        return redirect()->route('machine.detail', ['id' => encrypt($request->preventive_maintenances_id)])->with('status', 'Checksheet items stored successfully');
    }




        public function deleteChecksheet(Request $request,$id){
            // Delete record from checksheets table
            \DB::table('checksheets')->where('checksheet_id', $id)->delete();

            // Delete records from checksheet_items table
            \DB::table('checksheet_items')->where('checksheet_id', $id)->delete();

            // Redirect back to machine detail route with a success message
            return redirect()->route('machine.detail', ['id' => encrypt($request->preventive_maintenances_id)])->with('status', 'Checksheet and associated items deleted successfully.');
        }

            public function deleteChecksheetItem(Request $request, $id){
            // Delete records from checksheet_items table
            \DB::table('checksheet_items')->where('item_id', $id)->delete();

            // Redirect back to machine detail route with a success message
            return redirect()->route('machine.detail', ['id' => encrypt($request->preventive_maintenances_id)])->with('status', 'Checksheet and associated items deleted successfully.');
            }

            public function updateChecksheet(Request $request, $id)
                {
                    // Find the checksheet by ID
                    $checksheet = Checksheet::findOrFail($id);

                    // Update the checksheet attributes
                    $checksheet->checksheet_category = $request->mechine;
                    $checksheet->checksheet_type = $request->type;

                    // Check if any changes were made to the checksheet
                    if ($checksheet->isDirty()) {
                        // Save the updated checksheet
                        $checksheet->save();

                        // Redirect back with success message
                        return redirect()->route('machine.detail', ['id' => encrypt($request->preventive_maintenances_id)])->with('status', 'Checksheet updated successfully.');
                    } else {
                        // No changes were made to the checksheet
                        return redirect()->back()->with('failed', 'No changes were made to the checksheet.');
                    }
                }

                public function updateChecksheetItem(Request $request, $id) {
                    // Find the checksheet item
                    $checksheetItem = ChecksheetItem::findOrFail($id);

                    if ($checksheetItem->item_name !== $request->mechine || $checksheetItem->checksheet_id != $request->checksheet_id) {
                        $checksheetItem->item_name = $request->mechine;
                        $checksheetItem->checksheet_id = $request->type;
                    }

                    // Check if any changes were made
                    if ($checksheetItem->isDirty()) {
                        // Save changes
                        $checksheetItem->save();

                        // Redirect back with a success message
                        return redirect()->back()->with('status', 'Checksheet item updated successfully.');
                    } else {
                        // No changes were made
                        return redirect()->back()->with('failed', 'No changes were made to the checksheet item.');
                    }
                }

        public function template()
        {
            return Excel::download(new TemplateExport, 'template.xlsx');
        }
        public function upload(Request $request)
{
    // Validate the file upload
    $request->validate([
        'excel-file' => 'required|file|mimes:xlsx,xls,csv',
    ]);

    \Log::info('File uploaded successfully, attempting to process...');

    try {
        // Ensure the file path is correctly retrieved
        Excel::import(new ChecksheetImport, $request->file('excel-file'));

        \Log::info('File imported successfully.');
        return back()->with('status', 'File imported successfully.');
    } catch (\Exception $e) {
        // Log the error message
        \Log::error('Error importing file: ' . $e->getMessage());
        return back()->with('failed', 'Error importing file: ' . $e->getMessage());
    }
}



        public function pmSchedule()
        {
            // Fetching items with details, preventiveMaintenance, and machine details
            $items = PmScheduleMaster::with(['details' => function ($query) {
                $query->orderBy('annual_date'); // Sorting details by date
            }, 'preventiveMaintenance.machine', 'machine']) // Include machine relationship directly
                ->get()
                ->groupBy(function ($item) {
                    // Check if type exists in PmScheduleMaster; if not, fall back to 'Unknown'
                    return $item->type ?? 'Unknown';
                })
                ->map(function ($group) {
                    return $group->groupBy(function ($item) {
                        // Check if preventiveMaintenance exists, and if machine exists within it, else use machine directly from PmScheduleMaster
                        if ($item->preventiveMaintenance && $item->preventiveMaintenance->machine) {
                            return $item->preventiveMaintenance->machine->line ?? 'Unknown';
                        } elseif ($item->machine) {
                            return $item->machine->line ?? 'Unknown';
                        } else {
                            return 'Unknown';
                        }
                    });
                });


            // Define the types available for selection
            $types = ['Mechanic', 'Electric', 'Powerhouse'];

            return view('master.schedule.index', compact('items', 'types'));
        }



        public function fetchPlants($type)
        {
            // Determine the shop value based on the selected type
            $shopValue = ($type === 'Powerhouse') ? 'PH' : 'ME';

            // Fetch distinct plants from machines table based on the shop value
            $plants = DB::table('machines')
                ->where('shop', $shopValue)
                ->distinct()
                ->pluck('plant');

            return response()->json($plants);
        }

        public function fetchShops($type, $plant)
        {
            // Determine the shop value based on the selected type
            $shopValue = ($type === 'Powerhouse') ? 'PH' : 'ME';

            // Fetch distinct shops from machines table based on the shop value and plant
            $shops = DB::table('machines')
                ->where('shop', $shopValue)
                ->where('plant', $plant)
                ->distinct()
                ->pluck('shop');

            return response()->json($shops);
        }

        public function fetchOpNos($type, $plant, $shop)
{
    // Determine the shop value based on the selected type
    $shopValue = ($type === 'Powerhouse') ? 'PH' : 'ME';

    // Fetch distinct OP Nos and machine names from machines table based on the shop value, plant, and shop
    $opNosAndMachines = DB::table('machines')
        ->where('shop', $shopValue)
        ->where('plant', $plant)
        ->where('shop', $shop)
        ->distinct()
        ->select('op_no', 'machine_name')
        ->get();

    // Format the result as an array of strings like "op_no - machine_name"
    $formattedResults = $opNosAndMachines->map(function($machine) {
        return $machine->op_no . ' - ' . $machine->machine_name;
    });

    return response()->json($formattedResults);
}



public function pmScheduleDetail($month)
{
    $items = PmScheduleMaster::with([
        'details' => function ($query) use ($month) {
            $query->whereMonth('annual_date', $month);
        },
        'preventiveMaintenance.machine' // Ensure this relation is eager-loaded
    ])
    ->whereHas('details', function ($query) use ($month) {
        $query->whereMonth('annual_date', $month);
    })
    ->get()
    ->map(function ($item) {
        // Fetch the machine details using machine_id
        $machine = Machine::find($item->machine_id);

        if ($machine) {
            $item->machine_line = $machine->line;
            $item->machine_op_no = $machine->op_no;
            $item->machine_process = $machine->process;
            $item->machine_install_date = $machine->install_date;
        } else {
            $item->machine_line = 'Unknown';
            $item->machine_op_no = 'Unknown';
            $item->machine_process = 'Unknown';
            $item->machine_install_date = 'Unknown';
        }

        return $item;
    })
    ->groupBy(function ($item) {
        return $item->type ?: 'Unknown'; // Use type directly from PmScheduleMaster
    });

    return view('master.schedule.detail', compact('items', 'month'));
}





        public function scheduleTemplate()
    {
        return Excel::download(new ScheduleTemplateExport, 'annual_schedule_template.xlsx');
    }

    public function scheduleUpload(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'excel-file' => 'required|mimes:xlsx,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Import the file using the custom import class
            Excel::import(new ScheduleImport, $request->file('excel-file'));

            DB::commit();

            return redirect()->back()->with('status', 'File imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Return back with all error messages collected
            return redirect()->back()->with('failed', 'Import failed with errors: ' . $e->getMessage());
        }
    }


    public function updateSchedule(Request $request)
{
    $scheduleId = $request->input('schedule_id');
    $annualDates = $request->input('annual_dates');

    $schedule = PmScheduleMaster::find($scheduleId);
    if (!$schedule) {
        return redirect()->back()->with('failed', 'Schedule not found');
    }

    DB::transaction(function () use ($schedule, $annualDates) {
        // Fetch all existing schedule details for the current schedule
        $existingDetails = PmScheduleDetail::where('pm_schedule_master_id', $schedule->id)->get();

        // Update or keep existing details
        foreach ($annualDates as $id => $annualDate) {
            $detail = PmScheduleDetail::find($id);
            if ($detail) {
                $detail->annual_date = $annualDate;
                $detail->save();
            }
        }

        // Identify and delete any existing details that are not in the current request
        foreach ($existingDetails as $detail) {
            if (!in_array($detail->id, array_keys($annualDates))) {
                if (is_null($detail->actual_date) && is_null($detail->checksheet_form_heads_id)) {
                    $detail->delete();
                }
            }
        }
    });

    return redirect()->back()->with('status', 'Schedule updated successfully');
}




public function scheduleStore(Request $request)
{
    // Start a database transaction
    DB::beginTransaction();

    try {
        // Step 1: Validate input data
        $validatedData = $request->validate([
            'type' => 'required|string',
            'plant' => 'required|string',
            'op_no' => 'required|string',
            'frequency' => 'required|integer|min:1|max:12',
            'date' => 'required|integer|min:1|max:31',
        ]);

        // Extract the actual op_no from the combined op_no and machine_name
        $opNoParts = explode(' - ', $request->op_no);
        $op_no = trim($opNoParts[0]);  // Get the operation number part

        // Step 2: Get machine_id from machines table based on plant and op_no
        $machine = DB::table('machines')
            ->where('plant', $request->plant)
            ->where('op_no', $op_no)
            ->first(['id']); // Fetching machine_id

        if (!$machine) {
            return redirect()->back()->with('failed', 'Invalid machine selection. Please try again.');
        }

        $machine_id = $machine->id; // Assign the machine_id

        // Debugging output
        \Log::info('Machine found with ID: ' . $machine_id);

        // Step 3: Attempt to fetch pm_id if available
        $filter = DB::table('pm_filter_view')
            ->where('type', $request->type)
            ->where('plant', $request->plant)
            ->where('op_no', $op_no) // Use the extracted op_no here
            ->first(['pm_id']); // Only pm_id is required now

        $pm_id = $filter->pm_id ?? null; // pm_id can be null now

        // Debugging output
        \Log::info('PM ID fetched: ' . ($pm_id ? $pm_id : 'NULL'));

        // Step 4: Insert into pm_schedule_masters with machine_id and type
        $pmScheduleMasterId = DB::table('pm_schedule_masters')->insertGetId([
            'pm_id' => $pm_id, // pm_id is optional now
            'machine_id' => $machine_id, // Using machine_id from the query
            'type' => $request->type, // type is added
            'frequency' => $request->frequency,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Step 5: Calculate and insert the schedule details
        $frequency = (int)$request->frequency;
        $date = $request->date;
        $year = now()->year;

        $months = [];
        for ($i = 0; $i < 12; $i += $frequency) {
            $month = $i + $frequency;
            if ($month <= 12) {
                $months[] = $month;
            }
        }

        $scheduleDetails = [];
        foreach ($months as $month) {
            $scheduleDetails[] = [
                'pm_schedule_master_id' => $pmScheduleMasterId,
                'annual_date' => \Carbon\Carbon::createFromDate($year, $month, $date)->toDateString(),
                'status' => 'Scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert the schedule details
        DB::table('pm_schedule_details')->insert($scheduleDetails);

        // Commit the transaction
        DB::commit();

        return redirect()->back()->with('status', 'Schedule added successfully.');

    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();

        // Log the error for debugging purposes
        \Log::error('Error adding schedule: ' . $e->getMessage());

        return redirect()->back()->with('failed', 'Failed to add schedule. Please try again.');
    }
}



}
