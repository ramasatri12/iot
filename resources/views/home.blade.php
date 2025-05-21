<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <nav class="flex items-center justify-between bg-white dark:bg-gray-800 px-6 py-4 shadow">
        <img src="{{ asset('logo.png') }}" alt="Logo" class="h-12">
        <button onclick="document.documentElement.classList.toggle('dark')" class="text-gray-600 dark:text-gray-300">
            🌙
        </button>
    </nav>

    <div class="flex">
        <aside class="w-48 bg-gray-100 dark:bg-gray-800 min-h-screen p-4">
            <ul class="space-y-4">
                <li><a href="{{ route('home') }}" class="block px-2 py-1 rounded hover:bg-blue-500 hover:text-white">Home</a></li>
                <li><a href="{{ route('sensor') }}" class="block px-2 py-1 rounded hover:bg-blue-500 hover:text-white">Sensor Report</a></li>
            </ul>
        </aside>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>