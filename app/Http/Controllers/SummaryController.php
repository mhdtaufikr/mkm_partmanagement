<?php

namespace App\Http\Controllers;

use App\Models\HistoricalProblem;
use App\Models\ChecksheetFormHead;
use App\Models\Machine;
use App\Models\Part;
use App\Models\PreventiveMaintenanceView;
use App\Models\ChecksheetStatusLog;
use App\Models\MachineSparePartsInventoryLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index() {
        // Fetch only necessary historical problems with relationships
        $historicalProblems = HistoricalProblem::with(['machine', 'parent'])
            ->get(); // Fetching only required relationships

        // Fetch preventive maintenance records with necessary joins
        $preventiveMaintenancesQuery = PreventiveMaintenanceView::select(
            'preventive_maintenance_view.id',
            'preventive_maintenance_view.id_ch',
            'preventive_maintenance_view.machine_no',
            'preventive_maintenance_view.op_name',
            'preventive_maintenance_view.machine_name',
            'preventive_maintenance_view.no_document',
            'preventive_maintenance_view.type',
            'preventive_maintenance_view.shop',
            'preventive_maintenance_view.mfg_date',
            'checksheet_form_heads.id as checksheet_id',
            'checksheet_form_heads.planning_date',
            'checksheet_form_heads.actual_date',
            'checksheet_form_heads.pic',
            'checksheet_form_heads.pm_status',
            'checksheet_form_heads.status',
            'checksheet_form_heads.created_by',
            'checksheet_form_heads.remark'
        )
        ->join('checksheet_form_heads', 'preventive_maintenance_view.id', '=', 'checksheet_form_heads.preventive_maintenances_id');

        // Filter based on user roles
        if (Auth::user()->role == "Checker" || Auth::user()->role == "Approval") {
            $preventiveMaintenances = $preventiveMaintenancesQuery->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        } elseif (Auth::user()->role == "user") {
            $preventiveMaintenances = $preventiveMaintenancesQuery->where('checksheet_form_heads.created_by', Auth::user()->name)
                ->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        } else {
            $preventiveMaintenances = $preventiveMaintenancesQuery->orderBy('checksheet_form_heads.created_at', 'desc')->get();
        }

        // Combine the data into a single collection
        $combinedData = collect();

        // Add historical problems to the combined data
        foreach ($historicalProblems as $problem) {
            $hasChildren = $problem->children()->exists(); // Check if the record has children
            $isChild = !is_null($problem->parent_id); // Check if the record is a child

            $combinedData->push((object) [
                'date' => $problem->date,
                'type' => "Daily Report",
                'data' => $problem,
                'Category' => $problem->report,
                'status_logs' => collect(), // Empty collection for consistency in the view
                'flag' => $isChild || $hasChildren // Set flag if the record is a child or has children
            ]);
        }

        // Add preventive maintenance records to the combined data
        foreach ($preventiveMaintenances as $pm) {
            $combinedData->push((object) [
                'date' => $pm->planning_date,
                'type' => 'Preventive Maintenance',
                'data' => $pm,
                'Category' => 'Preventive Maintenance',
                'status_logs' => ChecksheetStatusLog::where('checksheet_header_id', $pm->checksheet_id)->orderBy('created_at', 'desc')->get(),
                'flag' => false // Preventive maintenance does not have parent/child relation
            ]);
        }

        // Sort the combined data by date
        $combinedData = $combinedData->sortBy('date');

        return view('summary.index', compact('combinedData'));
    }

}
