@extends('layouts.doctor')

@section('title', 'Szczegóły wizyty')
@section('page-title', 'Szczegóły wizyty')
@section('page-subtitle', 'Informacje o wizycie i pacjencie')

@section('content')
<div class="mb-6">
    <a href="{{ route('doctor.visits.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Powrót do listy wizyt
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informacje o wizycie -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Status i data -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Wizyta #{{ $visit->id }}</h2>
                    @if($visit->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Oczekująca
                        </span>
                    @elseif($visit->status === 'accepted')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Zatwierdzona
                        </span>
                    @elseif($visit->status === 'completed')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Zakończona
                        </span>
                    @elseif($visit->status === 'rejected')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Odrzucona
                        </span>
                    @endif
                </div>
                
                @if($visit->status === 'pending')
                    <div class="flex space-x-2">
                        <form action="{{ route('doctor.visits.accept', $visit) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                                ✓ Zaakceptuj
                            </button>
                        </form>
                        <form action="{{ route('doctor.visits.reject', $visit) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz odrzucić tę wizytę?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                                ✗ Odrzuć
                            </button>
                        </form>
                    </div>
                @elseif($visit->status === 'accepted' && \Carbon\Carbon::parse($visit->start_time)->isPast())
                    <form action="{{ route('doctor.visits.complete', $visit) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                            Oznacz jako zakończoną
                        </button>
                    </form>
                @endif
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Data wizyty</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($visit->start_time)->locale('pl')->isoFormat('dddd, D MMMM YYYY') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Godzina</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($visit->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($visit->end_time)->format('H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Czas trwania</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($visit->start_time)->diffInMinutes(\Carbon\Carbon::parse($visit->end_time)) }} minut
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Utworzono</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($visit->created_at)->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Notatki -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Notatki do wizyty</h3>
            
            @if($visit->status === 'pending' || $visit->status === 'accepted')
                <form action="{{ route('doctor.visits.update', $visit) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="duration" value="{{ \Carbon\Carbon::parse($visit->start_time)->diffInMinutes(\Carbon\Carbon::parse($visit->end_time)) }}">
                    <textarea 
                        name="notes" 
                        rows="6" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Dodaj notatki dotyczące wizyty, rozpoznanie, zalecenia..."
                    >{{ $visit->notes }}</textarea>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Zapisz notatki
                        </button>
                    </div>
                </form>
            @else
                @if($visit->notes)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $visit->notes }}</p>
                    </div>
                @else
                    <p class="text-gray-500 italic">Brak notatek do tej wizyty</p>
                @endif
            @endif
        </div>

        <!-- Recepty i Skierowania (tylko dla accepted/completed) -->
        @if(in_array($visit->status, ['accepted', 'completed']))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Wystawianie recepty -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Wystaw receptę</h3>
                </div>

                <form action="{{ route('doctor.visits.prescription.store', $visit) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa leku *</label>
                            <input type="text" name="medication_name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kod leku</label>
                            <input type="text" name="medication_code"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dawkowanie</label>
                            <textarea name="dosage" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ilość *</label>
                                <input type="number" name="quantity" min="1" value="1" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Odpłatność *</label>
                                <select name="is_refundable" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                                    <option value="1">Refundowane</option>
                                    <option value="0">Pełna odpłatność</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700">
                            Wystaw receptę
                        </button>
                    </div>
                </form>
            </div>

            <!-- Wystawianie skierowania -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Wystaw skierowanie</h3>
                </div>

                <form action="{{ route('doctor.visits.referral.store', $visit) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Typ *</label>
                            <select name="type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Wybierz...</option>
                                <option value="examination">Badanie</option>
                                <option value="specialist">Lekarz specjalista</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Skierowanie na *</label>
                            <input type="text" name="referral_to" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rozpoznanie</label>
                            <textarea name="diagnosis" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                            Wystaw skierowanie
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista wystawionych recept -->
        @if($visit->prescriptions->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Wystawione recepty</h3>
            <div class="space-y-3">
                @foreach($visit->prescriptions as $prescription)
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $prescription->medication_name }}</p>
                            @if($prescription->medication_code)
                            <p class="text-sm text-gray-600">Kod: {{ $prescription->medication_code }}</p>
                            @endif
                            @if($prescription->dosage)
                            <p class="text-sm text-gray-600">{{ $prescription->dosage }}</p>
                            @endif
                            <p class="text-sm text-gray-600">Ilość: {{ $prescription->quantity }} op.</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded {{ $prescription->is_refundable ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $prescription->is_refundable ? 'Refundowane' : 'Pełna odpłatność' }}
                            </span>
                            <button onclick="editPrescription({{ $prescription->id }})" 
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form action="{{ route('doctor.prescriptions.destroy', $prescription) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Czy na pewno chcesz usunąć tę receptę?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Formularz edycji (ukryty domyślnie) -->
                    <div id="edit-prescription-{{ $prescription->id }}" class="hidden mt-4 pt-4 border-t border-emerald-300">
                        <form action="{{ route('doctor.prescriptions.update', $prescription) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa leku *</label>
                                    <input type="text" name="medication_name" value="{{ $prescription->medication_name }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kod leku</label>
                                    <input type="text" name="medication_code" value="{{ $prescription->medication_code }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dawkowanie</label>
                                    <textarea name="dosage" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">{{ $prescription->dosage }}</textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ilość *</label>
                                        <input type="number" name="quantity" min="1" value="{{ $prescription->quantity }}" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Odpłatność *</label>
                                        <select name="is_refundable" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                                            <option value="1" {{ $prescription->is_refundable ? 'selected' : '' }}>Refundowane</option>
                                            <option value="0" {{ !$prescription->is_refundable ? 'selected' : '' }}>Pełna odpłatność</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit"
                                            class="flex-1 px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700">
                                        Zapisz zmiany
                                    </button>
                                    <button type="button" onclick="cancelEditPrescription({{ $prescription->id }})"
                                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300">
                                        Anuluj
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Lista wystawionych skierowań -->
        @if($visit->referrals->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Wystawione skierowania</h3>
            <div class="space-y-3">
                @foreach($visit->referrals as $referral)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $referral->referral_to }}</p>
                            @if($referral->diagnosis)
                            <p class="text-sm text-gray-600">{{ $referral->diagnosis }}</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                {{ $referral->type === 'examination' ? 'Badanie' : 'Specjalista' }}
                            </span>
                            <button onclick="editReferral({{ $referral->id }})" 
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form action="{{ route('doctor.referrals.destroy', $referral) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Czy na pewno chcesz usunąć to skierowanie?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Formularz edycji (ukryty domyślnie) -->
                    <div id="edit-referral-{{ $referral->id }}" class="hidden mt-4 pt-4 border-t border-blue-300">
                        <form action="{{ route('doctor.referrals.update', $referral) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Typ *</label>
                                    <select name="type" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="examination" {{ $referral->type === 'examination' ? 'selected' : '' }}>Badanie</option>
                                        <option value="specialist" {{ $referral->type === 'specialist' ? 'selected' : '' }}>Lekarz specjalista</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Skierowanie na *</label>
                                    <input type="text" name="referral_to" value="{{ $referral->referral_to }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rozpoznanie</label>
                                    <textarea name="diagnosis" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ $referral->diagnosis }}</textarea>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit"
                                            class="flex-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                                        Zapisz zmiany
                                    </button>
                                    <button type="button" onclick="cancelEditReferral({{ $referral->id }})"
                                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300">
                                        Anuluj
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif
    </div>
    
    <!-- Sidebar z informacjami o pacjencie -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Informacje o pacjencie</h3>
            
            <div class="flex items-center mb-6">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-xl mr-4">
                    {{ strtoupper(substr($visit->patient->user->first_name, 0, 1)) }}{{ strtoupper(substr($visit->patient->user->last_name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-lg">{{ $visit->patient->user->full_name }}</h4>
                    <p class="text-sm text-gray-600">Pacjent</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 mb-1">PESEL</p>
                    <p class="font-medium text-gray-900">{{ $visit->patient->pesel }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600 mb-1">Telefon</p>
                    <p class="font-medium text-gray-900">{{ $visit->patient->user->phone ?? 'Brak' }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600 mb-1">Email</p>
                    <p class="font-medium text-gray-900">{{ $visit->patient->user->email }}</p>
                </div>
            </div>
        </div>
        
        <!-- Historia wizyt -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Historia wizyt</h3>
            
            @php
                $patientVisits = \App\Models\Visit::where('patient_id', $visit->patient_id)
                    ->where('doctor_id', auth()->user()->doctor->id)
                    ->where('id', '!=', $visit->id)
                    ->orderBy('start_time', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            
            @if($patientVisits->count() > 0)
                <div class="space-y-3">
                    @foreach($patientVisits as $pastVisit)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($pastVisit->status === 'completed')
                                    <div class="w-2 h-2 mt-2 bg-emerald-500 rounded-full"></div>
                                @else
                                    <div class="w-2 h-2 mt-2 bg-gray-300 rounded-full"></div>
                                @endif
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($pastVisit->start_time)->format('d.m.Y H:i') }}
                                </p>
                                <p class="text-xs text-gray-600">{{ ucfirst($pastVisit->status) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Brak wcześniejszych wizyt</p>
            @endif
        </div>
    </div>
</div>

<script>
function editPrescription(id) {
    document.getElementById('edit-prescription-' + id).classList.remove('hidden');
}

function cancelEditPrescription(id) {
    document.getElementById('edit-prescription-' + id).classList.add('hidden');
}

function editReferral(id) {
    document.getElementById('edit-referral-' + id).classList.remove('hidden');
}

function cancelEditReferral(id) {
    document.getElementById('edit-referral-' + id).classList.add('hidden');
}
</script>
@endsection