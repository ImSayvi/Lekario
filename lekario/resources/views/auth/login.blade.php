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
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <!-- Left Side - Branding -->
                <div class="md:w-5/12 gradient-bg p-8 md:p-12 flex flex-col justify-center items-center text-white">
                    <div class="text-center">
                        <svg class="w-20 h-20 mx-auto mb-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <a href="/"><h1 class="text-4xl font-bold mb-4">Lekario</h1></a>
                        <p class="text-emerald-50 text-sm leading-relaxed">
                            Bezpieczny dostęp dla pacjentów i personelu
                        </p>
                        <p class="text-emerald-100 text-xs mt-4 leading-relaxed">
                            Zaloguj się, aby uzyskać dostęp do terminarza, recept i dokumentacji.
                        </p>
                    </div>
                    
                    <div class="mt-12">
                        <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="md:w-7/12 p-8 md:p-12">
                    <div class="max-w-md mx-auto">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">
                            Logowanie do portalu pacjenta
                        </h2>
                        
                        <form class="mt-8 space-y-5" action="{{ route('login') }}" method="POST">
                            @csrf
                            
                            @if ($errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                                    <p>{{ $errors->first() }}</p>
                                </div>
                            @endif

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nazwa użytkownika
                                </label>
                                <input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    required 
                                    value="{{ old('email') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                    placeholder="Login"
                                >
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hasło
                                </label>
                                <input 
                                    id="password" 
                                    name="password" 
                                    type="password" 
                                    required 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                    placeholder="Hasło"
                                >
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center">
                                    <input 
                                        id="remember" 
                                        name="remember" 
                                        type="checkbox" 
                                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                    >
                                    <label for="remember" class="ml-2 text-gray-700">
                                        Zapamiętaj mnie
                                    </label>
                                </div>

                                <a href="{{ route('password.request') }}" class="text-emerald-600 hover:text-emerald-700 transition">
                                    Nie pamiętasz hasła?
                                </a>
                            </div>

                            <button 
                                type="submit" 
                                class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                            >
                                Zaloguj się
                            </button>

                            <div class="text-center text-sm text-gray-600 mt-6">
                                Nie masz konta?
                                <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-medium transition">
                                    Zarejestruj się
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>