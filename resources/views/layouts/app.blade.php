<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite('resources/css/app.css')
    {{-- Chart.js sudah dipanggil di home.blade.php, jadi tidak perlu di sini jika hanya untuk home --}}
    {{-- Jika halaman lain juga butuh Chart.js, biarkan di sini atau pindahkan ke stack scripts di halaman tersebut --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script src="//unpkg.com/alpinejs" defer></script> {{-- Alpine.js untuk interaktivitas --}}
    @stack('styles')
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .dark ::-webkit-scrollbar-track {
            background: #2d3748; /* gray-800 */
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #4a5568; /* gray-600 */
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #718096; /* gray-500 */
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <aside 
            class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-50 dark:bg-gray-800 transform transition-transform duration-300 ease-in-out 
                   md:relative md:translate-x-0 md:flex md:flex-col"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
        >
            <div class="flex items-center justify-center h-16 bg-white dark:bg-gray-700 shadow-md px-4">
                <img src="{{ asset('logo-2.png') }}" alt="Logo" class="h-10 mr-2">
                <span class="text-lg font-semibold text-gray-800 dark:text-white">Monitoring Air</span>
            </div>

            <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
                <a href="{{ route('home') }}" 
                   class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-blue-500 hover:text-white {{ request()->routeIs('home') ? 'bg-blue-500 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Home
                </a>
                <a href="{{ route('sensor') }}" 
                   class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-blue-500 hover:text-white {{ request()->routeIs('sensor') ? 'bg-blue-500 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H3V9h2a4 4 0 004-4V3l4 4-4 4v2m0 4h12M3 12h4m4 0h4"></path></svg>
                    Sensor Report
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex items-center justify-between h-16 px-6 bg-white dark:bg-gray-800 shadow-md">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-300 focus:outline-none md:hidden">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6H20M4 12H20M4 18H11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div class="flex-1">
                    {{-- You can add a search bar or other elements here if needed --}}
                </div>

                <button onclick="document.documentElement.classList.toggle('dark')" 
                        class="p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none"
                        aria-label="Toggle dark mode">
                    <svg x-show="!document.documentElement.classList.contains('dark')" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg x-show="document.documentElement.classList.contains('dark')" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-12.66l-.707.707M5.05 18.95l-.707.707M21 12h-1M4 12H3m15.364 6.364l-.707-.707M6.364 5.05l-.707-.707"></path></svg>
                </button>
            </header>

            <main class="flex-1 p-4 md:p-6 overflow-x-hidden overflow-y-auto">
                @yield('content')
            </main>
        </div>
        
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black opacity-50 md:hidden" style="display: none;"></div>

    </div>

    @stack('scripts')
</body>
</html>
