<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admina') - Lekario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navbar -->
        <nav class="bg-gradient-to-r from-red-600 to-red-700 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="ml-2 text-xl font-bold text-white">Lekario Admin</span>
                        </a>
                        <div class="ml-10 flex space-x-4">
                            <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-red-200 px-3 py-2 rounded-md text-sm font-medium transition">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="text-white hover:text-red-200 px-3 py-2 rounded-md text-sm font-medium transition">
                                Użytkownicy
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-white text-sm mr-4">{{ Auth::user()->full_name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-white hover:text-red-200 text-sm font-medium transition">
                                Wyloguj
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">@yield('page-title')</h1>
                    @if(View::hasSection('page-subtitle'))
                        <p class="mt-1 text-sm text-gray-600">@yield('page-subtitle')</p>
                    @endif
                </div>

                <!-- Flash messages -->
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Page content -->
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>