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

            // Fetch data for each month in 2024
        $plannedData = [];
        $actualData = [];
        $trendData = [];

        // Perbaiki format penulisan menjadi sesuai dengan di database
        $shopTypes = array_map(function($type) {
            if ($type == 'power house') {
                return 'PowerHouse'; // Format sesuai dengan di database
            }
            return $type; // Biarkan nilai yang lainnya tetap sama
        }, $shopTypes);

        $year = now()->year;  // Get the current year dynamically

        for ($month = 1; $month <= 12; $month++) {
            $plannedCount = DB::table('pm_schedule_details')
                ->join('pm_schedule_masters', 'pm_schedule_details.pm_schedule_master_id', '=', 'pm_schedule_masters.id')
                ->join('machines', 'pm_schedule_masters.machine_id', '=', 'machines.id')
                ->whereIn('pm_schedule_masters.type', $shopTypes)
                ->where('machines.plant', $plant)
                ->whereMonth('pm_schedule_details.annual_date', $month)
                ->whereYear('pm_schedule_details.annual_date', $year)  // Use the dynamic year here
                ->count();

            $actualCount = DB::table('pm_schedule_details')
                ->join('pm_schedule_masters', 'pm_schedule_details.pm_schedule_master_id', '=', 'pm_schedule_masters.id')
                ->join('machines', 'pm_schedule_masters.machine_id', '=', 'machines.id')
                ->whereIn('pm_schedule_masters.type', $shopTypes)
                ->where('machines.plant', $plant)
                ->whereMonth('pm_schedule_details.actual_date', $month)
                ->whereYear('pm_schedule_details.actual_date', $year)  // Use the dynamic year here
                ->count();

            $plannedData[] = $plannedCount;
            $actualData[] = $actualCount;

            // Calculate trend data (percentage accuracy)
            if ($plannedCount > 0) {
                $trendData[] = min(($actualCount / $plannedCount) * 100, 100);  // Cap at 100%
            } else {
                $trendData[] = 0;
            }
        }
// Fetch data for current month and year
$plannedDataByDay = [];
$actualDataByDay = [];
$trendDataByDay = [];

// Pastikan format shopTypes sesuai dengan yang ada di database
$shopTypesCurrentMonth = array_map(function($type) {
    if ($type == 'power house') {
        return 'PowerHouse'; // Format sesuai dengan yang ada di database
    }
    return $type;
}, $shopTypes);

$currentMonthPM = now()->month; // Bulan saat ini
$currentYearPM = now()->year;   // Tahun saat ini

// Loop untuk setiap hari dalam bulan saat ini (1-31)
for ($day = 1; $day <= 31; $day++) {
    // Hitung jumlah planned untuk hari ke-$day
    $plannedCountDay = DB::table('pm_schedule_details')
        ->join('pm_schedule_masters', 'pm_schedule_details.pm_schedule_master_id', '=', 'pm_schedule_masters.id')
        ->join('machines', 'pm_schedule_masters.machine_id', '=', 'machines.id')
        ->whereIn('pm_schedule_masters.type', $shopTypesCurrentMonth)
        ->where('machines.plant', $plant)
        ->whereDay('pm_schedule_details.annual_date', $day)
        ->whereMonth('pm_schedule_details.annual_date', $currentMonthPM)
        ->whereYear('pm_schedule_details.annual_date', $currentYearPM)
        ->count();

    // Hitung jumlah actual untuk hari ke-$day
    $actualCountDay = DB::table('pm_schedule_details')
        ->join('pm_schedule_masters', 'pm_schedule_details.pm_schedule_master_id', '=', 'pm_schedule_masters.id')
        ->join('machines', 'pm_schedule_masters.machine_id', '=', 'machines.id')
        ->whereIn('pm_schedule_masters.type', $shopTypesCurrentMonth)
        ->where('machines.plant', $plant)
        ->whereDay('pm_schedule_details.actual_date', $day)
        ->whereMonth('pm_schedule_details.actual_date', $currentMonthPM)
        ->whereYear('pm_schedule_details.actual_date', $currentYearPM)
        ->count();

    // Simpan data ke dalam array untuk hari ke-$day
    $plannedDataByDay[] = $plannedCountDay;
    $actualDataByDay[] = $actualCountDay;

    // Hitung data trend (persentase akurasi) untuk hari ke-$day
    if ($plannedCountDay > 0) {
        $trendDataByDay[] = min(($actualCountDay / $plannedCountDay) * 100, 100);  // Batas maksimal 100%
    } else {
        $trendDataByDay[] = 0;
    }
}

        // Pass the data to the view
        return view('home.index', compact('trendDataByDay','plannedDataByDay','actualDataByDay','dailyProblemSummary', 'downtimeByLine', 'problemCountByLine','plant','type','plannedData','actualData','trendData'));
    }




}
