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
    // Fetch distinct report values
    $reports = DB::table('combined_problem_view')
        ->select('report')
        ->distinct()
        ->get()
        ->pluck('report'); // Get the 'report' values as a simple array

    return view('kpi.daily.index', compact('reports'));
}


    public function getData(Request $request)
{
    if ($request->ajax()) {
        $query = DB::table('combined_problem_view')
            ->join('machines', 'combined_problem_view.id_machine', '=', 'machines.id')
            ->select([
                'combined_problem_view.id',
                'machines.op_no',
                'machines.machine_name',
                'combined_problem_view.start_date',
                'combined_problem_view.end_date',
                'combined_problem_view.kpi',
                'combined_problem_view.total_balance',
                'combined_problem_view.pic',
                'combined_problem_view.latest_status',
                'combined_problem_view.problem',  // Adding problem
                'combined_problem_view.cause',    // Adding cause
                'combined_problem_view.action',   // Adding action
                'combined_problem_view.created_at',
                'combined_problem_view.updated_at'
            ]);

        // Filter by month and year if selected
        if ($request->month) {
            $query->whereMonth('combined_problem_view.start_date', $request->month);
        }
        if ($request->year) {
            $query->whereYear('combined_problem_view.start_date', $request->year);
        }

        // Filter by report if selected
        if ($request->report) {
            $query->where('combined_problem_view.report', $request->report);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('start_date', function ($row) {
                return \Carbon\Carbon::parse($row->start_date)->format('Y-m-d');
            })
            ->editColumn('end_date', function ($row) {
                return \Carbon\Carbon::parse($row->end_date)->format('Y-m-d');
            })
            ->editColumn('total_balance', function ($row) {
                return number_format($row->total_balance, 2) ;
            })
            ->editColumn('latest_status', function ($row) {
                return ucfirst($row->latest_status);
            })
            ->editColumn('id_machine', function ($row) {
                return $row->op_no . ' - ' . $row->machine_name;
            })
            ->make(true);
    }

}

public function update(Request $request)
{
    foreach ($request->rows as $row) {
        // Find the parent record first
        $parent = HistoricalProblem::find($row['id']);

        // Update Start Date (parent)
        if (isset($row['start_date'])) {
            $parent->date = $row['start_date'];
            $parent->save();
        }

        // Update End Date (latest child)
        if (isset($row['end_date'])) {
            $latestChild = HistoricalProblem::where('parent_id', $row['id'])
                ->orderBy('date', 'desc')
                ->first();
            if ($latestChild) {
                $latestChild->date = $row['end_date'];
                $latestChild->save();
            }
        }

        // Update KPI for parent and all children
        if (isset($row['kpi'])) {
            $parent->kpi = $row['kpi'];
            $parent->save();

            // Update all children with the same KPI
            HistoricalProblem::where('parent_id', $row['id'])->update(['kpi' => $row['kpi']]);
        }

        // Update Balance by Accumulation
        if (isset($row['total_balance'])) {
            // Fetch the entire chain: parent, children, grandchildren, etc.
            $historicalChain = HistoricalProblem::where('id', $row['id']) // parent
                ->orWhere('parent_id', $row['id']) // first-level children
                ->orWhereIn('parent_id', function($query) use ($row) {
                    $query->select('id')
                        ->from('historical_problems')
                        ->where('parent_id', $row['id']); // second-level children and beyond
                })
                ->orderBy('id') // Ensure proper order
                ->get();

            // Total number of records (including parent and all descendants)
            $totalItems = $historicalChain->count();

            // Calculate the balance to assign to each record
            $newBalancePerItem = $row['total_balance'] / $totalItems;

            // Update the balance for each item in the chain
            foreach ($historicalChain as $problem) {
                $problem->balance = $newBalancePerItem;
                $problem->save();
            }
        }


    }

    return redirect()->back()->with('status', 'Records updated successfully.');
}


}
