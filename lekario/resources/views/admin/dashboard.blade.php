@extends('layouts.admin')

@section('title', 'Panel Administracyjny')
@section('page-title', 'Panel Administracyjny')
@section('page-subtitle', 'Witaj, ' . Auth::user()->first_name)

@section('content')
<div class="space-y-6">
    <!-- Statystyki główne -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Użytkownicy -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Użytkownicy</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="text-blue-600">{{ $stats['total_patients'] }}</span> pacjentów, 
                        <span class="text-emerald-600">{{ $stats['total_doctors'] }}</span> lekarzy
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Wizyty -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Wizyty</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_visits'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="text-yellow-600">{{ $stats['pending_visits'] }}</span> oczekujących
                    </p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recepty -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Recepty</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_prescriptions'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">Wystawione</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Skierowania -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Skierowania</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_referrals'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">Wystawione</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Wykres wizyt i Top lekarze -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Wykres wizyt z ostatnich 7 dni -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Wizyty w ostatnich 7 dniach</h3>
            
            @if($visitsLastWeek->count() > 0)
            <div class="space-y-3">
                @foreach($visitsLastWeek as $day)
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 w-32">
                        {{ \Carbon\Carbon::parse($day->date)->locale('pl')->isoFormat('D MMM') }}
                    </span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" 
                                 style="width: {{ ($day->count / $visitsLastWeek->max('count')) * 100 }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 w-12 text-right">{{ $day->count }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-8">Brak danych</p>
            @endif
        </div>

        <!-- Top lekarze -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Najaktywniejszi lekarze</h3>
            
            @if($topDoctors->count() > 0)
            <div class="space-y-4">
                @foreach($topDoctors as $doctor)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 font-semibold mr-3">
                            {{ strtoupper(substr($doctor->user->first_name, 0, 1)) }}{{ strtoupper(substr($doctor->user->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $doctor->user->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $doctor->specializations->pluck('name')->join(', ') }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-sm font-semibold">
                        {{ $doctor->visits_count }} wizyt
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-8">Brak danych</p>
            @endif
        </div>
    </div>

    <!-- Ostatnie wizyty -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Ostatnie wizyty</h3>
        </div>
        
        @if($recentVisits->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pacjent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lekarz</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utworzono</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentVisits as $visit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $visit->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $visit->patient->user->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $visit->doctor->user->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($visit->start_time)->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($visit->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Oczekująca</span>
                            @elseif($visit->status === 'accepted')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Zatwierdzona</span>
                            @elseif($visit->status === 'completed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Zakończona</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Odrzucona</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($visit->created_at)->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <p class="text-gray-500">Brak wizyt</p>
        </div>
        @endif
    </div>
</div>
@endsection