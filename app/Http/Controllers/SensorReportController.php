<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\SensorReport;


class SensorReportController extends Controller
{
    public function index() {
        return view('sensor');
    }
    
    public function getData() {
        return DataTables::of(SensorReport::select('id', 'tinggi_air', 'ph', 'debit'))->make(true);
    }
}