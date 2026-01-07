<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekario - Twoja Przychodnia Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
        
        .hover-scale {
            transition: transform 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: translateY(-5px);
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="ml-2 text-2xl font-bold text-emerald-600">Lekario</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 px-4 py-2 rounded-md font-medium transition">
                        Zaloguj się
                    </a>
                    <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md font-medium transition shadow-md">
                        Zarejestruj się
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg pt-24 pb-20 fade-in">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-white">
                <h1 class="text-5xl font-bold mb-6">
                    Twoje zdrowie w jednym miejscu
                </h1>
                <p class="text-xl mb-8 text-emerald-50 max-w-2xl mx-auto">
                    Zamawiaj wizyty online, sprawdzaj wyniki badań i kontaktuj się z lekarzem - 
                    wszystko to bezpiecznie i wygodnie z domu.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('register') }}" class="bg-white text-emerald-600 px-8 py-3 rounded-md font-semibold hover:bg-gray-100 transition shadow-lg">
                        Załóż konto pacjenta
                    </a>
                    <a href="#jak-to-dziala" class="border-2 border-white text-white px-8 py-3 rounded-md font-semibold hover:bg-white hover:text-emerald-600 transition">
                        Jak to działa?
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="funkcje" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Co możesz zrobić w Lekario?</h2>
                <p class="text-xl text-gray-600">Proste narzędzia do zarządzania Twoim zdrowiem</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-lg hover-scale shadow-sm">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Rezerwuj wizyty online</h3>
                    <p class="text-gray-600">
                        Sprawdź dostępne terminy i umów się na wizytę bez dzwonienia. Otrzymasz przypomnienie SMS przed wizytą.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-lg hover-scale shadow-sm">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Przeglądaj swoją dokumentację</h3>
                    <p class="text-gray-600">
                        Miej dostęp do historii wizyt, wyników badań i otrzymanych recept w każdej chwili i z każdego miejsca.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-lg hover-scale shadow-sm">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Kontakt z lekarzem</h3>
                    <p class="text-gray-600">
                        Zadawaj pytania lekarzowi przez wiadomości. Otrzymuj szybkie odpowiedzi bez czekania w kolejce.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-lg hover-scale shadow-sm">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Powiadomienia i przypomnienia</h3>
                    <p class="text-gray-600">
                        Nie przegap wizyty dzięki automatycznym przypomnieniom. Otrzymuj powiadomienia o gotowych wynikach badań.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-lg hover-scale shadow-sm">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Bezpieczeństwo Twoich danych</h3>
                    <p class="text-gray-600">
                        Twoje dane medyczne są chronione najwyższymi standardami bezpieczeństwa zgodnie z RODO.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-lg hover-scale shadow-sm">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Dostęp z każdego urządzenia</h3>
                    <p class="text-gray-600">
                        Korzystaj z platformy na komputerze, telefonie czy tablecie. Zawsze masz dostęp do swojego zdrowia.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works Section -->
    <section id="jak-to-dziala" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Jak to działa?</h2>
                <p class="text-xl text-gray-600">Zacznij korzystać w 3 prostych krokach</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Zarejestruj się</h3>
                    <p class="text-gray-600">
                        Załóż bezpłatne konto podając podstawowe dane. Zajmie Ci to tylko minutę.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Umów wizytę</h3>
                    <p class="text-gray-600">
                        Wybierz lekarza i dostępny termin. Możesz to zrobić o każdej porze dnia i nocy.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Przyjdź na wizytę</h3>
                    <p class="text-gray-600">
                        Otrzymasz przypomnienie przed wizytą. Po wizycie wszystkie dokumenty będą dostępne w systemie.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="gradient-bg py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">
                Gotowy na wygodniejszą opiekę zdrowotną?
            </h2>
            <p class="text-xl text-emerald-50 mb-8">
                Dołącz do pacjentów, którzy wybrali nowoczesne podejście do zdrowia
            </p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-emerald-600 px-8 py-3 rounded-md font-semibold hover:bg-gray-100 transition shadow-lg">
                Załóż darmowe konto
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="ml-2 text-xl font-bold text-white">Lekario</span>
                    </div>
                    <p class="text-sm">Twoje zdrowie w jednym miejscu</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Dla pacjenta</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#funkcje" class="hover:text-emerald-400 transition">Funkcje</a></li>
                        <li><a href="#jak-to-dziala" class="hover:text-emerald-400 transition">Jak to działa</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-emerald-400 transition">Zarejestruj się</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Pomoc</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-emerald-400 transition">Najczęstsze pytania</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Kontakt</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Instrukcje</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Informacje</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-emerald-400 transition">O przychodni</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Nasi lekarze</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Polityka prywatności</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-sm text-center">
                <p>&copy; 2026 Lekario. Wszystkie prawa zastrzeżone.</p>
            </div>
        </div>
    </footer>
</body>
</html>