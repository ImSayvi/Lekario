<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Lekario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Form -->
        <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Logo -->
                <div class="text-center">
                    <a href="/" class="inline-flex items-center justify-center">
                        <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="ml-2 text-3xl font-bold text-emerald-600">Lekario</span>
                    </a>
                    <h2 class="mt-6 text-3xl font-bold text-gray-900">
                        Witaj ponownie!
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Zaloguj się do swojego konta
                    </p>
                </div>

                <!-- Login Form -->
                <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            <p class="text-sm">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Adres e-mail
                            </label>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                required 
                                value="{{ old('email') }}"
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="twoj@email.pl"
                            >
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Hasło
                            </label>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Zapamiętaj mnie
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-emerald-600 hover:text-emerald-700 transition">
                                Zapomniałeś hasła?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition shadow-md"
                        >
                            Zaloguj się
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Nie masz jeszcze konta?
                            <a href="{{ route('register') }}" class="font-medium text-emerald-600 hover:text-emerald-700 transition">
                                Zarejestruj się
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side - Image/Gradient -->
        <div class="hidden lg:block lg:flex-1 gradient-bg relative">
            <div class="h-full flex items-center justify-center p-12">
                <div class="max-w-md text-white">
                    <svg class="w-20 h-20 mb-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h3 class="text-3xl font-bold mb-4">
                        Bezpieczny dostęp do Twojego zdrowia
                    </h3>
                    <p class="text-emerald-50 text-lg leading-relaxed">
                        Twoje dane są chronione najwyższymi standardami bezpieczeństwa. 
                        Loguj się spokojnie i zarządzaj swoim zdrowiem online.
                    </p>
                    <div class="mt-8 space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-emerald-50">Szyfrowane połączenie SSL</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-emerald-50">Zgodność z RODO</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-emerald-50">Dostęp 24/7 z każdego urządzenia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>