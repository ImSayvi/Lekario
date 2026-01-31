@extends('layouts.admin')

@section('title', 'Edycja użytkownika')
@section('page-title', 'Edycja danych użytkownika')
@section('page-subtitle', $user->full_name)

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.users.show', $user) }}" class="text-red-600 hover:text-red-700">
            ← Powrót do użytkownika
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <strong>Błędy walidacji:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <!-- Imię i Nazwisko -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Imię *
                        </label>
                        <input type="text" 
                               name="first_name" 
                               value="{{ old('first_name', $user->first_name) }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('first_name') border-red-500 @enderror">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nazwisko *
                        </label>
                        <input type="text" 
                               name="last_name" 
                               value="{{ old('last_name', $user->last_name) }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('last_name') border-red-500 @enderror">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email *
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Email musi być unikalny w systemie</p>
                </div>

                <!-- Telefon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Telefon
                    </label>
                    <input type="tel" 
                           name="phone" 
                           value="{{ old('phone', $user->phone) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('phone') border-red-500 @enderror"
                           placeholder="np. 123456789">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PESEL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        PESEL
                    </label>
                    <input type="text" 
                           name="pesel" 
                           value="{{ old('pesel', $user->pesel) }}" 
                           maxlength="11"
                           pattern="[0-9]{11}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('pesel') border-red-500 @enderror"
                           placeholder="Wprowadź PESEL (11 cyfr)">
                    @error('pesel')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Dokładnie 11 cyfr. PESEL musi być unikalny.</p>
                </div>

                <!-- Info o roli -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Informacja</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Rola użytkownika: 
                                    @if($user->admin)
                                        <strong>Administrator</strong>
                                    @elseif($user->doctor)
                                        <strong>Lekarz</strong>
                                    @elseif($user->patient)
                                        <strong>Pacjent</strong>
                                    @else
                                        <strong>Brak roli</strong>
                                    @endif
                                </p>
                                @if($user->doctor)
                                <p class="mt-1">Zmiana imienia/nazwiska automatycznie zaktualizuje dane lekarza.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Przyciski -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.show', $user) }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Anuluj
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Zapisz zmiany
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Dodatkowe informacje -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informacje systemowe</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Data rejestracji</p>
                <p class="font-medium text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Ostatnia aktualizacja</p>
                <p class="font-medium text-gray-900">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status konta</p>
                <p class="font-medium text-gray-900">
                    @if($user->status === 'VERIFY')
                        <span class="text-yellow-600">Oczekuje na weryfikację</span>
                    @elseif($user->status === 'ACTIVE')
                        <span class="text-emerald-600">Aktywny</span>
                    @else
                        <span class="text-red-600">Nieaktywny</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">ID użytkownika</p>
                <p class="font-medium text-gray-900">#{{ $user->id }}</p>
            </div>
        </div>
    </div>

    <!-- Ostrzeżenie -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
        <div class="flex">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="ml-3 text-sm text-yellow-700">
                <p class="font-medium">Uwaga:</p>
                <ul class="list-disc list-inside mt-1">
                    <li>Zmiana emaila spowoduje wylogowanie użytkownika</li>
                    <li>PESEL i email muszą być unikalne w systemie</li>
                    <li>Nie można zmienić hasła użytkownika - użytkownik musi zresetować hasło samodzielnie</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection