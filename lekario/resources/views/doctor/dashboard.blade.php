@extends('layouts.doctor')

@section('title', 'Panel Lekarza')
@section('page-title', 'Panel główny')
@section('page-subtitle', 'Witaj z powrotem, Dr ' . Auth::user()->last_name)

@section('content')
<!-- Statystyki -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Wizyty dzisiaj -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Wizyty dzisiaj</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $todayVisits }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Oczekujące wizyty -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Oczekujące</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingCount }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Wizyty w tym tygodniu -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Ten tydzień</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $thisWeekVisits }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Wizyty oczekujące na akceptację -->
@if($pendingVisits->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Wizyty do zaakceptowania</h2>
            </div>
            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full">{{ $pendingCount }}</span>
        </div>
    </div>
    
    <div class="divide-y divide-gray-100">
        @foreach($pendingVisits as $visit)
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <!-- Avatar -->
                        <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-semibold text-lg">
                            {{ strtoupper(substr($visit->patient->user->first_name, 0, 1)) }}{{ strtoupper(substr($visit->patient->user->last_name, 0, 1)) }}
                        </div>
                        
                        <!-- Info -->
                        <div>
                            <h3 class="font-semibold text-gray-900 text-lg">{{ $visit->patient->user->full_name }}</h3>
                            <p class="text-sm text-gray-600">PESEL: {{ $visit->patient->pesel }}</p>
                            <p class="text-sm text-gray-600">Telefon: {{ $visit->patient->user->phone ?? 'Brak' }}</p>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($visit->start_time)->format('d.m.Y') }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($visit->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($visit->end_time)->format('H:i') }}
                        </p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 bg-amber-100 text-amber-800">
                            Oczekująca
                        </span>
                    </div>
                </div>

                <!-- Formularz edycji -->
                <form action="{{ route('doctor.visits.update', $visit) }}" method="POST" class="bg-gray-50 rounded-lg p-4 mb-4">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Czas trwania (minuty)</label>
                            <select name="duration" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="15">15 minut</option>
                                <option value="30" selected>30 minut</option>
                                <option value="45">45 minut</option>
                                <option value="60">60 minut</option>
                                <option value="90">90 minut</option>
                                <option value="120">120 minut</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Wiadomość do pacjenta</label>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                Wyślij wiadomość
                            </a>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notatki do wizyty</label>
                        <textarea 
                            name="notes" 
                            rows="3" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Dodaj notatki dotyczące wizyty..."
                        >{{ $visit->notes }}</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button 
                            type="button" 
                            onclick="if(confirm('Czy na pewno chcesz odrzucić tę wizytę?')) { document.getElementById('reject-form-{{ $visit->id }}').submit(); }"
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-medium">
                            Odrzuć
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition font-medium">
                            Zapisz zmiany
                        </button>
                        <button 
                            type="button"
                            onclick="if(confirm('Zaakceptować wizytę?')) { document.getElementById('accept-form-{{ $visit->id }}').submit(); }"
                            class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                            ✓ Zaakceptuj wizytę
                        </button>
                    </div>
                </form>

                <!-- Ukryte formularze dla akcji -->
                <form id="accept-form-{{ $visit->id }}" action="{{ route('doctor.visits.accept', $visit) }}" method="POST" class="hidden">
                    @csrf
                </form>
                
                <form id="reject-form-{{ $visit->id }}" action="{{ route('doctor.visits.reject', $visit) }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Nadchodzące zatwierdzone wizyty -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Nadchodzące wizyty</h2>
            <a href="{{ route('doctor.visits.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Zobacz wszystkie →
            </a>
        </div>
    </div>
    
    <div class="divide-y divide-gray-100">
        @forelse($upcomingVisits as $visit)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-semibold">
                            {{ strtoupper(substr($visit->patient->user->first_name, 0, 1)) }}{{ strtoupper(substr($visit->patient->user->last_name, 0, 1)) }}
                        </div>
                        
                        <!-- Info -->
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $visit->patient->user->full_name }}</h3>
                            <p class="text-sm text-gray-600">PESEL: {{ $visit->patient->pesel }}</p>
                            @if($visit->notes)
                                <p class="text-sm text-gray-500 mt-1 italic">📝 {{ Str::limit($visit->notes, 50) }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($visit->start_time)->format('d.m.Y') }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($visit->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($visit->end_time)->format('H:i') }}
                        </p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 bg-emerald-100 text-emerald-800">
                            Zatwierdzona
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Brak nadchodzących wizyt</h3>
                <p class="text-gray-600">Wszystkie wizyty zostały zrealizowane</p>
            </div>
        @endforelse
    </div>
</div>
@endsection