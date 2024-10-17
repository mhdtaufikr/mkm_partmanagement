<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChecksheetFormHead;
use App\Models\Machine;
use App\Models\Checksheet;
use App\Models\ChecksheetItem;
use Illuminate\Support\Facades\Auth;
use App\Models\ChecksheetFormDetail;
use App\Models\Signature;
use App\Models\Rule;
use App\Models\ChecksheetStatusLog;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\ChecksheetJourneyLog;
use App\Models\PmScheduleDetail;
use App\Models\HistoricalProblem;
use App\Models\PreventiveMaintenanceView;
use App\Models\PreventiveMaintenance;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovalReminder;
use App\Mail\RemandNotification;
use App\Mail\CheckerReminder;
use App\Mail\ChecksheetApprovalNotification;
use DB;
use PDF;
use App\Models\PmFilterView; // Ensure this model is imported

class ChecksheetController extends Controller
{


    public function index(Request $request)
    {
        $userPlant = auth()->user()->plant;
        $userType = auth()->user()->type;
        // Retrieve all distinct types
        $types = PmFilterView::select('type')->distinct()->get();

        // Retrieve unique plants, shops, op_nos, and machine names
        $plants = PmFilterView::select('plant')->distinct()->get();
        $shops = PmFilterView::select('shop')->distinct()->get();
        $opNos = PmFilterView::select('op_no')->distinct()->get();
        $machines = PmFilterView::select('machine_name')->distinct()->get();

        // Main query with checksheet_form_heads as the base table
        $query = ChecksheetFormHead::select(
            'checksheet_form_heads.*',
            'preventive_maintenances.machine_id',
            'preventive_maintenances.no_document',
            'preventive_maintenances.type',
            'preventive_maintenances.dept',
            'preventive_maintenances.shop',
            'preventive_maintenances.effective_date',
            'preventive_maintenances.mfg_date',
            'preventive_maintenances.revision',
            'preventive_maintenances.no_procedure',
            'machines.machine_no',
            'machines.op_no as op_name',
            'machines.machine_name',
            'machines.process',
            'machines.plant',
            'machines.location',
            'machines.line'
        )
        ->join('preventive_maintenances', 'checksheet_form_heads.preventive_maintenances_id', '=', 'preventive_maintenances.id')
        ->join('machines', 'preventive_maintenances.machine_id', '=', 'machines.id')
        ->orderBy('checksheet_form_heads.created_at', 'desc');


        // Apply plant filter if the user's plant is not 'All'
        if ($userPlant != 'All') {
            $query->where('machines.plant', $userPlant);
        }

        // Apply type filter based on user's type
        if ($userType != 'All') {
            if ($userType == 'ME') {
                // If user is ME, include both Mechanic and Electric
                $query->whereIn('preventive_maintenances.type', ['Mechanic', 'Electric']);
            } else {
                // Otherwise, apply the userType directly
                $query->where('preventive_maintenances.type', $userType);
            }
        }

        // Apply role-based filters
        if (Auth::user()->role == "Checker" || Auth::user()->role == "Approval") {
            $items = $query->get();
        } elseif (Auth::user()->role == "user") {
            $items = $query->where('checksheet_form_heads.created_by', Auth::user()->name)->get();
        } else {
            $items = $query->get();
        }



        $logStatus = null; // Initialize the variable before the loop

        // Attach logs and status_logs to each item
        foreach ($items as $item) {
            $item->logs = ChecksheetJourneyLog::where('checksheet_id', $item->id)
                ->orderBy('created_at', 'desc')->get();

            $item->status_logs = ChecksheetStatusLog::where('checksheet_header_id', $item->id)
                ->orderBy('change_date', 'desc')->get();

            // Check if status_logs is not empty before querying HistoricalProblem
            if ($item->status_logs->isNotEmpty()) {
                $logStatus = HistoricalProblem::with(['spareParts.part', 'machine'])
                    ->where('id', $item->status_logs[0]->historical_id)
                    ->first();

                // Attach logStatus to the item
                $item->logStatus = $logStatus;
            } else {
                $item->logStatus = null; // No logStatus if status_logs is empty
            }
        }
        return view('checksheet.index', compact('userPlant','userType','types', 'plants', 'shops', 'opNos', 'machines', 'items', 'logStatus'));
    }

