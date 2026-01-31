@extends('layouts.app')

@section('title', 'Szczegóły wizyty')
@section('page-title', 'Szczegóły wizyty')
@section('page-subtitle', 'Pełne informacje o wizycie')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Przycisk powrotu -->
    <div class="mb-6">
        <a href="{{ route('patient.visits.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Powrót do listy wizyt
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Nagłówek ze statusem -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">
                        Wizyta u {{ $visit->doctor->user->full_name }}
                    </h2>
                    <p class="text-emerald-100">
                        {{ $visit->doctor->specializations->pluck('name')->join(', ') }}
                    </p>
                </div>
                <div>
                    @if($visit->status === 'pending')
                        <span class="px-4 py-2 bg-amber-100 text-amber-800 rounded-full text-sm font-semibold">
                            Oczekuje na potwierdzenie
                        </span>
                    @elseif($visit->status === 'accepted')
                        <span class="px-4 py-2 bg-white text-emerald-600 rounded-full text-sm font-semibold">
                            Potwierdzona
                        </span>
                    @elseif($visit->status === 'completed')
                        <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                            Ukończona
                        </span>
                    @elseif($visit->status === 'rejected')
                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                            Anulowana
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Szczegóły wizyty -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Data i godzina -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900">Data i godzina</h3>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Data:</span>
                            <span class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($visit->start_time)->locale('pl')->isoFormat('dddd, D MMMM YYYY') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Godzina:</span>
                            <span class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($visit->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($visit->end_time)->format('H:i') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Czas trwania:</span>
                            <span class="font-medium text-gray-900">30 minut</span>
                        </div>
                    </div>
                </div>

                <!-- Informacje o lekarzu -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900">Lekarz</h3>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <span class="text-gray-600 block">Imię i nazwisko:</span>
                            <span class="font-medium text-gray-900">{{ $visit->doctor->user->full_name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 block">Specjalizacja:</span>
                            <span class="font-medium text-gray-900">
                                {{ $visit->doctor->specializations->pluck('name')->join(', ') }}
                            </span>
                        </div>
                        @if($visit->doctor->user->email)
                        <div>
                            <span class="text-gray-600 block">Email:</span>
                            <a href="mailto:{{ $visit->doctor->user->email }}" class="font-medium text-emerald-600 hover:text-emerald-700">
                                {{ $visit->doctor->user->email }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informacje dodatkowe -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="ml-4 text-lg font-semibold text-gray-900">Informacje dodatkowe</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Data utworzenia:</span>
                        <span class="font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($visit->created_at)->locale('pl')->isoFormat('D MMMM YYYY, HH:mm') }}
                        </span>
                    </div>
                    @if($visit->updated_at != $visit->created_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ostatnia aktualizacja:</span>
                        <span class="font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($visit->updated_at)->locale('pl')->isoFormat('D MMMM YYYY, HH:mm') }}
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Numer wizyty:</span>
                        <span class="font-medium text-gray-900">#{{ str_pad($visit->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>

            <!-- Notatki -->
            @if($visit->notes)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-8">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-amber-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-amber-900 mb-2">Notatki z wizyty</h3>
                        <p class="text-amber-800 whitespace-pre-line">{{ $visit->notes }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recepty -->
            @if($visit->prescriptions->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="ml-4 text-lg font-semibold text-gray-900">Recepty</h3>
                </div>
                <div class="space-y-4">
                    @foreach($visit->prescriptions as $prescription)
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $prescription->medication_name }}</h4>
                                @if($prescription->medication_code)
                                <p class="text-sm text-gray-600 mb-1">
                                    <span class="font-medium">Kod leku:</span> {{ $prescription->medication_code }}
                                </p>
                                @endif
                                @if($prescription->dosage)
                                <p class="text-sm text-gray-700 mb-2">
                                    <span class="font-medium">Dawkowanie:</span> {{ $prescription->dosage }}
                                </p>
                                @endif
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="text-gray-600">
                                        <span class="font-medium">Ilość:</span> {{ $prescription->quantity }} op.
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $prescription->is_refundable ? 'bg-emerald-200 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $prescription->is_refundable ? 'Refundowane' : 'Pełna odpłatność' }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <svg class="w-16 h-16 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3 pt-3 border-t border-emerald-200">
                            Wystawiono: {{ \Carbon\Carbon::parse($prescription->created_at)->locale('pl')->isoFormat('D MMMM YYYY, HH:mm') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Skierowania -->
            @if($visit->referrals->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="ml-4 text-lg font-semibold text-gray-900">Skierowania</h3>
                </div>
                <div class="space-y-4">
                    @foreach($visit->referrals as $referral)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $referral->referral_to }}</h4>
                                    <span class="ml-3 px-3 py-1 rounded-full text-xs font-semibold bg-blue-200 text-blue-800">
                                        {{ $referral->type === 'examination' ? 'Badanie' : 'Specjalista' }}
                                    </span>
                                </div>
                                @if($referral->diagnosis)
                                <p class="text-sm text-gray-700 mb-2">
                                    <span class="font-medium">Rozpoznanie:</span> {{ $referral->diagnosis }}
                                </p>
                                @endif
                            </div>
                            <div class="ml-4">
                                <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3 pt-3 border-t border-blue-200">
                            Wystawiono: {{ \Carbon\Carbon::parse($referral->created_at)->locale('pl')->isoFormat('D MMMM YYYY, HH:mm') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Akcje -->
            @if(in_array($visit->status, ['pending', 'accepted']) && \Carbon\Carbon::parse($visit->start_time)->isFuture())
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dostępne akcje</h3>
                
                @php
                    $hoursUntil = \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($visit->start_time), false);
                @endphp

                @if($hoursUntil >= 24)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="text-red-900 font-semibold mb-2">Anuluj wizytę</h4>
                                <p class="text-red-700 text-sm mb-4">
                                    Możesz anulować tę wizytę bez konsekwencji, ponieważ pozostało jeszcze więcej niż 24 godziny do jej terminu.
                                    Po anulowaniu będziesz mógł umówić się na inny termin.
                                </p>
                                <form action="{{ route('patient.visits.cancel', $visit->id) }}" method="POST" 
                                      onsubmit="return confirm('Czy na pewno chcesz anulować tę wizytę? Ta operacja jest nieodwracalna.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                                        Anuluj wizytę
                                    </button>
                                </form>
                            </div>
                            <svg class="w-12 h-12 text-red-400 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-100 border border-gray-300 rounded-lg p-6">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-gray-400 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <div>
                                <h4 class="text-gray-700 font-semibold mb-2">Anulowanie niedostępne</h4>
                                <p class="text-gray-600 text-sm">
                                    Nie możesz anulować tej wizyty, ponieważ pozostało mniej niż 24 godziny do jej terminu.
                                    W razie pilnej potrzeby skontaktuj się bezpośrednio z lekarzem.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @elseif($visit->status === 'completed')
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-blue-800">Ta wizyta została zakończona.</p>
                </div>
            </div>
            @elseif($visit->status === 'rejected')
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-red-800">Ta wizyta została anulowana.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection