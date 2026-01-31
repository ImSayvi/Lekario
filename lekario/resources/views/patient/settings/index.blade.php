@extends('layouts.app')

@section('title', 'Ustawienia')
@section('page-title', 'Ustawienia konta')
@section('page-subtitle', 'Zarządzaj swoim profilem i bezpieczeństwem')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Dane osobowe -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xl">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $user->full_name }}</h3>
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
            </div>
        </div>

        <form action="{{ route('settings.profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imię</label>
                        <input type="text" 
                               name="first_name" 
                               value="{{ old('first_name', $user->first_name) }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nazwisko</label>
                        <input type="text" 
                               name="last_name" 
                               value="{{ old('last_name', $user->last_name) }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Numer telefonu</label>
                    <input type="tel" 
                           name="phone" 
                           value="{{ old('phone', $user->phone) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="np. 123456789">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium text-gray-900">{{ $user->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">Nie można zmienić adresu email</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">PESEL</p>
                            <p class="font-medium text-gray-900">{{ $user->pesel ?? 'Brak' }}</p>
                            <p class="text-xs text-gray-500 mt-1">Skontaktuj się z administratorem aby zmienić</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                        Zapisz zmiany
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Zmiana hasła -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Zmiana hasła</h3>
        
        <form action="{{ route('settings.password.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Obecne hasło</label>
                    <input type="password" 
                           name="current_password" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Wprowadź obecne hasło">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nowe hasło</label>
                    <input type="password" 
                           name="password" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Wprowadź nowe hasło">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Hasło musi mieć co najmniej 8 znaków</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Potwierdź nowe hasło</label>
                    <input type="password" 
                           name="password_confirmation" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Wprowadź ponownie nowe hasło">
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="ml-3 text-sm text-yellow-700">
                            <p class="font-medium">Wskazówki dotyczące hasła:</p>
                            <ul class="list-disc list-inside mt-1">
                                <li>Użyj co najmniej 8 znaków</li>
                                <li>Połącz wielkie i małe litery</li>
                                <li>Dodaj cyfry i znaki specjalne</li>
                                <li>Nie używaj popularnych haseł</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                        Zmień hasło
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Informacje o koncie -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informacje o koncie</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Data rejestracji</p>
                <p class="font-medium text-gray-900 mt-1">{{ $user->created_at->format('d.m.Y') }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Ostatnia aktualizacja</p>
                <p class="font-medium text-gray-900 mt-1">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Rola w systemie</p>
                <p class="font-medium text-gray-900 mt-1">Pacjent</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Status konta</p>
                <p class="font-medium text-emerald-600 mt-1">Aktywne</p>
            </div>
        </div>
    </div>

    <!-- Bezpieczeństwo -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Ochrona danych</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Twoje dane są chronione zgodnie z RODO. W przypadku pytań dotyczących danych osobowych, skontaktuj się z administratorem systemu.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection