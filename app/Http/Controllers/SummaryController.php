<?php

namespace App\Http\Controllers;

use App\Models\HistoricalProblem;
use App\Models\ChecksheetFormHead;
use App\Models\ChecksheetStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Machine;

class SummaryController extends Controller
{
    public function index(Request $request)
{
    $userPlant = auth()->user()->plant;
    $userType = auth()->user()->type;
    // Handle AJAX request for DataTables
    if ($request->ajax()) {
        $combinedData = collect();

        // 1. Fetch historical problems with necessary relationships
        $historicalProblems = HistoricalProblem::with(['machine', 'parent'])->get();

        // 2. Fetch preventive maintenance records with necessary joins
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
            'machines.op_no as op_name',
            'machines.machine_name',
            'machines.line'
        )
        ->join('preventive_maintenances', 'checksheet_form_heads.preventive_maintenances_id', '=', 'preventive_maintenances.id')
        ->join('machines', 'preventive_maintenances.machine_id', '=', 'machines.id')
        ->orderBy('checksheet_form_heads.actual_date', 'desc') // Sort by actual date for preventive maintenance
        ->orderBy('checksheet_form_heads.created_at', 'desc'); // Secondary sort by creation date

        // Apply role-based filters
        if (Auth::user()->role == "Checker" || Auth::user()->role == "Approval") {
            $preventiveMaintenances = $query->get();
        } elseif (Auth::user()->role == "user") {
            $preventiveMaintenances = $query->where('checksheet_form_heads.created_by', Auth::user()->name)->get();
        } else {
            $preventiveMaintenances = $query->get();
        }

       // 3. Add historical problems to the combined data
foreach ($historicalProblems as $problem) {
    $hasChildren = $problem->children()->exists();
    $isChild = !is_null($problem->parent_id);

    // Get the latest status from the hierarchy of parent-child relationships
    $latestStatus = $this->getLatestStatus($problem);

    $combinedData->push((object) [
        'date' => $problem->date, // Use date from historical problems
        'type' => "Daily Report",
        'data' => $problem,
        'Category' => $problem->report,
        'status' => $latestStatus, // Use the latest status
        'status_logs' => collect(),
        'flag' => $isChild || $hasChildren,
        'balance' => $problem->balance,
        'op_no' => $problem->machine->op_no,
        'line' => $problem->machine->line,
        'start_time' => $problem->start_time ?? '--:--', // Ensure start_time is provided
        'finish_time' => $problem->finish_time ?? '--:--', // Ensure finish_time is provided
    ]);
}

        // 4. Add preventive maintenance records to the combined data
        foreach ($preventiveMaintenances as $pm) {
            $combinedData->push((object) [
                'date' => $pm->actual_date ?? $pm->planning_date, // Use actual date for preventive maintenance
                'type' => 'Preventive Maintenance',
                'data' => $pm,
                'Category' => 'Preventive Maintenance',
                'status' => $pm->status, // Get status from checksheet_form_heads table
                'status_logs' => ChecksheetStatusLog::where('checksheet_header_id', $pm->id)->orderBy('change_date', 'desc')->get(),
                'flag' => false,
                'balance' => false,
                'op_no' => $pm->op_name,
                'line' => $pm->line,
                'start_time' => '--:--', // Preventive maintenance doesn't have start/finish time
                'finish_time' => '--:--', // Preventive maintenance doesn't have start/finish time
            ]);
        }

        // Sort combined data by date and start_time (for daily report entries)
        $sortedData = $combinedData->sort(function ($a, $b) {
            // First sort by date
            $dateA = strtotime($a->date);
            $dateB = strtotime($b->date);

            if ($dateA == $dateB) {
                // If dates are equal, sort by start_time (only for Daily Reports)
                if ($a->type === 'Daily Report' && $b->type === 'Daily Report') {
                    $timeA = strtotime($a->start_time);
                    $timeB = strtotime($b->start_time);
                    return $timeA - $timeB;
                }
            }
            return $dateB - $dateA; // Sort descending by date
        });

        // Handle DataTables request (pagination, search, sorting)
        $filteredData = $sortedData->slice($request->start, $request->length);
        $totalRecords = $sortedData->count();

        $data = [];
        foreach ($filteredData as $key => $item) {
            // Set action button behavior based on the type of the report
            $actionButton = '';
            if ($item->type === 'Daily Report') {
                // If it's a Daily Report, show the modal button
                $actionButton = '<button class="btn btn-sm btn-primary btn-detail" data-id="'.$item->data->id.'" data-bs-toggle="modal" data-bs-target="#modal-detail">Detail</button>';
            } elseif ($item->type === 'Preventive Maintenance') {
                // If it's a Preventive Maintenance record, provide a redirect link
                $actionButton = '<a href="/checksheet/detail/' . encrypt($item->data->id) . '" class="btn btn-sm btn-primary">Detail</a>';
            }

            // Prepare data for each row
            $data[] = [
                'DT_RowIndex' => $key + 1 + $request->start,
                'type' => $item->Category,
                'date' => date('d/m/Y', strtotime($item->date)), // Display the correct date format
                'op_no' => "{$item->op_no} ({$item->data->machine_name})",
                'line' => $item->line,
                'shift' => $item->data->shift ?? '-',
                'shop' => $item->data->shop,
                'problem' => isset($item->data->problem) && strlen($item->data->problem) > 8 ? substr($item->data->problem, 0, 8) . '...' : ($item->data->problem ?? 'Preventive Maintenance'),
                'cause' => isset($item->data->cause) && strlen($item->data->cause) > 8 ? substr($item->data->cause, 0, 8) . '...' : ($item->data->cause ?? 'Preventive Maintenance'),
                'action' => isset($item->data->action) && strlen($item->data->action) > 8 ? substr($item->data->action, 0, 8) . '...' : ($item->data->action ?? 'Preventive Maintenance'),
                'start_time' => "{$item->start_time}", // Display start and finish times
                'finish_time' => "{$item->finish_time}", // Display start and finish times
                'remarks' => isset($item->data->remarks) && strlen($item->data->remarks) > 8 ? substr($item->data->remarks, 0, 8) . '...' : ($item->data->remarks ?? 'OK'),
                'pic' => $item->data->pic ?? 'Hmd. Prod',
                'status' => $item->status, // Already retrieved from either table
                'flag' => $item->flag ? '<i class="fas fa-flag text-info"></i>' : '',
                'balance' => $item->balance ? $item->balance : '-',
                'action' => $actionButton, // Action button logic
            ];
        }

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data,
        ]);
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
        ->orderBy('date', 'asc') // Ensure FIFO ordering by date
        ->get();

        // Filter out chains where any descendant has "OK" status
        $openReports = $openReports->filter(function ($report) {
        return !$this->hasOkInDescendants($report);
        });

        $openPMReports = ChecksheetFormHead::whereIn('pm_status', ['Not Good', 'Temporary'])
        ->with([
            'preventiveMaintenance' => function ($query) {
                $query->with('machine:id,op_no,machine_name'); // Only select necessary fields
            }
        ])
        ->orderBy('planning_date', 'asc') // FIFO ordering by planning date
        ->get();


    return view('summary.index',compact('openReports','openPMReports'));
}



private function getLatestStatus($row)
{
    // Start with the current row
    $latestStatus = $row;

    // Traverse up to the parent if exists
    while ($latestStatus->parent_id !== null) {
        $latestStatus = HistoricalProblem::find($latestStatus->parent_id); // Find the parent
    }

    // Traverse down to the latest child, if any children exist
    while ($latestStatus->children()->exists()) {
        $latestStatus = $latestStatus->children()->latest('id')->first(); // Get the latest child
    }

    // Return the status of the latest in the chain
    return $latestStatus->status;
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


}
