<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class KPIDailyReport extends Controller
{
    public function index()
    {
        return view('kpi.daily.index');
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
                    'combined_problem_view.report',
                    'combined_problem_view.start_date',
                    'combined_problem_view.end_date',
                    'combined_problem_view.kpi',
                    'combined_problem_view.category',
                    'combined_problem_view.total_balance',
                    'combined_problem_view.pic',
                    'combined_problem_view.latest_status',
                    'combined_problem_view.created_at',
                    'combined_problem_view.updated_at'
                ]);

            // Filter by month if a month is selected
            if ($request->month) {
                $query->whereMonth('combined_problem_view.start_date', $request->month);
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
                    return number_format($row->total_balance, 2);
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




}
