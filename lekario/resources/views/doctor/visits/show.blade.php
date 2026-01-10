@extends('layouts.doctor')

@section('title', 'Szczegóły wizyty')
@section('page-title', 'Szczegóły wizyty')
@section('page-subtitle', 'Informacje o wizycie i pacjencie')

@section('content')
<div class="mb-6">
    <a href="{{ route('doctor.visits.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Powrót do listy wizyt
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informacje o wizycie -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Status i data -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Wizyta #{{ $visit->id }}</h2>
                    @if($visit->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Oczekująca
                        </span>
                    @elseif($visit->status === 'accepted')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Zatwierdzona
                        </span>
                    @elseif($visit->status === 'completed')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Zakończona
                        </span>
                    @elseif($visit->status === 'rejected')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Odrzucona
                        </span>
                    @endif
                </div>
                
                @if($visit->status === 'pending')
                    <div class="flex space-x-2">
                        <form action="{{ route('doctor.visits.accept', $visit) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                                ✓ Zaakceptuj
                            </button>
                        </form>
                        <form action="{{ route('doctor.visits.reject', $visit) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz odrzucić tę wizytę?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                                ✗ Odrzuć
                            </button>
                        </form>
                    </div>
                @elseif($visit->status === 'accepted' && \Carbon\Carbon::parse($visit->start_time)->isPast())
                    <form action="{{ route('doctor.visits.complete', $visit) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                            Oznacz jako zakończoną
                        </button>
                    </form>
                @endif
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Data wizyty</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($visit->start_time)->locale('pl')->isoFormat('dddd, D MMMM YYYY') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Godzina</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($visit->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($visit->end_time)->format('H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Czas trwania</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($visit->start_time)->diffInMinutes(\Carbon\Carbon::parse($visit->end_time)) }} minut
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Utworzono</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($visit->created_at)->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Notatki -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Notatki do wizyty</h3>
            
            @if($visit->status === 'pending' || $visit->status === 'accepted')
                <form action="{{ route('doctor.visits.update', $visit) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="duration" value="{{ \Carbon\Carbon::parse($visit->start_time)->diffInMinutes(\Carbon\Carbon::parse($visit->end_time)) }}">
                    <textarea 
                        name="notes" 
                        rows="6" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Dodaj notatki dotyczące wizyty, rozpoznanie, zalecenia..."
                    >{{ $visit->notes }}</textarea>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Zapisz notatki
                        </button>
                    </div>
                </form>
            @else
                @if($visit->notes)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $visit->notes }}</p>
                    </div>
                @else
                    <p class="text-gray-500 italic">Brak notatek do tej wizyty</p>
                @endif
            @endif
        </div>
    </div>
    
    <!-- Informacje o pacjencie -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Informacje o pacjencie</h3>
            
            <div class="flex items-center mb-6">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-xl mr-4">
                    {{ strtoupper(substr($visit->patient->user->first_name, 0, 1)) }}{{ strtoupper(substr($visit->patient->user->last_name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-lg">{{ $visit->patient->user->full_name }}</h4>
                    <p class="text-sm text-gray-600">Pacjent</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 mb-1">PESEL</p>
                    <p class="font-medium text-gray-900">{{ $visit->patient->pesel }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600 mb-1">Telefon</p>
                    <p class="font-medium text-gray-900">{{ $visit->patient->user->phone ?? 'Brak' }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600 mb-1">Email</p>
                    <p class="font-medium text-gray-900">{{ $visit->patient->user->email }}</p>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-100">
                <a href="#" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Wyślij wiadomość
                </a>
            </div>
        </div>
        
        <!-- Historia wizyt -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Historia wizyt</h3>
            
            @php
                $patientVisits = \App\Models\Visit::where('patient_id', $visit->patient_id)
                    ->where('doctor_id', auth()->user()->doctor->id)
                    ->where('id', '!=', $visit->id)
                    ->orderBy('start_time', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            
            @if($patientVisits->count() > 0)
                <div class="space-y-3">
                    @foreach($patientVisits as $pastVisit)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($pastVisit->status === 'completed')
                                    <div class="w-2 h-2 mt-2 bg-emerald-500 rounded-full"></div>
                                @else
                                    <div class="w-2 h-2 mt-2 bg-gray-300 rounded-full"></div>
                                @endif
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($pastVisit->start_time)->format('d.m.Y H:i') }}
                                </p>
                                <p class="text-xs text-gray-600">{{ ucfirst($pastVisit->status) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Brak wcześniejszych wizyt</p>
            @endif
        </div>
    </div>
</div>
@endsection