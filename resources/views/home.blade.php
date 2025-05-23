@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Dashboard - Home</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex flex-col items-center justify-center">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">pH Air</h2>
            <p class="text-5xl font-bold text-blue-600 dark:text-blue-400">7.2</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Normal</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex flex-col items-center justify-center">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">Tinggi Air</h2>
            <p class="text-5xl font-bold text-green-600 dark:text-green-400">120 cm</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Level Optimal</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex flex-col items-center justify-center">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">Debit Air</h2>
            <p class="text-5xl font-bold text-purple-600 dark:text-purple-400">50 L/min</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Aliran Stabil</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Grafik pH Air</h2>
            <canvas id="phChart"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Grafik Tinggi Air</h2>
            <canvas id="waterLevelChart"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Grafik Debit Air</h2>
            <canvas id="flowRateChart"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    const phCtx = document.getElementById('phChart');
    new Chart(phCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai pH',
                data: [7.0, 7.1, 7.3, 7.2, 7.0, 7.4, 7.1],
                borderColor: 'rgb(59, 130, 246)', 
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                tension: 0.4,
                fill: true 
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false, 
            scales: {
                y: {
                    beginAtZero: false, 
                    min: 6.0, 
                    max: 8.0, 
                    title: {
                        display: true,
                        text: 'pH'
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

    const waterLevelCtx = document.getElementById('waterLevelChart');
    new Chart(waterLevelCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tinggi Air (cm)',
                data: [100, 110, 105, 120, 115, 125, 110],
                borderColor: 'rgb(34, 197, 94)', 
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 150, 
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

    // --- Grafik Debit Air ---
    const flowRateCtx = document.getElementById('flowRateChart');
    new Chart(flowRateCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Debit Air (L/min)',
                data: [45, 50, 48, 55, 52, 60, 50],
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 70, 
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
</script>
@endpush