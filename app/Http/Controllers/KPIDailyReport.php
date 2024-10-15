<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Models\HistoricalProblem;

class KPIDailyReport extends Controller
{
    public function index()
    {
        // Fetch distinct report values from the combined_problem_view
        $reports = DB::table('historical_problems') // Make sure the table name is correct
            ->select('report')
            ->distinct()
            ->get()
            ->pluck('report'); // Get the 'report' values as a simple array

        return view('kpi.daily.index', compact('reports')); // Pass reports to the view
    }

    public function getData(Request $request)
{
    if ($request->ajax()) {
        // Query to fetch data from the historical_problems table and join with machines table
        $query = HistoricalProblem::join('machines', 'historical_problems.id_machine', '=', 'machines.id')
            ->select(
                'historical_problems.id',
                DB::raw('CONCAT(machines.op_no, " - ", machines.machine_name) as machine'), // Concatenate op_no and machine_name
                'historical_problems.date',
                'historical_problems.kpi',
                'historical_problems.balance',
                'historical_problems.pic',
                'historical_problems.problem',
                'historical_problems.cause',
                'historical_problems.action',
                'historical_problems.status'
            )
            // Apply sorting by date and start_time in descending order (latest problems first)
            ->orderBy('historical_problems.date', 'desc')
            ->orderBy('historical_problems.start_time', 'desc');

        // Filter by month and year if selected
        if ($request->month) {
            $query->whereMonth('historical_problems.date', $request->month);
        }
        if ($request->year) {
            $query->whereYear('historical_problems.date', $request->year);
        }

        // Filter by report if selected
        if ($request->report) {
            $query->where('historical_problems.report', $request->report);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('problem', function ($row) {
                // Truncate the 'problem' field to a max of 18 characters
                $maxLength = 18;
                if (strlen($row->problem) > $maxLength) {
                    return substr($row->problem, 0, $maxLength) . '...';
                }
                return $row->problem;
            })
            ->editColumn('cause', function ($row) {
                // Truncate the 'cause' field to a max of 18 characters
                $maxLength = 18;
                if (strlen($row->cause) > $maxLength) {
                    return substr($row->cause, 0, $maxLength) . '...';
                }
                return $row->cause;
            })
            ->editColumn('action', function ($row) {
                // Truncate the 'action' field to a max of 18 characters
                $maxLength = 18;
                if (strlen($row->action) > $maxLength) {
                    return substr($row->action, 0, $maxLength) . '...';
                }
                return $row->action;
            })
            ->editColumn('balance', function ($row) {
                return number_format($row->balance, 2);
            })
            ->editColumn('status', function ($row) {
                return ucfirst($row->status);
            })
            ->rawColumns(['problem', 'cause', 'action']) // Ensure the columns render the truncated text correctly
            ->make(true);
    }
}





public function update(Request $request)
{
    // Loop through each row from the request
    foreach ($request->rows as $row) {
        // Check if the record exists in the historical_problems table
        $historicalProblem = HistoricalProblem::find($row['id']);

        if ($historicalProblem) {
            // Update the relevant fields
            $historicalProblem->date = $row['date'];
            $historicalProblem->balance = $row['balance'];

            // If the 'kpi' field is present, update it, otherwise set it to null
            if (isset($row['kpi'])) {
                $historicalProblem->kpi = $row['kpi'];
            } else {
                $historicalProblem->kpi = null;
            }

            // Save the changes to the database
            $historicalProblem->save();
        }
    }

    // Redirect back with a success message
    return redirect()->back()->with('status', 'Records updated successfully.');
}


public function getChildData($id)
{
    // Recursive function to get all descendants
    $getAllChildren = function ($parentId) use (&$getAllChildren) {
        $children = DB::table('historical_problems')
            ->where('parent_id', $parentId)
            ->get();

        foreach ($children as $child) {
            $child->children = $getAllChildren($child->id); // Recursively get the children's children
        }

        return $children;
    };

    // Start by fetching all descendants of the given parent ID
    $childData = $getAllChildren($id);

    return response()->json($childData);
}




}
