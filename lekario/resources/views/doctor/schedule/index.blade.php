@extends('layouts.doctor')

@section('title', 'Harmonogram')
@section('page-title', 'Harmonogram wizyt')
@section('page-subtitle', 'Kalendarz tygodniowy')

@section('content')
<div class="space-y-6">
    <!-- Nawigacja tygodniowa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('doctor.schedule', ['week' => $previousWeek]) }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    ← Poprzedni tydzień
                </a>
                
                <a href="{{ route('doctor.schedule', ['week' => $currentWeek]) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Bieżący tydzień
                </a>
                
                <a href="{{ route('doctor.schedule', ['week' => $nextWeek]) }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Następny tydzień →
                </a>
            </div>
            
            <div class="text-right">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $startOfWeek->format('d.m.Y') }} - {{ $endOfWeek->format('d.m.Y') }}
                </h3>
                <p class="text-sm text-gray-600">Tydzień {{ $startOfWeek->weekOfYear }}</p>
            </div>
        </div>
    </div>

    <!-- Kalendarz tygodniowy -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-7 divide-x divide-gray-200">
            @foreach($weekDays as $day)
            <div class="min-h-[600px] flex flex-col {{ $day['date']->isToday() ? 'bg-blue-50' : '' }}">
                <!-- Nagłówek dnia -->
                <div class="p-4 border-b border-gray-200 {{ $day['date']->isToday() ? 'bg-blue-100' : 'bg-gray-50' }}">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-600">
                            {{ $day['date']->locale('pl')->dayName }}
                        </p>
                        <p class="text-2xl font-bold {{ $day['date']->isToday() ? 'text-blue-600' : 'text-gray-900' }}">
                            {{ $day['date']->format('d') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $day['date']->locale('pl')->monthName }}
                        </p>
                    </div>
                </div>

                <!-- Wizyty -->
                <div class="flex-1 p-2 space-y-2 overflow-y-auto">
                    @forelse($day['visits'] as $visit)
                    <div class="p-3 rounded-lg border-l-4 
                        @if($visit->status === 'completed')
                            bg-emerald-50 border-emerald-500
                        @elseif($visit->status === 'pending')
                            bg-blue-50 border-blue-500
                        @else
                            bg-red-50 border-red-500
                        @endif
                        hover:shadow-md transition cursor-pointer"
                        onclick="window.location='{{ route('doctor.visits.show', $visit) }}'">
                        
                        <div class="flex items-start justify-between mb-1">
                            <span class="text-xs font-bold
                                @if($visit->status === 'completed')
                                    text-emerald-700
                                @elseif($visit->status === 'pending')
                                    text-blue-700
                                @else
                                    text-red-700
                                @endif">
                                {{ $visit->start_time->format('H:i') }}
                            </span>
                            
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                @if($visit->status === 'completed')
                                    bg-emerald-100 text-emerald-800
                                @elseif($visit->status === 'pending')
                                    bg-blue-100 text-blue-800
                                @else
                                    bg-red-100 text-red-800
                                @endif">
                                @if($visit->status === 'completed')
                                    Ukończona
                                @elseif($visit->status === 'pending')
                                    Oczekuje
                                @else
                                    Anulowana
                                @endif
                            </span>
                        </div>
                        
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $visit->patient->user->full_name }}
                        </p>
                        
                        @if($visit->symptoms)
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                            {{ $visit->symptoms }}
                        </p>
                        @endif
                    </div>
                    @empty
                    <div class="flex items-center justify-center h-full">
                        <p class="text-sm text-gray-400 text-center">
                            Brak wizyt
                        </p>
                    </div>
                    @endforelse
                </div>

                <!-- Liczba wizyt -->
                @if($day['visits']->count() > 0)
                <div class="p-2 border-t border-gray-200 bg-gray-50 text-center">
                    <p class="text-xs font-medium text-gray-600">
                        {{ $day['visits']->count() }} {{ $day['visits']->count() === 1 ? 'wizyta' : ($day['visits']->count() < 5 ? 'wizyty' : 'wizyt') }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Legenda -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Legenda</h3>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Oczekuje</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-emerald-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Ukończona</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Anulowana</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-100 border-2 border-blue-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Dzisiejszy dzień</span>
            </div>
        </div>
    </div>
</div>
@endsection