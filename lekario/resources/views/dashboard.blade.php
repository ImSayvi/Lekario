@extends('layouts.app')

@section('title', 'Panel główny')
@section('page-title', 'Panel główny')
@section('page-subtitle', 'Witaj z powrotem, ' . Auth::user()->first_name)

@section('content')
<div class="space-y-6">
    <!-- Statystyki - Kafelki -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Nadchodzące wizyty -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Nadchodzące wizyty</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['upcoming_visits'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-gray-500">Zaplanowane wizyty</span>
            </div>
        </div>

        <!-- Oczekujące -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Oczekujące</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_visits'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-gray-500">Oczekują na akceptację</span>
            </div>
        </div>

        <!-- Ukończone -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ukończone</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['completed_visits'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-gray-500">Wszystkie wizyty</span>
            </div>
        </div>
    </div>

    <!-- Najbliższa wizyta -->
    @if($nextVisit)
    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium opacity-90">Najbliższa wizyta</span>
                </div>
                <h3 class="text-2xl font-bold mb-1">
                    {{ \Carbon\Carbon::parse($nextVisit->start_time)->locale('pl')->isoFormat('D MMMM YYYY') }}
                </h3>
                <p class="text-lg opacity-90">
                    {{ \Carbon\Carbon::parse($nextVisit->start_time)->format('H:i') }} - 
                    {{ \Carbon\Carbon::parse($nextVisit->end_time)->format('H:i') }}
                </p>
                <div class="mt-4 flex items-center">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-emerald-600 font-semibold mr-3">
                        {{ strtoupper(substr($nextVisit->doctor->user->first_name, 0, 1)) }}{{ strtoupper(substr($nextVisit->doctor->user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $nextVisit->doctor->user->full_name }}</p>
                        <p class="text-sm opacity-75">{{ $nextVisit->doctor->specializations->pluck('name')->join(', ') ?? 'Lekarz' }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                @if($nextVisit->status === 'pending')
                <span class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs font-medium">
                    Oczekuje
                </span>
                @else
                <span class="px-3 py-1 bg-white text-emerald-600 rounded-full text-xs font-medium">
                    Potwierdzona
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Kalendarz i Lista wizyt -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kalendarz z nawigacją -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kalendarz wizyt</h3>
            
            <div class="mb-4 flex items-center justify-between">
                <button onclick="previousMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h4 id="currentMonth" class="font-medium text-gray-700">
                    {{ \Carbon\Carbon::now()->locale('pl')->isoFormat('MMMM YYYY') }}
                </h4>
                <button onclick="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Dni tygodnia -->
            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach(['Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So', 'Nd'] as $day)
                <div class="text-center text-xs font-medium text-gray-500 py-2">{{ $day }}</div>
                @endforeach
            </div>

            <!-- Dni miesiąca -->
            <div id="calendarDays" class="grid grid-cols-7 gap-1">
                @php
                    $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                    $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
                    $startDayOfWeek = $startOfMonth->dayOfWeekIso;
                    
                    // Puste dni przed pierwszym dniem miesiąca
                    for ($i = 1; $i < $startDayOfWeek; $i++) {
                        echo '<div class="aspect-square"></div>';
                    }
                    
                    // Dni miesiąca
                    for ($day = 1; $day <= $endOfMonth->day; $day++) {
                        $currentDate = \Carbon\Carbon::now()->startOfMonth()->addDays($day - 1);
                        $dateString = $currentDate->format('Y-m-d');
                        $hasVisit = isset($monthVisits[$dateString]);
                        $isToday = $currentDate->isToday();
                        
                        $classes = 'aspect-square flex items-center justify-center text-sm rounded-lg transition ';
                        
                        if ($isToday) {
                            $classes .= 'bg-emerald-600 text-white font-bold ';
                        } elseif ($hasVisit) {
                            $classes .= 'bg-emerald-100 text-emerald-700 font-medium ';
                        } else {
                            $classes .= 'text-gray-700 hover:bg-gray-100 ';
                        }
                        
                        echo "<div class='$classes' data-date='$dateString'>$day</div>";
                    }
                @endphp
            </div>

            <div class="mt-4 flex items-center justify-center space-x-4 text-xs">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-emerald-600 rounded mr-2"></div>
                    <span class="text-gray-600">Dzisiaj</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-emerald-100 rounded mr-2"></div>
                    <span class="text-gray-600">Wizyta</span>
                </div>
            </div>
        </div>

        <!-- Lista nadchodzących wizyt -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Nadchodzące wizyty</h3>
            
            @if($upcomingVisits->count() > 0)
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($upcomingVisits->take(5) as $visit)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-emerald-300 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($visit->start_time)->locale('pl')->isoFormat('D MMM, HH:mm') }}
                                </span>
                            </div>
                            <p class="font-medium text-gray-900">{{ $visit->doctor->user->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $visit->doctor->specializations->pluck('name')->join(', ') ?? 'Lekarz' }}</p>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            @if($visit->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">
                                Oczekuje
                            </span>
                            @else
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">
                                Potwierdzona
                            </span>
                            @endif
                            
                            <form method="POST" action="{{ route('patient.visits.cancel', $visit->id) }}" 
                                  onsubmit="return confirm('Czy na pewno chcesz anulować tę wizytę?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-xs text-red-600 hover:text-red-700 font-medium transition">
                                    Anuluj
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($upcomingVisits->count() > 5)
            <div class="mt-4 text-center">
                <a href="{{ route('patient.visits.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                    Zobacz wszystkie wizyty →
                </a>
            </div>
            @endif
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500 mb-4">Brak nadchodzących wizyt</p>
                <a href="{{ route('visits.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Umów wizytę
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Recepty i Skierowania -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recepty -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Ostatnie recepty</h3>
                <a href="{{ route('prescriptions.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Zobacz wszystkie →
                </a>
            </div>
            
            @if($recentPrescriptions && $recentPrescriptions->count() > 0)
            <div class="space-y-3">
                @foreach($recentPrescriptions as $prescription)
                <div class="border border-emerald-200 rounded-lg p-4 bg-emerald-50 hover:bg-emerald-100 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-1">{{ $prescription->medication_name }}</h4>
                            @if($prescription->dosage)
                            <p class="text-sm text-gray-600 mb-2">{{ $prescription->dosage }}</p>
                            @endif
                            <div class="flex items-center space-x-3 text-xs text-gray-500">
                                <span>Ilość: {{ $prescription->quantity }} op.</span>
                                <span class="px-2 py-0.5 rounded {{ $prescription->is_refundable ? 'bg-emerald-200 text-emerald-800' : 'bg-red-100 text-red-700' }}">
                                    {{ $prescription->is_refundable ? 'Refundowane' : 'Pełna odpłatność' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">
                                {{ \Carbon\Carbon::parse($prescription->created_at)->locale('pl')->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>
                        <svg class="w-10 h-10 text-emerald-300 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500">Brak recept</p>
                <p class="text-sm text-gray-400 mt-1">Recepty pojawią się tutaj po wizycie lekarskiej</p>
            </div>
            @endif
        </div>

        <!-- Skierowania -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Ostatnie skierowania</h3>
                <a href="{{ route('referrals.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Zobacz wszystkie →
                </a>
            </div>
            
            @if($recentReferrals && $recentReferrals->count() > 0)
            <div class="space-y-3">
                @foreach($recentReferrals as $referral)
                <div class="border border-blue-200 rounded-lg p-4 bg-blue-50 hover:bg-blue-100 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <h4 class="font-semibold text-gray-900">{{ $referral->referral_to }}</h4>
                                <span class="ml-2 px-2 py-0.5 text-xs rounded bg-blue-200 text-blue-800">
                                    {{ $referral->type === 'examination' ? 'Badanie' : 'Specjalista' }}
                                </span>
                            </div>
                            @if($referral->diagnosis)
                            <p class="text-sm text-gray-600 mb-2">{{ Str::limit($referral->diagnosis, 60) }}</p>
                            @endif
                            <p class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($referral->created_at)->locale('pl')->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>
                        <svg class="w-10 h-10 text-blue-300 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500">Brak skierowań</p>
                <p class="text-sm text-gray-400 mt-1">Skierowania pojawią się tutaj po wizycie lekarskiej</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Dane wizyt dla kalendarza (przekazane z kontrolera)
const allVisits = @json($allVisits ?? []);
let currentDate = new Date();

const monthNames = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 
                    'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Aktualizuj nagłówek
    document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
    
    // Pierwszy dzień miesiąca
    const firstDay = new Date(year, month, 1);
    let startDayOfWeek = firstDay.getDay();
    startDayOfWeek = startDayOfWeek === 0 ? 7 : startDayOfWeek; // Niedziela = 7
    
    // Ostatni dzień miesiąca
    const lastDay = new Date(year, month + 1, 0).getDate();
    
    // Dzisiejsza data
    const today = new Date();
    const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;
    const todayDate = today.getDate();
    
    let html = '';
    
    // Puste komórki przed pierwszym dniem
    for (let i = 1; i < startDayOfWeek; i++) {
        html += '<div class="aspect-square"></div>';
    }
    
    // Dni miesiąca
    for (let day = 1; day <= lastDay; day++) {
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const hasVisit = allVisits.includes(dateString);
        const isToday = isCurrentMonth && day === todayDate;
        
        let classes = 'aspect-square flex items-center justify-center text-sm rounded-lg transition cursor-pointer ';
        
        if (isToday) {
            classes += 'bg-emerald-600 text-white font-bold ';
        } else if (hasVisit) {
            classes += 'bg-emerald-100 text-emerald-700 font-medium hover:bg-emerald-200 ';
        } else {
            classes += 'text-gray-700 hover:bg-gray-100 ';
        }
        
        html += `<div class="${classes}" data-date="${dateString}">${day}</div>`;
    }
    
    document.getElementById('calendarDays').innerHTML = html;
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

// Inicjalizacja kalendarza
document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
});
</script>
@endsection