    public function destroy($id)
{
    try {
        // Step 1: Update related records in pm_schedule_details
        PmScheduleDetail::where('checksheet_form_heads_id', $id)
            ->update([
                'status' => 'Scheduled',
                'actual_date' => null,
                'checksheet_form_heads_id' => null
            ]);

        // Step 2: Delete related records in other tables
        ChecksheetFormDetail::where('id_header', $id)->delete();
        ChecksheetJourneyLog::where('checksheet_id', $id)->delete();
        ChecksheetStatusLog::where('checksheet_header_id', $id)->delete();

        // Step 3: Finally, delete the checksheet_form_head record
        ChecksheetFormHead::findOrFail($id)->delete();

        return redirect()->back()->with('status', 'Record deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('failed', 'Failed to delete record: ' . $e->getMessage());
    }
}





    public function getPlants(Request $request)
    {
        $plants = PmFilterView::where(DB::raw('UPPER(type)'), strtoupper($request->input('type')))
            ->select(DB::raw('DISTINCT UPPER(plant) AS plant')) // Select distinct and ensure uppercase
            ->get();

        return response()->json($plants);
    }

    public function getShops(Request $request)
    {
        $shops = PmFilterView::where(DB::raw('UPPER(type)'), strtoupper($request->input('type')))
            ->where(DB::raw('UPPER(plant)'), strtoupper($request->input('plant')))
            ->select(DB::raw('DISTINCT UPPER(line) AS line')) // Ensure uppercase for line
            ->get();

        return response()->json($shops);
    }

    public function getOpNos(Request $request)
    {
        $opNos = PmFilterView::where(DB::raw('UPPER(type)'), strtoupper($request->input('type')))
            ->where(DB::raw('UPPER(plant)'), strtoupper($request->input('plant')))
            ->where(DB::raw('UPPER(line)'), strtoupper($request->input('shop')))
            ->select('op_no', 'machine_name')
            ->distinct()
            ->get();

        return response()->json($opNos);
    }


public function getMachineNames(Request $request)
{
    $machines = PmFilterView::where('type', $request->input('type'))
        ->where('plant', $request->input('plant'))
        ->where('shop', $request->input('shop'))
        ->where('op_no', $request->input('op_no'))
        ->select('machine_name')
        ->distinct()
        ->get();

    return response()->json($machines);
}


public function checksheetScan(Request $request)
{
    if (empty($request->no_mechine)) {
        // Join PreventiveMaintenance with Machine to filter by plant and op_no
        $item = PreventiveMaintenance::join('machines', 'preventive_maintenances.machine_id', '=', 'machines.id')
            ->select('preventive_maintenances.*', 'machines.plant', 'machines.op_no', 'machines.machine_no', 'machines.machine_name', 'machines.process', 'preventive_maintenances.mfg_date', 'preventive_maintenances.shop')
            ->where('machines.plant', $request->plant)
            ->where('machines.op_no', $request->op_no)
            ->first();
    } else {
        // Join PreventiveMaintenance with Machine to filter by machine_no
        $item = PreventiveMaintenance::join('machines', 'preventive_maintenances.machine_id', '=', 'machines.id')
            ->select('preventive_maintenances.*', 'machines.plant', 'machines.op_no', 'machines.machine_no', 'machines.machine_name', 'machines.process', 'preventive_maintenances.mfg_date', 'preventive_maintenances.shop')
            ->where('machines.machine_no', $request->no_mechine)
            ->first();
    }

    $plannedDates = DB::table('pm_schedule_details')
        ->join('pm_schedule_masters', 'pm_schedule_details.pm_schedule_master_id', '=', 'pm_schedule_masters.id')
        ->where('pm_schedule_masters.machine_id', $item->machine_id)
        ->whereNull('pm_schedule_details.actual_date')
        ->select('pm_schedule_details.id', 'pm_schedule_details.annual_date')
        ->get();

    return view('checksheet.form', compact('item', 'plannedDates'));
}




public function storeHeadForm(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'id' => 'required|exists:preventive_maintenances,id',
        'planning_date' => 'required|exists:pm_schedule_details,id', // Validate as the ID in pm_schedule_details
        'actual_date' => 'required|date',
    ]);

    DB::beginTransaction(); // Start the transaction

