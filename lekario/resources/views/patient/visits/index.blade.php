@extends('layouts.app')

@section('title', 'Moje Wizyty')
@section('page-title', 'Moje Wizyty')
@section('page-subtitle', 'Przeglądaj i zarządzaj swoimi wizytami')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Statystyki -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Nadchodzące</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Oczekujące</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Ukończone</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Anulowane</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtry -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('patient.visits.index', ['type' => 'upcoming']) }}" 
                   class="px-6 py-3 text-sm font-medium {{ $type === 'upcoming' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Nadchodzące
                </a>
                <a href="{{ route('patient.visits.index', ['type' => 'pending']) }}" 
                   class="px-6 py-3 text-sm font-medium {{ $type === 'pending' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Oczekujące
                </a>
                <a href="{{ route('patient.visits.index', ['type' => 'past']) }}" 
                   class="px-6 py-3 text-sm font-medium {{ $type === 'past' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Historia
                </a>
                <a href="{{ route('patient.visits.index', ['type' => 'cancelled']) }}" 
                   class="px-6 py-3 text-sm font-medium {{ $type === 'cancelled' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Anulowane
                </a>
            </nav>
        </div>
    </div>

    <!-- Lista wizyt -->
    <div class="bg-white rounded-lg shadow">
        @if($visits->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($visits as $visit)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $visit->doctor->user->full_name }}
                                    </h3>
                                    @if($visit->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded">
                                            Oczekuje
                                        </span>
                                    @elseif($visit->status === 'accepted')
                                        <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 rounded">
                                            Potwierdzona
                                        </span>
                                    @elseif($visit->status === 'completed')
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                            Ukończona
                                        </span>
                                    @elseif($visit->status === 'rejected')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                                            Anulowana
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-600 mb-1">
                                    {{ $visit->doctor->specializations->pluck('name')->join(', ') }}
                                </p>

                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($visit->start_time)->format('d.m.Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($visit->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($visit->end_time)->format('H:i') }}
                                    </div>
                                </div>

                                @if($visit->notes)
                                    <div class="mt-2 p-2 bg-gray-50 rounded text-sm text-gray-600">
                                        <p class="font-medium text-gray-700 mb-1">Notatki:</p>
                                        <p class="whitespace-pre-line">{{ $visit->notes }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col space-y-2 ml-4">
                                <a href="{{ route('patient.visits.show', $visit->id) }}" 
                                   class="px-4 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 border border-emerald-600 rounded-lg hover:bg-emerald-50 transition text-center">
                                    Szczegóły
                                </a>

                                @if(in_array($visit->status, ['pending', 'accepted']) && \Carbon\Carbon::parse($visit->start_time)->isFuture())
                                    @php
                                        $hoursUntil = \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($visit->start_time), false);
                                    @endphp
                                    
                                    @if($hoursUntil >= 24)
                                        <form action="{{ route('patient.visits.cancel', $visit->id) }}" method="POST" 
                                              onsubmit="return confirm('Czy na pewno chcesz anulować tę wizytę?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 border border-red-600 rounded-lg hover:bg-red-50 transition">
                                                Anuluj
                                            </button>
                                        </form>
                                    @else
                                        <button disabled 
                                                class="w-full px-4 py-2 text-sm font-medium text-gray-400 border border-gray-300 rounded-lg cursor-not-allowed"
                                                title="Nie można anulować wizyty na mniej niż 24h przed terminem">
                                            Anuluj
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginacja -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $visits->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Brak wizyt</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($type === 'upcoming')
                        Nie masz żadnych zaplanowanych wizyt
                    @elseif($type === 'pending')
                        Nie masz wizyt oczekujących na potwierdzenie
                    @elseif($type === 'past')
                        Nie masz jeszcze żadnych ukończonych wizyt
                    @else
                        Nie masz anulowanych wizyt
                    @endif
                </p>
                @if($type === 'upcoming')
                    <div class="mt-6">
                        <a href="{{ route('visits.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                            Umów wizytę
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection