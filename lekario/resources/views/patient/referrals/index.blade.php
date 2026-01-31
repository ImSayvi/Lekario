@extends('layouts.app')

@section('title', 'Moje skierowania')
@section('page-title', 'Moje skierowania')
@section('page-subtitle', 'Historia wystawionych skierowań')

@section('content')
<div class="space-y-6">
    <!-- Statystyki -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Wszystkie skierowania</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['all'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Na badania</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['examination'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Do specjalistów</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['specialist'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtry -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('referrals.index', ['type' => 'all']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition {{ $type === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Wszystkie ({{ $stats['all'] }})
            </a>
            <a href="{{ route('referrals.index', ['type' => 'examination']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition {{ $type === 'examination' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Badania ({{ $stats['examination'] }})
            </a>
            <a href="{{ route('referrals.index', ['type' => 'specialist']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition {{ $type === 'specialist' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Specjaliści ({{ $stats['specialist'] }})
            </a>
        </div>
    </div>

    <!-- Lista skierowań -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Lista skierowań</h2>
            <p class="text-sm text-gray-600 mt-1">
                @if($type === 'examination')
                    Skierowania na badania
                @elseif($type === 'specialist')
                    Skierowania do specjalistów
                @else
                    Wszystkie skierowania wystawione przez lekarzy
                @endif
            </p>
        </div>

        @if($referrals->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($referrals as $referral)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-start">
                    <div class="w-12 h-12 {{ $referral->type === 'examination' ? 'bg-purple-100' : 'bg-indigo-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($referral->type === 'examination')
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        @else
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        @endif
                    </div>
                    
                    <div class="ml-4 flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $referral->referral_to }}</h3>
                                <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold {{ $referral->type === 'examination' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                                    {{ $referral->type === 'examination' ? 'Badanie' : 'Lekarz specjalista' }}
                                </span>
                            </div>
                        </div>
                        
                        @if($referral->diagnosis)
                        <div class="mt-3 bg-gray-50 rounded-lg p-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Rozpoznanie:</p>
                            <p class="text-sm text-gray-600">{{ $referral->diagnosis }}</p>
                        </div>
                        @endif
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Wystawił:</p>
                                    <p class="font-medium text-gray-900">{{ $referral->visit->doctor->user->full_name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $referral->visit->doctor->specializations->pluck('name')->join(', ') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Data wystawienia:</p>
                                    <p class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($referral->created_at)->locale('pl')->isoFormat('D MMMM YYYY, HH:mm') }}
                                    </p>
                                    <p class="text-gray-500 text-xs">
                                        {{ \Carbon\Carbon::parse($referral->created_at)->diffForHumans() }}
                                    </p>
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
            {{ $referrals->appends(['type' => $type])->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Brak skierowań</h3>
            <p class="text-gray-600 mb-6">
                @if($type === 'examination')
                    Nie masz jeszcze żadnych skierowań na badania
                @elseif($type === 'specialist')
                    Nie masz jeszcze żadnych skierowań do specjalistów
                @else
                    Nie masz jeszcze żadnych wystawionych skierowań
                @endif
            </p>
            <a href="{{ route('visits.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
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