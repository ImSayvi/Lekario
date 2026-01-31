@extends('layouts.doctor')

@section('title', 'Moi pacjenci')
@section('page-title', 'Moi pacjenci')
@section('page-subtitle', 'Pacjenci z którymi miałeś wizyty')

@section('content')
<div class="space-y-6">
    <!-- Statystyki -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Wszyscy pacjenci</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Aktywni (3 mies.)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Wyszukiwarka -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('doctor.patients.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}"
                       placeholder="Szukaj pacjenta (imię, nazwisko, email)..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Szukaj
            </button>
            @if($search)
            <a href="{{ route('doctor.patients.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                Wyczyść
            </a>
            @endif
        </form>
    </div>

    <!-- Lista pacjentów -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($patients->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pacjent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontakt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liczba wizyt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ostatnia wizyta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($patients as $patient)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                    {{ strtoupper(substr($patient->user->first_name, 0, 1)) }}{{ strtoupper(substr($patient->user->last_name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $patient->user->full_name }}
                                    </div>
                                    @if($patient->user->pesel)
                                    <div class="text-sm text-gray-500">PESEL: {{ $patient->user->pesel }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $patient->user->email }}</div>
                            @if($patient->user->phone)
                            <div class="text-sm text-gray-500">{{ $patient->user->phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $patient->visits->count() }} {{ $patient->visits->count() === 1 ? 'wizyta' : 'wizyt' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($patient->visits->first())
                                {{ $patient->visits->first()?->start_time?->format('d.m.Y') ?? 'Brak wizyt' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('doctor.patients.show', $patient) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                Zobacz historię
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
        <div class="p-6 border-t border-gray-200">
            {{ $patients->links() }}
        </div>
        @endif
        @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if($search)
                    Nie znaleziono pacjentów
                @else
                    Brak pacjentów
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if($search)
                    Spróbuj innego wyszukiwania
                @else
                    Pacjenci pojawią się tutaj po pierwszej wizycie
                @endif
            </p>
        </div>
        @endif
    </div>
</div>
@endsection