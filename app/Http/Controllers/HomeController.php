<?php

namespace App\Http\Controllers;
use DB;

class HomeController extends Controller
{
    public function index($plant, $type)
    {
        // Replace dashes with spaces for consistency
        $type = str_replace('-', ' ', $type);

        // Check if the type is "ME" (indicating both "Mechanic" and "Electric")
        if (strtolower($type) == 'me') {
            $shopTypes = ['Mechanic', 'Electric'];  // Both types for "ME"
        } else {
            $shopTypes = [$type];  // Only the single type provided
        }

        // First query: Fetch daily problem summary for the specific shop(s) and plant
        $dailyProblemSummary = DB::table('combined_problem_view')
            ->whereIn('shop', $shopTypes)  // Use whereIn for multiple shop types
            ->where('plant', $plant)
            ->select(
                DB::raw('DATE(start_date) as date'),
                DB::raw('SUM(total_balance) as total_downtime'),
                DB::raw('COUNT(id) as total_problem_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Second query: Fetch sum of downtime (total_balance) grouped by line
        $downtimeByLine = DB::table('combined_problem_view')
            ->whereIn('shop', $shopTypes)  // Use whereIn for multiple shop types
            ->where('plant', $plant)
            ->select(
                'line',
                DB::raw('SUM(total_balance) as total_downtime')
            )
            ->groupBy('line')
            ->orderBy('line', 'ASC')
            ->get();

        // Third query: Fetch problem count grouped by line
        $problemCountByLine = DB::table('combined_problem_view')
            ->whereIn('shop', $shopTypes)  // Use whereIn for multiple shop types
            ->where('plant', $plant)
            ->select(
                'line',
                DB::raw('COUNT(id) as total_problem_count')
            )
            ->groupBy('line')
            ->orderBy('line', 'ASC')
            ->get();

        // Pass the data to the view
        return view('home.index', compact('dailyProblemSummary', 'downtimeByLine', 'problemCountByLine','plant','type'));
    }




}
