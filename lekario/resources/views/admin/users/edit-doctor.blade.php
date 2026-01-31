@extends('layouts.admin')

@section('title', 'Edycja lekarza')
@section('page-title', 'Edycja danych lekarza')
@section('page-subtitle', $user->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.show', $user) }}" class="text-red-600 hover:text-red-700">
            ← Powrót do użytkownika
        </a>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Specjalizacje lekarza</h3>

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

            <form action="{{ route('admin.users.update-doctor', $user) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Dane użytkownika (tylko do odczytu) -->
                    <div class="grid grid-cols-2 gap-4 pb-6 border-b border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Imię i nazwisko</label>
                            <input type="text" value="{{ $user->full_name }}" disabled
                                   class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled
                                   class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600">
                        </div>
                    </div>

                    <!-- Specjalizacje -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Specjalizacje *
                        </label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto @error('specialization_ids') border-red-500 @enderror">
                            @foreach($specializations as $spec)
                            <label class="flex items-center py-2 hover:bg-gray-50 px-2 rounded cursor-pointer">
                                <input type="checkbox" 
                                       name="specialization_ids[]" 
                                       value="{{ $spec->id }}"
                                       {{ in_array($spec->id, old('specialization_ids', $user->doctor->specializations->pluck('id')->toArray())) ? 'checked' : '' }}
                                       class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="ml-3 text-sm text-gray-700">{{ $spec->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Wybierz co najmniej jedną specjalizację</p>
                        @error('specialization_ids')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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

        <!-- Informacje dodatkowe -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Informacja</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Zmiana specjalizacji wpłynie na dostępność lekarza w systemie rezerwacji wizyt</li>
                            <li>Obecne wizyty lekarza nie zostaną zmienione</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statystyki lekarza -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statystyki</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ $user->doctor->visits()->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Wszystkie wizyty</p>
                </div>
                <div class="text-center p-4 bg-emerald-50 rounded-lg">
                    <p class="text-2xl font-bold text-emerald-600">{{ $user->doctor->visits()->where('status', 'completed')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Ukończone</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600">{{ $user->doctor->visits()->where('status', 'pending')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Oczekujące</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection