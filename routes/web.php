<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorReportController;

Route::get('/', [SensorReportController::class, 'home'])->name('home');
Route::get('/sensor', [SensorReportController::class, 'index'])->name('sensor');
Route::get('/sensor-data', [SensorReportController::class, 'getData'])->name('sensor.data');
