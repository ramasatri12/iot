@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Dashboard - Home</h1>
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
        <canvas id="sensorChart" height="120"></canvas>
    </div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('sensorChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr'],
            datasets: [{
                label: 'Tinggi Air',
                data: [10, 20, 15, 30],
                borderColor: 'rgb(59, 130, 246)',
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush