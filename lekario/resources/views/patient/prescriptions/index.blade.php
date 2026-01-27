@extends('layouts.app')

@section('title', 'Moje recepty')
@section('page-title', 'Moje recepty')
@section('page-subtitle', 'Historia wystawionych recept')

@section('content')
<div class="space-y-6">
    <!-- Statystyki -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Wszystkie recepty</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Refundowane</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['refundable'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ostatnie 30 dni</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['recent'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista recept -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Lista recept</h2>
            <p class="text-sm text-gray-600 mt-1">Wszystkie recepty wystawione przez lekarzy</p>
        </div>

        @if($prescriptions->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($prescriptions as $prescription)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $prescription->medication_name }}</h3>
                                
                                @if($prescription->medication_code)
                                <p class="text-sm text-gray-600 mt-1">
                                    <span class="font-medium">Kod leku:</span> {{ $prescription->medication_code }}
                                </p>
                                @endif
                                
                                @if($prescription->dosage)
                                <p class="text-sm text-gray-700 mt-2">
                                    <span class="font-medium">Dawkowanie:</span> {{ $prescription->dosage }}
                                </p>
                                @endif
                                
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <span class="text-sm text-gray-600">
                                        <span class="font-medium">Ilość:</span> {{ $prescription->quantity }} op.
                                    </span>
                                    
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $prescription->is_refundable ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $prescription->is_refundable ? 'Refundowane' : 'Pełna odpłatność' }}
                                    </span>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">Wystawił:</p>
                                            <p class="font-medium text-gray-900">{{ $prescription->visit->doctor->user->full_name }}</p>
                                            <p class="text-gray-500 text-xs">{{ $prescription->visit->doctor->specializations->pluck('name')->join(', ') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Data wystawienia:</p>
                                            <p class="font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($prescription->created_at)->locale('pl')->isoFormat('D MMMM YYYY, HH:mm') }}
                                            </p>
                                            <p class="text-gray-500 text-xs">
                                                {{ \Carbon\Carbon::parse($prescription->created_at)->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginacja -->
        <div class="p-6 border-t border-gray-200">
            {{ $prescriptions->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Brak recept</h3>
            <p class="text-gray-600 mb-6">Nie masz jeszcze żadnych wystawionych recept</p>
            <a href="{{ route('visits.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Umów wizytę
            </a>
        </div>
        @endif
    </div>
</div>
@endsection