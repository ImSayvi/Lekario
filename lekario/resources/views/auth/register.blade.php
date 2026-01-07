<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja - Lekario</title>
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
        <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
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
                        Załóż konto
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Zacznij zarządzać swoim zdrowiem online
                    </p>
                </div>

                <!-- Register Form -->
                <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Imię
                                </label>
                                <input 
                                    id="first_name" 
                                    name="first_name" 
                                    type="text" 
                                    required 
                                    value="{{ old('first_name') }}"
                                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                    placeholder="Jan"
                                >
                            </div>
                            
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nazwisko
                                </label>
                                <input 
                                    id="last_name" 
                                    name="last_name" 
                                    type="text" 
                                    required 
                                    value="{{ old('last_name') }}"
                                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                    placeholder="Kowalski"
                                >
                            </div>
                        </div>

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
                                placeholder="jan.kowalski@email.pl"
                            >
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Numer telefonu
                            </label>
                            <input 
                                id="phone" 
                                name="phone" 
                                type="tel" 
                                required 
                                value="{{ old('phone') }}"
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="123 456 789"
                            >
                        </div>

                        <div>
                            <label for="pesel" class="block text-sm font-medium text-gray-700 mb-1">
                                PESEL
                            </label>
                            <input 
                                id="pesel" 
                                name="pesel" 
                                type="text" 
                                required 
                                maxlength="11"
                                value="{{ old('pesel') }}"
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="12345678901"
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
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 znaków</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Potwierdź hasło
                            </label>
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                required 
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input 
                                id="terms" 
                                name="terms" 
                                type="checkbox" 
                                required
                                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                            >
                        </div>
                        <div class="ml-2 text-sm">
                            <label for="terms" class="text-gray-700">
                                Akceptuję <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium">regulamin</a> i <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium">politykę prywatności</a>
                            </label>
                        </div>
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition shadow-md"
                        >
                            Zarejestruj się
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Masz już konto?
                            <a href="{{ route('login') }}" class="font-medium text-emerald-600 hover:text-emerald-700 transition">
                                Zaloguj się
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <h3 class="text-3xl font-bold mb-4">
                        Dołącz do nas już dziś
                    </h3>
                    <p class="text-emerald-50 text-lg leading-relaxed mb-8">
                        Rejestracja zajmie Ci tylko chwilę. Po założeniu konta będziesz mógł korzystać ze wszystkich funkcji platformy.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-emerald-50">Umów wizytę w dowolnym momencie</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-emerald-50">Dostęp do dokumentacji medycznej</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-emerald-50">Kontakt online z lekarzem</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-emerald-50">Przypomnienia o wizytach</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>