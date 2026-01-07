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
<body class="bg-gradient-to-br from-emerald-500 to-emerald-600 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-6xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden flex">
        <!-- Left Side - Info -->
        <div class="hidden lg:block lg:w-2/5 gradient-bg p-12 text-white relative">
            <div class="flex flex-col h-full">
                <div class="flex items-center  justify-center mb-8">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <a href="/"><span class="ml-2 text-3xl font-bold">Lekario</span></a>
                </div>
                
                <div class="flex-1 flex flex-col justify-center">
                    <h2 class="text-2xl font-bold mb-4">Zarejestruj konto pacjenta</h2>
                    <p class="text-emerald-50 mb-8 text-m">
                        Utwórz konto, aby zarządzać wizytami i dokumentacją.
                    </p>
                    
                    <div class="flex justify-center mb-8">
                        <div class="w-40 h-40 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-3/5 p-8 lg:p-12">
            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center justify-center mb-8">
                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="ml-2 text-2xl font-bold text-emerald-600">Lekario</span>
            </div>

            <div class="max-w-md mx-auto">
                <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center lg:text-left">Rejestracja</h1>
                <p class="text-gray-600 mb-8 text-center lg:text-left">Wypełnij formularz, aby utworzyć konto</p>

                <form action="{{ route('register') }}" method="POST" class="space-y-4">
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

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input 
                                id="first_name" 
                                name="first_name" 
                                type="text" 
                                required 
                                value="{{ old('first_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="Imię"
                            >
                        </div>
                        
                        <div>
                            <input 
                                id="last_name" 
                                name="last_name" 
                                type="text" 
                                required 
                                value="{{ old('last_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="Nazwisko"
                            >
                        </div>
                    </div>

                    <div>
                        <input 
                            id="phone" 
                            name="phone" 
                            type="tel" 
                            required 
                            value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Numer telefonu"
                        >
                    </div>

                    <div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Adres e-mail"
                        >
                    </div>

                    <div>
                        <input 
                            id="pesel" 
                            name="pesel" 
                            type="text" 
                            required 
                            maxlength="11"
                            value="{{ old('pesel') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="PESEL"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="Hasło"
                            >
                        </div>

                        <div>
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="Potwierdź hasło"
                            >
                        </div>
                    </div>

                    <div class="flex items-start pt-2">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            required
                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded mt-1"
                        >
                        <label for="terms" class="ml-2 text-sm text-gray-700">
                            Akceptuję warunki polityki prywatności
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        Zarejestruj konto
                    </button>

                    <div class="text-center pt-4">
                        <p class="text-sm text-gray-600">
                            Masz już konto?
                            <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 transition">
                                Zaloguj się
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>