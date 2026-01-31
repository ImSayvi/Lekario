@extends('layouts.doctor')

@section('title', 'Historia pacjenta')
@section('page-title', 'Historia pacjenta')
@section('page-subtitle', $patient->user->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('doctor.patients.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2">
            <span>&larr;</span> Powrót do listy pacjentów
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl">
                {{ strtoupper(substr($patient->user->first_name, 0, 1)) }}{{ strtoupper(substr($patient->user->last_name, 0, 1)) }}
            </div>
            <div class="ml-4">
                <h3 class="text-xl font-semibold text-gray-900">{{ $patient->user->full_name }}</h3>
                <p class="text-sm text-gray-600">{{ $patient->user->email }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">PESEL</p>
                <p class="font-medium text-gray-900">{{ $patient->user->pesel ?? 'Brak' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Telefon</p>
                <p class="font-medium text-gray-900">{{ $patient->user->phone ?? 'Brak' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Liczba wizyt</p>
                <p class="font-medium text-gray-900">{{ $patient->visits->count() }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Ostatnia wizyta</p>
                <p class="font-medium text-gray-900">
                    {{-- POPRAWKA: Używamy first() na kolekcji zamiast niezdefiniowanej zmiennej $visit --}}
                    @if($patient->visits->first())
                        {{ $patient->visits->first()->start_time->format('d.m.Y H:i') }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Historia wizyt</h3>
        
        @if($patient->visits->count() > 0)
        <div class="space-y-4">
            @foreach($patient->visits as $visit)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-medium text-gray-900">
                                {{-- POPRAWKA: start_time zamiast visit_date --}}
                                {{ $visit->start_time->format('d.m.Y H:i') }}
                            </span>
                            @if($visit->status === 'completed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Ukończona</span>
                            @elseif($visit->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Oczekuje</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Anulowana</span>
                            @endif
                        </div>
                        
                        @if($visit->symptoms)
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Objawy:</strong> {{ $visit->symptoms }}
                        </p>
                        @endif
                        
                        @if($visit->diagnosis)
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Diagnoza:</strong> {{ $visit->diagnosis }}
                        </p>
                        @endif
                        
                        @if($visit->notes)
                        <p class="text-sm text-gray-600">
                            <strong>Notatki:</strong> {{ $visit->notes }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-8">Brak wizyt</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Wystawione recepty</h3>
        @if($prescriptions->count() > 0)
        <div class="space-y-3">
            @foreach($prescriptions as $prescription)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $prescription->medication_name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $prescription->dosage }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $prescription->created_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $prescription->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $prescription->status === 'active' ? 'Aktywna' : 'Wygasła' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Brak wystawionych recept</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Wystawione skierowania</h3>
        @if($referrals->count() > 0)
        <div class="space-y-3">
            @foreach($referrals as $referral)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $referral->specialist_type }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $referral->diagnosis }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $referral->created_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $referral->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $referral->status === 'active' ? 'Aktywne' : 'Wykorzystane' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Brak wystawionych skierowań</p>
        @endif
    </div>
</div>
@endsection