@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Sensor Report</h1>
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
        <table id="sensorTable" class="min-w-full text-left">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tinggi Air</th>
                    <th>Debit</th>
                    <th>Waktu</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#sensorTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("sensor.data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'tinggi_air', name: 'tinggi_air' },
                { data: 'debit', name: 'debit' },
                { data: 'created_at', name: 'created_at' },

            ]
        });
    });
</script>
@endpush