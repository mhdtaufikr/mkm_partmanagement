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
        // Retrieve all distinct types
        $types = PmFilterView::select('type')->distinct()->get();

        // Retrieve unique plants, shops, op_nos, and machine names
        $plants = PmFilterView::select('plant')->distinct()->get();
        $shops = PmFilterView::select('shop')->distinct()->get();
        $opNos = PmFilterView::select('op_no')->distinct()->get();
        $machines = PmFilterView::select('machine_name')->distinct()->get();

        $query = PreventiveMaintenanceView::select(
            'preventive_maintenance_view.id',
            'preventive_maintenance_view.id_ch',
            'preventive_maintenance_view.machine_id',
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
            'checksheet_form_heads.status',
            'checksheet_form_heads.pm_status',
            'checksheet_form_heads.created_by',
            'checksheet_form_heads.remark',
            'checksheet_form_heads.created_at as checksheet_created_at',
            'checksheet_form_heads.updated_at as checksheet_updated_at'
        )
        ->join('checksheet_form_heads', 'preventive_maintenance_view.id', '=', 'checksheet_form_heads.preventive_maintenances_id');


        // Apply role-based filters
        if (Auth::user()->role == "Checker" || Auth::user()->role == "Approval") {
            $items = $query->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        } elseif (Auth::user()->role == "user") {
            $items = $query->where('checksheet_form_heads.created_by', Auth::user()->name)->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        } else {
            $items = $query->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        }

        // Attach logs and status_logs to each item
        foreach ($items as $item) {
            $item->logs = ChecksheetJourneyLog::where('checksheet_id', $item->checksheet_id)
                ->orderBy('created_at', 'desc')->get();

            $item->status_logs = ChecksheetStatusLog::where('checksheet_header_id', $item->checksheet_id)
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


        return view('checksheet.index', compact('types', 'plants', 'shops', 'opNos', 'machines', 'items','logStatus'));
    }



public function getPlants(Request $request)
{
    $plants = PmFilterView::where('type', $request->input('type'))
        ->select('plant')
        ->distinct()
        ->get();

    return response()->json($plants);
}

public function getShops(Request $request)
{
    $shops = PmFilterView::where('type', $request->input('type'))
        ->where('plant', $request->input('plant'))
        ->select('shop')
        ->distinct()
        ->get();

    return response()->json($shops);
}

public function getOpNos(Request $request)
{
    $opNos = PmFilterView::where('type', $request->input('type'))
        ->where('plant', $request->input('plant'))
        ->where('shop', $request->input('shop'))
        ->select('op_no')
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


public function checksheetScan(Request $request){
    if (empty($request->no_mechine)) {
        $item = PreventiveMaintenanceView::where('plant', $request->plant)
        ->where('op_name', $request->op_no)
        ->first();
    }else {
        $item = PreventiveMaintenanceView::where('machine_no', $request->no_mechine)->first();
    }

    $plannedDates = DB::table('pm_schedule_details')
    ->join('pm_schedule_masters', 'pm_schedule_details.pm_schedule_master_id', '=', 'pm_schedule_masters.id')
    ->where('pm_schedule_masters.pm_id', $item->id)
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
        // Validate the incoming request data
        $request->validate([
            'pic' => 'required|string|max:45',
            'remarks' => 'nullable|string|max:255',
            'file_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|string|in:Open,Close',
            'id_header' => 'required|exists:checksheet_form_heads,id',
            'items' => 'required|array',
            'items.*.checksheet_category' => 'required|string',
            'items.*.checksheet_type' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Handle the image upload if it exists
            $imgPath = null;
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('assets/img/pm');
                $file->move($destinationPath, $fileName);
                $imgPath = 'assets/img/pm/' . $fileName;
            }

            // Get the id header
            $idHeader = $request->input('id_header');

            // Get the items to be saved
            $items = $request->input('items');

            // Get the values for pic and remarks from the request
            $pic = $request->input('pic');
            $remarks = $request->input('remarks');
            $pmStatus = $request->input('status');

            // Update pic, remarks, pm_status, and img for the header id
            $checksheetHead = ChecksheetFormHead::find($idHeader);
            if ($checksheetHead) {
                $checksheetHead->pic = $pic;
                $checksheetHead->remark = $remarks;
                $checksheetHead->status = 1; // Update status to 1
                $checksheetHead->pm_status = $pmStatus; // Update pm_status
                $checksheetHead->img = $imgPath; // Update the image path
                $checksheetHead->save();
            }

            // Save each item to the table
            foreach ($items as $itemName => $itemData) {
                $checksheetDetail = new ChecksheetFormDetail();
                $checksheetDetail->id_header = $idHeader;
                $checksheetDetail->checksheet_category = $itemData['checksheet_category']; // Add checksheet category
                $checksheetDetail->item_name = $itemName;
                $checksheetDetail->checksheet_type = $itemData['checksheet_type'];
                $checksheetDetail->act = $itemData['act'] ?? null;
                $checksheetDetail->B = isset($itemData['B']) ? $itemData['B'] : 0;
                $checksheetDetail->R = isset($itemData['R']) ? $itemData['R'] : 0;
                $checksheetDetail->G = isset($itemData['G']) ? $itemData['G'] : 0;
                $checksheetDetail->PP = isset($itemData['PP']) ? $itemData['PP'] : 0;
                $checksheetDetail->judge = $itemData['judge'] ?? null;
                $checksheetDetail->remarks = $itemData['remarks'] ?? null;
                $checksheetDetail->save();
            }

            // Send email notifications if status is 1
            $getMail = Rule::where('rule_name', 'Checker')->get();
            foreach ($getMail as $mail) {
                if ($checksheetHead && $checksheetHead->status == 1) {
                    Mail::to($mail->rule_value)->send(new CheckerReminder($checksheetHead));
                }
            }

            // Commit the transaction if everything is successful
            DB::commit();

            // Redirect with success message
            return redirect()->route('machine')->with('status', 'Checksheet submitted successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            return redirect()->back()->with('failed', 'An error occurred while submitting the checksheet. Please try again.');
        }
    }




    public function checksheetDetail($id) {
        $id = decrypt($id);

        // Retrieve the ChecksheetFormHead along with related PreventiveMaintenance and Machine in one query
        $itemHead = ChecksheetFormHead::with('preventiveMaintenance.machine')->where('id', $id)->first();

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

        return view('checksheet.detail', compact('itemHead', 'groupedResults', 'id'));
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
        $itemHead = ChecksheetFormHead::where('id', $id)->first();
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
        $itemHead = ChecksheetFormHead::where('id', $id)->first();
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
        $itemHead = ChecksheetFormHead::where('id', $id)->first();
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
    $get_pm = PreventiveMaintenance::where('id', $request->id_pm)->first();

    $noMachine = Machine::where('id', $get_pm->machine_id)->first();
    $line = $noMachine->line;
    $shift = $request->shift;
    $no_machine = $noMachine->id;
    $date = $request->date;

   // Get the necessary parameters from the request
   $noMachine = $no_machine;
   // Redirect to the specified URL with the parameters
   return redirect()->route('formStatus', ['no_machine' => encrypt($noMachine), 'date' => $date, 'shift' => $shift, 'id_pm' => $request->id_pm, 'id_checksheet_head' => $request->checksheet_id]);
}



}
