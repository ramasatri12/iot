@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Dashboard - Home</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-8">
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex flex-col items-center justify-center">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">Tinggi Air</h2>
            {{-- Display latest tinggi_air from controller --}}
            <p class="text-5xl font-bold text-green-600 dark:text-green-400">{{ $currentTinggiAir }} 
                @if($currentTinggiAir !== 'N/A') cm @endif
            </p>
            {{-- Display status for tinggi_air from controller --}}
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $tinggiAirStatus }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex flex-col items-center justify-center">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">Debit Air</h2>
            {{-- Display latest debit_air from controller --}}
            <p class="text-5xl font-bold text-purple-600 dark:text-purple-400">{{ $currentDebitAir }} 
                @if($currentDebitAir !== 'N/A') L/min @endif
            </p>
            {{-- Display status for debit_air from controller --}}
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $debitAirStatus }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-3">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Grafik Tinggi Air</h2>
            <div style="height: 300px;"> {{-- Set a fixed height for the chart container --}}
                <canvas id="waterLevelChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Grafik Debit Air</h2>
            <div style="height: 300px;"> {{-- Set a fixed height for the chart container --}}
                <canvas id="flowRateChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartLabels = JSON.parse('{!! $chartLabels !!}');
    const waterLevelData = JSON.parse('{!! $waterLevelChartData !!}');
    const flowRateData = JSON.parse('{!! $flowRateChartData !!}');

    // --- Grafik Tinggi Air ---
    const waterLevelCtx = document.getElementById('waterLevelChart');
    if (waterLevelCtx) {
        new Chart(waterLevelCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Tinggi Air (cm)',
                    data: waterLevelData,
                    borderColor: 'rgb(34, 197, 94)', 
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    tension: 0.4,
                    fill: true,
                    spanGaps: true // Connect lines over null data points (days with no data)
                }]
            },
            options: {
                responsive: true, // Make chart responsive
                maintainAspectRatio: false, // Allow chart to not maintain aspect ratio, good for fixed height containers
                scales: {
                    y: {
                        beginAtZero: false, // Start y-axis based on data, not necessarily zero
                        // min: 0, // Optional: set a fixed minimum if needed
                        // max: 150, // Optional: set a fixed maximum if needed
                        title: {
                            display: true,
                            text: 'Tinggi Air (cm)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Hari'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    } else {
        console.error("Element with ID 'waterLevelChart' not found.");
    }

    // --- Grafik Debit Air ---
    const flowRateCtx = document.getElementById('flowRateChart');
    if (flowRateCtx) {
        new Chart(flowRateCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Debit Air (L/min)',
                    data: flowRateData,
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.2)',
                    tension: 0.4,
                    fill: true,
                    spanGaps: true // Connect lines over null data points
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        // min: 0, // Optional: set a fixed minimum
                        // max: 70,  // Optional: set a fixed maximum
                        title: {
                            display: true,
                            text: 'Debit Air (L/min)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Hari'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    } else {
        console.error("Element with ID 'flowRateChart' not found.");
    }
</script>
@endpush
