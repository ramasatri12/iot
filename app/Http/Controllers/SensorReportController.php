<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\SensorReport;
use Carbon\Carbon;
use Carbon\CarbonInterface; 
class SensorReportController extends Controller
{
    /**
     * Display the home dashboard.
     * Fetches latest sensor data and weekly chart data.
     */
    public function home()
    {
        $latestReport = SensorReport::latest('created_at')->first();

        $currentTinggiAir = null;
        $currentDebitAir = null;
        $tinggiAirStatus = 'Data tidak tersedia';
        $debitAirStatus = 'Data tidak tersedia';

        if ($latestReport) {
            $currentTinggiAir = $latestReport->tinggi_air;
            $currentDebitAir = $latestReport->debit;

            if ($currentTinggiAir > 110 && $currentTinggiAir < 130) {
                $tinggiAirStatus = 'Level Optimal';
            } elseif ($currentTinggiAir <= 110) {
                $tinggiAirStatus = 'Level Rendah';
            } else {
                $tinggiAirStatus = 'Level Tinggi';
            }

            if ($currentDebitAir > 45 && $currentDebitAir < 55) {
                $debitAirStatus = 'Aliran Stabil';
            } elseif ($currentDebitAir <= 45) {
                $debitAirStatus = 'Aliran Lambat';
            } else {
                $debitAirStatus = 'Aliran Deras';
            }
        }

        $chartData = $this->prepareWeeklyChartData();

        return view('home', [
            'currentTinggiAir' => $currentTinggiAir ?? 'N/A',
            'currentDebitAir' => $currentDebitAir ?? 'N/A',
            'tinggiAirStatus' => $tinggiAirStatus,
            'debitAirStatus' => $debitAirStatus,
            'chartLabels' => json_encode($chartData['labels']),
            'waterLevelChartData' => json_encode($chartData['waterLevels']), 
            'flowRateChartData' => json_encode($chartData['flowRates']), 
        ]);
    }

    /**
     * Prepare aggregated data for the weekly charts.
     * Calculates average sensor readings for each day of the current week (Mon-Sun).
     * @return array
     */
    private function prepareWeeklyChartData()
    {
        $labels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $waterLevels = array_fill(0, 7, null);
        $flowRates = array_fill(0, 7, null);

        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $dailyAvgData = SensorReport::selectRaw('WEEKDAY(created_at) as day_index, AVG(tinggi_air) as avg_tinggi, AVG(debit) as avg_debit')
            ->whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->groupBy('day_index') 
            ->orderBy('day_index', 'asc') 
            ->get();

        foreach ($dailyAvgData as $data) {
            if (isset($labels[$data->day_index])) {
                $waterLevels[$data->day_index] = round($data->avg_tinggi, 2);
                $flowRates[$data->day_index] = round($data->avg_debit, 2);
            }
        }

        return [
            'labels' => $labels,
            'waterLevels' => $waterLevels,
            'flowRates' => $flowRates,
        ];
    }

    /**
     * Display the sensor data table page.
     */
    public function index()
    {
        return view('sensor'); 
    }

    /**
     * Provide data for the DataTables on the sensor page.
     */
    public function getData()
    {
                $query = SensorReport::select('id', 'tinggi_air', 'created_at', 'debit');

        return DataTables::of($query)
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s');
            })
            ->rawColumns(['created_at']) 
            ->make(true);

    }
}
