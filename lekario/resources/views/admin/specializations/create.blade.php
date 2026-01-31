@extends('layouts.admin')

@section('title', 'Dodaj specjalizację')
@section('page-title', 'Dodaj nową specjalizację')
@section('page-subtitle', 'Utwórz nową specjalizację dla lekarzy')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.specializations.index') }}" class="text-red-600 hover:text-red-700">
            ← Powrót do listy
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

        <form action="{{ route('admin.specializations.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Nazwa -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nazwa specjalizacji *
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror"
                           placeholder="np. Kardiologia, Pediatria">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Nazwa musi być unikalna</p>
                </div>

               

                <!-- Przyciski -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.specializations.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Anuluj
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Dodaj specjalizację
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Info box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Informacja</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Specjalizacje są przypisywane do lekarzy podczas tworzenia lub edycji ich profilu</li>
                        <li>Pacjenci mogą filtrować lekarzy według specjalizacji podczas rezerwacji wizyt</li>
                        <li>Nazwa specjalizacji musi być unikalna w systemie</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection