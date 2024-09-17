<?php

namespace App\Http\Controllers;

use App\Models\HistoricalProblem;
use App\Models\ChecksheetFormHead;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index()
    {
        // Fetch all historical problems for all machines
        $historicalProblems = HistoricalProblem::with(['children', 'machine'])->get();

        // Fetch all preventive maintenance records from the checksheet_form_heads table
        $preventiveMaintenances = ChecksheetFormHead::with('preventiveMaintenance')->get();

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
                'status_logs' => collect(),
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
                'status_logs' => collect(),
                'flag' => false, // Preventive maintenance does not have parent_id, so no flag
                'checksheet_link' => route('checksheet.detail', $pm->id) // Generate the link for checksheet detail
            ]);
        }

        // Sort the combined data by date
        $combinedData = $combinedData->sortBy('date');

        // Pass the combined data to the view
        return view('summary.index', compact('combinedData'));
    }
}