    try {
        // Create a new instance of ChecksheetFormHead model
        $checksheetHead = new ChecksheetFormHead();

        // Assign values from the request to the model attributes
        $checksheetHead->preventive_maintenances_id = $request->id;
        $checksheetHead->planning_date = DB::table('pm_schedule_details')
                                          ->where('id', $request->planning_date)
                                          ->value('annual_date'); // Get the actual date from the schedule detail
        $checksheetHead->actual_date = $request->actual_date;
        $checksheetHead->status = 0; // Set status to 0
        $checksheetHead->created_by = Auth::user()->name;

        // Save the data to the database
        $checksheetHead->save();

        // Update the pm_schedule_details table with the actual date and checksheet_form_heads_id
        $updateResult = DB::table('pm_schedule_details')
            ->where('id', $request->planning_date)
            ->update([
                'checksheet_form_heads_id' => $checksheetHead->id,
                'actual_date' => $checksheetHead->actual_date,
                'status' => 'Completed', // Assuming status should be updated as well
                'updated_at' => now(),
            ]);

        // If the update fails, throw an exception to trigger the rollback
        if ($updateResult === 0) {
            throw new \Exception('Failed to update PM schedule details');
        }

        // Commit the transaction
        DB::commit();

        // Redirect the user to the 'fill' route with the ID as a parameter
        return redirect()->route('fillForm', ['id' => encrypt($checksheetHead->id)])->with('status', 'Checksheet head form submitted successfully.');

    } catch (\Exception $e) {
        // Rollback the transaction
        DB::rollBack();

        // Redirect back with an error message
        return redirect()->back()->with('failed', 'Transaction failed: ' . $e->getMessage());
    }
}


    public function checksheetfill($id){

        $id = decrypt($id);
        $getformID = ChecksheetFormHead::where('id',$id)->first('preventive_maintenances_id');

        // Fetch the preventive maintenance record based on the given ID
        $preventiveMaintenance = PreventiveMaintenance::findOrFail($getformID->preventive_maintenances_id);

        // Fetch the machine name based on the given machine_id
        $item = Machine::findOrFail($preventiveMaintenance->machine_id);

        // Now, use the preventive_maintenances_id to filter the query
        $results = ChecksheetItem::select(
                'checksheets.checksheet_id',
                'machines.machine_name',
                'checksheet_items.item_name',
                'checksheets.checksheet_category',
                'checksheets.checksheet_type',
                'checksheet_items.spec'
            )
            ->join('checksheets', 'checksheet_items.checksheet_id', '=', 'checksheets.checksheet_id')
            ->join('preventive_maintenances', 'checksheet_items.preventive_maintenances_id', '=', 'preventive_maintenances.id')
            ->join('machines', 'preventive_maintenances.machine_id', '=', 'machines.id')
            ->where('preventive_maintenances.id', $getformID->preventive_maintenances_id)
            ->get();
        // Inisialisasi array untuk menyimpan hasil pengelompokkan
        $groupedResults = [];

        // Perulangan melalui hasil query
        foreach ($results as $result) {
            // Tambahkan hasil ke dalam array berdasarkan checksheet_id
            $groupedResults[$result->checksheet_id][] = [
                'machine_name' => $result->machine_name,
                'item_name' => $result->item_name,
                'checksheet_category' => $result->checksheet_category,
                'checksheet_type' => $result->checksheet_type,
                'spec' => $result->spec,
            ];
        }

        return view('checksheet.fill', compact('results', 'groupedResults', 'id', 'item'));
    }


    public function storeDetailForm(Request $request)
{
    $request->validate([
        'pic' => 'required|string|max:45',
        'remarks' => 'nullable|string|max:255',
        'file_upload' => 'nullable|array',
        'file_upload.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'status' => 'required|string|in:Not Good,OK,Temporary',
        'id_header' => 'required|exists:checksheet_form_heads,id',
        'items' => 'required|array',
        'items.*.checksheet_category' => 'required|string',
        'items.*.checksheet_type' => 'required|string',
    ]);

    DB::beginTransaction();

    try {
        // Handle multiple image uploads if they exist
        $imgPaths = $this->handleMultipleFileUploads($request->file('file_upload'));

        // Get the id header and related data
        $idHeader = $request->input('id_header');
        $items = $request->input('items');
        $pic = $request->input('pic');
        $remarks = $request->input('remarks');
        $pmStatus = $request->input('status');

        // Update header
        $checksheetHead = ChecksheetFormHead::findOrFail($idHeader);
        $checksheetHead->update([
            'pic' => $pic,
            'remark' => $remarks,
            'status' => 1,
            'pm_status' => $pmStatus,
            'img' => json_encode($imgPaths), // Store as JSON array
        ]);

        // Save each item detail
        foreach ($items as $itemName => $itemData) {
            ChecksheetFormDetail::create([
                'id_header' => $idHeader,
                'checksheet_category' => $itemData['checksheet_category'],
                'item_name' => $itemName,
                'checksheet_type' => $itemData['checksheet_type'],
                'act' => $itemData['act'] ?? null,
                'B' => $itemData['B'] ?? 0,
                'R' => $itemData['R'] ?? 0,
                'G' => $itemData['G'] ?? 0,
                'PP' => $itemData['PP'] ?? 0,
                'judge' => $itemData['judge'] ?? null,
                'remarks' => $itemData['remarks'] ?? null,
            ]);
        }

        // Send email notifications if status is 1
        if ($checksheetHead->status == 1) {
            $this->sendEmailNotifications($checksheetHead);
        }

        DB::commit();

        return redirect()->route('machine')->with('status', 'Checksheet submitted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error($e->getMessage());
        return redirect()->back()->with('failed', 'An error occurred while submitting the checksheet. Please try again.');
    }
}

private function handleMultipleFileUploads($files)
{
    $imgPaths = [];

    if ($files) {
        foreach ($files as $file) {
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/img/pm');
            $file->move($destinationPath, $fileName);
            $imgPaths[] = 'assets/img/pm/' . $fileName; // Add the path to the array
        }
    }

    return $imgPaths;
}

private function sendEmailNotifications($checksheetHead)
{
    $getMail = Rule::where('rule_name', 'Checker')->get();
    foreach ($getMail as $mail) {
        Mail::to($mail->rule_value)->send(new CheckerReminder($checksheetHead));
    }
}

    public function checksheetDetail($id) {
        $id = decrypt($id);

       // Retrieve the ChecksheetFormHead along with related PreventiveMaintenance and Machine in one query
$itemHead = ChecksheetFormHead::with(['preventiveMaintenance.machine'])->where('id', $id)->first();

if ($itemHead) {
    // Add the necessary data directly into the ChecksheetFormHead attributes
    $itemHead->setAttribute('op_no', $itemHead->preventiveMaintenance->machine->op_no ?? null);                // From machine
    $itemHead->setAttribute('machine_name', $itemHead->preventiveMaintenance->machine->machine_name ?? null);  // From machine
    $itemHead->setAttribute('no_doc', $itemHead->preventiveMaintenance->no_document ?? null);                  // From PreventiveMaintenance
    $itemHead->setAttribute('process', $itemHead->preventiveMaintenance->machine->process ?? null);            // From machine
    $itemHead->setAttribute('mfg_date', $itemHead->preventiveMaintenance->mfg_date ?? null);                   // From PreventiveMaintenance
    $itemHead->setAttribute('dept', $itemHead->preventiveMaintenance->dept ?? null);                           // From PreventiveMaintenance
    $itemHead->setAttribute('efc_date', $itemHead->preventiveMaintenance->effective_date ?? null);             // From PreventiveMaintenance
    $itemHead->setAttribute('shop', $itemHead->preventiveMaintenance->shop ?? null);                  // From machine
    $itemHead->setAttribute('rev', $itemHead->preventiveMaintenance->revision ?? null);                        // From PreventiveMaintenance

    // No need to reassign already existing attributes like pic, remark, etc.
}


        // Check if itemHead is found
        if (!$itemHead) {
            return redirect()->back()->with('error', 'Checksheet not found.');
        }

        // Retrieve the ChecksheetFormDetails
        $itemDetail = ChecksheetFormDetail::where('id_header', $id)->get();

        // Group item details based on asset categories
        $groupedResults = [];
        foreach ($itemDetail as $detail) {
            // Query the checksheet_items table to get the spec
            $item = ChecksheetItem::where('item_name', $detail->item_name)->first();

            // Add the spec to the detail object
            $detail->spec = $item ? $item->spec : ''; // If item not found, set spec to empty string

            $groupedResults[$detail->checksheet_category][] = $detail;
        }

        // Check if there is a status log for the checksheet
        $logStatus = ChecksheetStatusLog::where('checksheet_header_id', $id)->first();

        // If there is a status log, fetch the historical problem associated with it
        if ($logStatus) {
            $logStatus = HistoricalProblem::with(['spareParts.part', 'machine'])
                ->where('id', $logStatus->historical_id)
                ->first();
        }
        return view('checksheet.detail', compact('itemHead', 'groupedResults', 'logStatus', 'id'));
    }





    public function checksheetSignature(Request $request){
        // Decode the JSON signature data
        $signatures = json_decode($request->signature1);

        // Extract the checksheet ID
        $checksheet_id = $request->checksheet_id;

        // Create a new instance of the Signature model
        $signature = new Signature();

        // Fill the model attributes
        $signature->checksheet_id = $checksheet_id;
        $signature->signature1 = $signatures->signature1;
        $signature->signature2 = $signatures->signature2;
        $signature->signature3 = $signatures->signature3;
        $signature->signature4 = $signatures->signature4;

        // Save the signature to the database
        $signature->save();

        return redirect()->back()->with('status', 'Success Sign Checksheet');

        // Optionally, you can return a response or redirect the user
    }


    public function checksheetApprove($id){
        $id = decrypt($id);
        // Retrieve the ChecksheetFormHead along with related PreventiveMaintenance and Machine in one query
        $itemHead = ChecksheetFormHead::with('preventiveMaintenance.machine')->where('id', $id)->first();

        // Check if itemHead is found
        if (!$itemHead) {
            return redirect()->back()->with('error', 'Checksheet not found.');
        }
        $itemDetail = ChecksheetFormDetail::where('id_header', $id)->get();

        // Group item details based on asset categories
        $groupedResults = [];
        foreach ($itemDetail as $detail) {
            // Query the checksheet_items table to get the spec
            $item = ChecksheetItem::where('item_name', $detail->item_name)->first();

            // Add the spec to the detail object
            $detail->spec = $item ? $item->spec : ''; // If item not found, set spec to empty string

            $groupedResults[$detail->checksheet_category][] = $detail;
        }

        return view('checksheet.approve', compact('itemHead', 'groupedResults', 'id'));
    }

    public function checksheetChecker($id){
        $id = decrypt($id);
      // Retrieve the ChecksheetFormHead along with related PreventiveMaintenance and Machine in one query
        $itemHead = ChecksheetFormHead::with(['preventiveMaintenance.machine'])->where('id', $id)->first();

        if ($itemHead) {
            // Add the necessary data directly into the ChecksheetFormHead attributes
            $itemHead->setAttribute('op_no', $itemHead->preventiveMaintenance->machine->op_no ?? null);                // From machine
            $itemHead->setAttribute('machine_name', $itemHead->preventiveMaintenance->machine->machine_name ?? null);  // From machine
            $itemHead->setAttribute('no_doc', $itemHead->preventiveMaintenance->no_document ?? null);                  // From PreventiveMaintenance
            $itemHead->setAttribute('process', $itemHead->preventiveMaintenance->machine->process ?? null);            // From machine
            $itemHead->setAttribute('mfg_date', $itemHead->preventiveMaintenance->mfg_date ?? null);                   // From PreventiveMaintenance
            $itemHead->setAttribute('dept', $itemHead->preventiveMaintenance->dept ?? null);                           // From PreventiveMaintenance
            $itemHead->setAttribute('efc_date', $itemHead->preventiveMaintenance->effective_date ?? null);             // From PreventiveMaintenance
            $itemHead->setAttribute('shop', $itemHead->preventiveMaintenance->machine->shop ?? null);                  // From machine
            $itemHead->setAttribute('rev', $itemHead->preventiveMaintenance->revision ?? null);                        // From PreventiveMaintenance

            // No need to reassign already existing attributes like pic, remark, etc.
        }



       // Check if itemHead is found
       if (!$itemHead) {
           return redirect()->back()->with('error', 'Checksheet not found.');
       }
        $itemDetail = ChecksheetFormDetail::where('id_header', $id)->get();

        // Group item details based on asset categories
        $groupedResults = [];
        foreach ($itemDetail as $detail) {
            // Query the checksheet_items table to get the spec
            $item = ChecksheetItem::where('item_name', $detail->item_name)->first();

            // Add the spec to the detail object
            $detail->spec = $item ? $item->spec : ''; // If item not found, set spec to empty string

            $groupedResults[$detail->checksheet_category][] = $detail;
        }

        return view('checksheet.checkher', compact('itemHead', 'groupedResults', 'id'));
    }

    public function checksheetApproveStore(Request $request)
{
    $checksheetHeader = ChecksheetFormHead::findOrFail($request->id);
    $getMail = User::where('name', $checksheetHeader->created_by)->first();
    $authUser = Auth::user(); // Get the authenticated user

    switch ($request->approvalStatus) {
        case 'approve':
            $checksheetHeader->status = 4; // Waiting Approval

            // Generate PDF
            $pdf = $this->generatePdfmail($checksheetHeader->id);

            // Send email with PDF to the authenticated user
            Mail::to($authUser->email)
                ->send(new ChecksheetApprovalNotification($checksheetHeader, $pdf));

            break;
        case 'remand':
            $checksheetHeader->status = 3; // Remand
            Mail::to($getMail->email) // Replace with appropriate recipient
                ->send(new RemandNotification($checksheetHeader, $request->remark));
            break;
        default:
            break;
    }
    $checksheetHeader->save();

    $log = new ChecksheetJourneyLog();
    $log->checksheet_id = $request->id;
    $log->user_id = Auth::id();
    $log->action = $checksheetHeader->status;
    $log->remark = $request->remark;
    $log->save();

    return redirect()->route('machine')->with('status', 'Checksheet submitted successfully.');
}

public function generatePdfmail($id)
{
    $checksheetHead = ChecksheetFormHead::find($id);
    $checksheetDetails = ChecksheetFormDetail::where('id_header', $id)
        ->leftJoin('checksheet_items', 'checksheet_form_details.item_name', '=', 'checksheet_items.item_name')
        ->select('checksheet_form_details.*', 'checksheet_items.spec')
        ->get();

    $pdf = PDF::loadView('checksheet.pdf', compact('checksheetHead', 'checksheetDetails'))->setPaper('a4', 'landscape');

    return $pdf;
}



    public function checksheetCheckerStore(Request $request)
    {
        $checksheetHeader = ChecksheetFormHead::findOrFail($request->id);
        $checksheetHead = ChecksheetFormHead::findOrFail($request->id);

        $getMail = User::where('name',$checksheetHeader->created_by)->first();

        switch ($request->approvalStatus) {
            case 'approve':
                $checksheetHeader->status = 2; // Waiting Approval
                $getMail = Rule::where('rule_name', 'Approval')->get();
                foreach ($getMail as $mail) {
                    if ($checksheetHead && $checksheetHead->status == 1) {
                        Mail::to($mail->rule_value)->send(new ApprovalReminder($checksheetHead));
                    }
                }
                break;
            case 'remand':
                $checksheetHeader->status = 3; // Remand
                Mail::to($getMail->email) // Replace with appropriate recipient
                    ->send(new RemandNotification($checksheetHeader, $request->remark));
                break;
            default:
                break;
        }
        $checksheetHeader->save();

        $log = new ChecksheetJourneyLog();
        $log->checksheet_id = $request->id;
        $log->user_id = Auth::id();
        $log->action = $checksheetHeader->status;
        $log->remark = $request->remark;
        $log->save();

        return redirect()->route('machine')->with('status', 'Checksheet submitted successfully.');
    }

    public function checksheetUpdate($id){
        $id = decrypt($id);
       // Retrieve the ChecksheetFormHead along with related PreventiveMaintenance and Machine in one query
       $itemHead = ChecksheetFormHead::with('preventiveMaintenance.machine')->where('id', $id)->first();

       // Check if itemHead is found
       if (!$itemHead) {
           return redirect()->back()->with('error', 'Checksheet not found.');
       }
        $itemDetail = ChecksheetFormDetail::where('id_header', $id)->get();

        // Group item details based on asset categories
        $groupedResults = [];
        foreach ($itemDetail as $detail) {
            // Query the checksheet_items table to get the spec
            $item = ChecksheetItem::where('item_name', $detail->item_name)->first();

            // Add the spec to the detail object
            $detail->spec = $item ? $item->spec : ''; // If item not found, set spec to empty string

            $groupedResults[$detail->checksheet_category][] = $detail;
        }

        return view('checksheet.update', compact('itemHead', 'groupedResults', 'id'));
    }

    public function checksheetUpdateDetail(Request $request)
{
    // Retrieve request data
    $requestData = $request->all();
    $id = $requestData['id'];
    $noDocument = $requestData['no_document'];

    // Update values in the checksheet_form_details table
    foreach ($requestData['items'] as $itemName => $itemData) {
        $detail = ChecksheetFormDetail::where('id_header', $id)
            ->where('item_name', $itemName)
            ->first();
        if ($detail) {
            $detail->update($itemData);
        }
    }

    // Check for changes in checksheet_form_details
    $detailsBeforeUpdate = ChecksheetFormDetail::where('id_header', $id)
        ->pluck('id', 'item_name');

    // Update values in the checksheet_form_heads table if necessary
    $head = ChecksheetFormHead::find($id);

    if ($head) {
        // Prepare an array for fields that need updating
        $updates = [];

        // Update pic and remarks if they have changed
        if ($head->pic !== $requestData['pic']) {
            $updates['pic'] = $requestData['pic'];
        }
        if ($head->remark !== $requestData['remarks']) {
            $updates['remark'] = $requestData['remarks'];
        }

        // Update other fields in checksheet_form_heads if necessary
        $otherFields = ['no_document', 'department', 'shop', 'effective_date', 'revision', 'op_number', 'mfg_date', 'planning_date', 'machine_name', 'process', 'actual_date'];
        foreach ($otherFields as $field) {
            if (isset($requestData[$field]) && $head->$field !== $requestData[$field]) {
                $updates[$field] = $requestData[$field];
            }
        }

        if (!empty($updates)) {
            $head->update($updates);

            // Log changes in checksheet_journey_logs for checksheet_form_heads
            $logData = [
                'checksheet_id' => $id,
                'user_id' => auth()->id(),
                'action' => '5', // Assuming action '5' represents an update
                'remark' => 'Checksheet updated: ' . implode(', ', array_keys($updates)),
            ];
            ChecksheetJourneyLog::create($logData);
        }

        // Update checksheet status to 1 (done)
        $head->update(['status' => 1]);
    }

    // Retrieve emails of users with the "Checker" role and send reminder emails
    $getMail = Rule::where('rule_name', 'Checker')->get();
    foreach ($getMail as $mail) {
        if ($head && $head->status == 1) {
            Mail::to($mail->rule_value)->send(new CheckerReminder($head));
        }
    }

    // Redirect to /checksheet with success message
    return redirect('/checksheet')->with('status', 'Checksheet updated successfully');
}

public function generatePdf($id)
{
    $id = decrypt($id);

    // Retrieve the checksheet head
    $checksheetHead = ChecksheetFormHead::find($id);

    // Retrieve the checksheet details with the spec from the checksheet_items table
    $checksheetDetails = ChecksheetFormDetail::where('id_header', $id)
        ->leftJoin('checksheet_items', 'checksheet_form_details.item_name', '=', 'checksheet_items.item_name')
        ->select('checksheet_form_details.*', 'checksheet_items.spec')
        ->get();

    // Load the view and pass the data to it
    $pdf = PDF::loadView('checksheet.pdf', compact('checksheetHead', 'checksheetDetails'))->setPaper('a4', 'landscape');

    // Return the generated PDF
    return $pdf->download('checksheet_' . $checksheetHead->document_number . '.pdf');
}

public function changeStatus(Request $request)
{
    $get_pm = ChecksheetFormHead::where('id', $request->id_pm)->first();
    $get_pm = PreventiveMaintenance::where('id', $get_pm->preventive_maintenances_id)->first();

    $noMachine = Machine::where('id', $get_pm->machine_id)->first();
    $line = $noMachine->line;
    $shift = $request->shift;
    $no_machine = $noMachine->id;
    $date = $request->date;

   // Get the necessary parameters from the request
   $noMachine = $no_machine;
   // Redirect to the specified URL with the parameters
   return redirect()->route('formStatus', ['no_machine' => encrypt($noMachine), 'date' => $date, 'shift' => $shift, 'id_pm' => $get_pm->id, 'id_checksheet_head' =>$request->id_pm]);
}



}
