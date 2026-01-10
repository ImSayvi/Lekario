@extends('layouts.doctor')

@section('title', 'Wizyty')
@section('page-title', 'Wszystkie wizyty')
@section('page-subtitle', 'Zarządzaj swoimi wizytami')

@section('content')
<!-- Statystyki -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <a href="{{ route('doctor.visits.index', ['status' => 'all']) }}" 
       class="bg-white rounded-xl shadow-sm p-4 border-2 transition hover:shadow-md {{ $status === 'all' ? 'border-blue-500' : 'border-gray-100' }}">
        <p class="text-sm font-medium text-gray-600">Wszystkie</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['all'] }}</p>
    </a>
    
    <a href="{{ route('doctor.visits.index', ['status' => 'pending']) }}" 
       class="bg-white rounded-xl shadow-sm p-4 border-2 transition hover:shadow-md {{ $status === 'pending' ? 'border-amber-500' : 'border-gray-100' }}">
        <p class="text-sm font-medium text-gray-600">Oczekujące</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
    </a>
    
    <a href="{{ route('doctor.visits.index', ['status' => 'accepted']) }}" 
       class="bg-white rounded-xl shadow-sm p-4 border-2 transition hover:shadow-md {{ $status === 'accepted' ? 'border-blue-500' : 'border-gray-100' }}">
        <p class="text-sm font-medium text-gray-600">Zatwierdzone</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['accepted'] }}</p>
    </a>
    
    <a href="{{ route('doctor.visits.index', ['status' => 'completed']) }}" 
       class="bg-white rounded-xl shadow-sm p-4 border-2 transition hover:shadow-md {{ $status === 'completed' ? 'border-emerald-500' : 'border-gray-100' }}">
        <p class="text-sm font-medium text-gray-600">Zakończone</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
    </a>
    
    <a href="{{ route('doctor.visits.index', ['status' => 'rejected']) }}" 
       class="bg-white rounded-xl shadow-sm p-4 border-2 transition hover:shadow-md {{ $status === 'rejected' ? 'border-red-500' : 'border-gray-100' }}">
        <p class="text-sm font-medium text-gray-600">Odrzucone</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
    </a>
</div>

<!-- Filtry -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <form method="GET" action="{{ route('doctor.visits.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Wyszukiwanie -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Szukaj pacjenta</label>
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}"
                placeholder="Imię, nazwisko lub PESEL..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <!-- Data od -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data od</label>
            <input 
                type="date" 
                name="date_from" 
                value="{{ $dateFrom }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <!-- Data do -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data do</label>
            <input 
                type="date" 
                name="date_to" 
                value="{{ $dateTo }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <input type="hidden" name="status" value="{{ $status }}">
        
        <div class="md:col-span-4 flex items-center space-x-3">
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Filtruj
            </button>
            <a 
                href="{{ route('doctor.visits.index') }}"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                Wyczyść filtry
            </a>
        </div>
    </form>
</div>

<!-- Lista wizyt -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-900">
            @if($status === 'all')
                Wszystkie wizyty
            @elseif($status === 'pending')
                Wizyty oczekujące
            @elseif($status === 'accepted')
                Wizyty zatwierdzone
            @elseif($status === 'completed')
                Wizyty zakończone
            @elseif($status === 'rejected')
                Wizyty odrzucone
            @endif
            <span class="text-gray-500 text-base font-normal">({{ $visits->total() }})</span>
        </h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pacjent</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data i godzina</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Czas trwania</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notatki</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($visits as $visit)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm mr-3">
                                    {{ strtoupper(substr($visit->patient->user->first_name, 0, 1)) }}{{ strtoupper(substr($visit->patient->user->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $visit->patient->user->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $visit->patient->pesel }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($visit->start_time)->format('d.m.Y') }}</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($visit->start_time)->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($visit->start_time)->diffInMinutes(\Carbon\Carbon::parse($visit->end_time)) }} min
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($visit->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Oczekująca
                                </span>
                            @elseif($visit->status === 'accepted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Zatwierdzona
                                </span>
                            @elseif($visit->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    Zakończona
                                </span>
                            @elseif($visit->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Odrzucona
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($visit->notes)
                                <p class="text-sm text-gray-600 truncate max-w-xs" title="{{ $visit->notes }}">
                                    📝 {{ Str::limit($visit->notes, 30) }}
                                </p>
                            @else
                                <p class="text-sm text-gray-400 italic">Brak notatek</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('doctor.visits.show', $visit) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                Szczegóły
                            </a>
                            @if($visit->status === 'accepted' && \Carbon\Carbon::parse($visit->start_time)->isPast())
                                <form action="{{ route('doctor.visits.complete', $visit) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:text-emerald-900">
                                        Zakończ
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Brak wizyt</h3>
                            <p class="text-gray-600">Nie znaleziono wizyt spełniających kryteria</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginacja -->
    @if($visits->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $visits->links() }}
        </div>
    @endif
</div>
@endsection