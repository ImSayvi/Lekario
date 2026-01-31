@extends('layouts.admin')

@section('title', 'Szczegóły użytkownika')
@section('page-title', 'Szczegóły użytkownika')
@section('page-subtitle', $user->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="text-red-600 hover:text-red-700">
            ← Powrót do listy
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informacje o użytkowniku -->
        <div class="lg:col-span-2 space-y-6">
           <!-- Dodaj przycisk edycji w show.blade.php zaraz pod "Dane podstawowe" -->

<!-- Dane podstawowe -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Dane podstawowe</h3>
        <a href="{{ route('admin.users.edit', $user) }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
            Edytuj dane
        </a>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600">Imię</p>
            <p class="font-medium text-gray-900">{{ $user->first_name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Nazwisko</p>
            <p class="font-medium text-gray-900">{{ $user->last_name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Email</p>
            <p class="font-medium text-gray-900">{{ $user->email }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Telefon</p>
            <p class="font-medium text-gray-900">{{ $user->phone ?? 'Brak' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">PESEL</p>
            <p class="font-medium text-gray-900">{{ $user->pesel ?? 'Brak' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Data rejestracji</p>
            <p class="font-medium text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</p>
        </div>
    </div>
</div>

            <!-- Przypisz rolę (jeśli nie ma) -->
            @if(!$user->patient && !$user->doctor && !$user->admin)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Przypisz rolę</h3>
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <strong>Błędy walidacji:</strong>
                        <ul class="list-disc list-inside mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('admin.users.assign-role', $user) }}" method="POST" id="assignRoleForm">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Wybierz rolę *</label>
                            <select name="role" id="role" required onchange="toggleRoleFields()"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                <option value="">Wybierz...</option>
                                <option value="patient" {{ old('role') === 'patient' ? 'selected' : '' }}>Pacjent</option>
                                <option value="doctor" {{ old('role') === 'doctor' ? 'selected' : '' }}>Lekarz</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                        </div>

                        <!-- Pola dla Pacjenta -->
                        <div id="patient-fields" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">PESEL *</label>
                            <input type="text" name="pesel" maxlength="11" pattern="[0-9]{11}" value="{{ old('pesel') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                   placeholder="Wprowadź PESEL (11 cyfr)">
                            <p class="text-xs text-gray-500 mt-1">Dokładnie 11 cyfr</p>
                        </div>

                        <!-- Pola dla Lekarza -->
                        <div id="doctor-fields" class="hidden space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Specjalizacje *</label>
                                <select name="specialization_ids[]" multiple size="5"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    @foreach(\App\Models\Specialization::orderBy('name')->get() as $spec)
                                    <option value="{{ $spec->id }}" {{ in_array($spec->id, old('specialization_ids', [])) ? 'selected' : '' }}>
                                        {{ $spec->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Przytrzymaj Ctrl/Cmd aby wybrać wiele</p>
                            </div>
                        </div>

                        <!-- Pola dla Admina -->
                        <div id="admin-fields" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stanowisko</label>
                            <input type="text" name="position" value="{{ old('position') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                   placeholder="np. Administrator Systemu">
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                            Przypisz rolę
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status konta</h3>
                
                <div class="mb-4">
                    @if($user->status === 'VERIFY')
                        <span class="px-3 py-2 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 block text-center">
                            Oczekuje na weryfikację
                        </span>
                    @elseif($user->status === 'ACTIVE')
                        <span class="px-3 py-2 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800 block text-center">
                            Aktywny
                        </span>
                    @else
                        <span class="px-3 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800 block text-center">
                            Nieaktywny
                        </span>
                    @endif
                </div>

                <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3">
                        <option value="VERIFY" {{ $user->status === 'VERIFY' ? 'selected' : '' }}>Oczekujący</option>
                        <option value="ACTIVE" {{ $user->status === 'ACTIVE' ? 'selected' : '' }}>Aktywny</option>
                        <option value="INACTIVE" {{ $user->status === 'INACTIVE' ? 'selected' : '' }}>Nieaktywny</option>
                    </select>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Zaktualizuj status
                    </button>
                </form>
            </div>

            <!-- Rola -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rola</h3>
                
                @if($user->admin)
                    <div class="px-4 py-3 bg-red-100 rounded-lg text-center mb-4">
                        <span class="font-semibold text-red-800">Administrator</span>
                        @if($user->admin->position)
                        <p class="text-sm text-red-700 mt-1">{{ $user->admin->position }}</p>
                        @endif
                    </div>
                @elseif($user->doctor)
                    <div class="px-4 py-3 bg-emerald-100 rounded-lg mb-4">
                        <div class="text-center mb-3">
                            <span class="font-semibold text-emerald-800">Lekarz</span>
                            @if($user->doctor->specializations && $user->doctor->specializations->count() > 0)
                            <p class="text-sm text-emerald-700 mt-1">
                                {{ $user->doctor->specializations->pluck('name')->join(', ') }}
                            </p>
                            @endif
                        </div>
                        
                        <!-- Przycisk edycji specjalizacji -->
                        <a href="{{ route('admin.users.edit-doctor', $user) }}" 
                           class="block w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium text-center mb-2">
                            Edytuj specjalizacje
                        </a>
                    </div>
                @elseif($user->patient)
                    <div class="px-4 py-3 bg-blue-100 rounded-lg text-center mb-4">
                        <span class="font-semibold text-blue-800">Pacjent</span>
                        @if($user->patient->pesel)
                        <p class="text-sm text-blue-700 mt-1">PESEL: {{ $user->patient->pesel }}</p>
                        @endif
                    </div>
                @else
                    <div class="px-4 py-3 bg-gray-100 rounded-lg text-center mb-4">
                        <span class="font-semibold text-gray-800">Brak przypisanej roli</span>
                    </div>
                @endif
                
                <!-- Przycisk zmiany roli -->
                @if($user->patient || $user->doctor || $user->admin)
                <button onclick="openChangeRoleModal()" 
                        class="block w-full px-4 py-2 border-2 border-orange-500 text-orange-600 rounded-lg hover:bg-orange-50 transition font-medium text-center">
                    Zmień rolę
                </button>
                @endif
            </div>

            <!-- Usuń użytkownika -->
            @if($user->id !== auth()->id())
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <h3 class="text-lg font-semibold text-red-900 mb-4">Strefa niebezpieczna</h3>
                
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                      onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika? Ta operacja jest nieodwracalna.')">
                    @csrf
                    @method('DELETE')
                    
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Usuń użytkownika
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal zmiany roli - zamień w show.blade.php -->
<div id="changeRoleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Zmiana roli użytkownika</h3>
            <button onclick="closeChangeRoleModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Ostrzeżenie o wizytach -->
        @php
            $patientVisitsCount = $user->patient ? $user->patient->visits()->count() : 0;
            $doctorVisitsCount = $user->doctor ? $user->doctor->visits()->count() : 0;
            $totalVisits = $patientVisitsCount + $doctorVisitsCount;
        @endphp

        @if($totalVisits > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-red-800">Uwaga! Wizyty zostaną usunięte</h4>
                    <div class="mt-2 text-sm text-red-700">
                        <p class="font-semibold">Użytkownik ma {{ $totalVisits }} {{ $totalVisits === 1 ? 'wizytę' : ($totalVisits < 5 ? 'wizyty' : 'wizyt') }} w systemie:</p>
                        <ul class="list-disc list-inside mt-1">
                            @if($patientVisitsCount > 0)
                                <li>{{ $patientVisitsCount }} {{ $patientVisitsCount === 1 ? 'wizyta' : ($patientVisitsCount < 5 ? 'wizyty' : 'wizyt') }} jako pacjent</li>
                            @endif
                            @if($doctorVisitsCount > 0)
                                <li>{{ $doctorVisitsCount }} {{ $doctorVisitsCount === 1 ? 'wizyta' : ($doctorVisitsCount < 5 ? 'wizyty' : 'wizyt') }} jako lekarz</li>
                            @endif
                        </ul>
                        <p class="mt-2 font-semibold">Zmiana roli spowoduje TRWAŁE USUNIĘCIE wszystkich tych wizyt z bazy danych!</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3 text-sm text-blue-700">
                    Użytkownik nie ma żadnych wizyt w systemie. Zmiana roli może być wykonana bezpiecznie.
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('admin.users.change-role.update', $user) }}" method="POST" id="changeRoleForm">
            @csrf
            @method('PATCH')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nowa rola *</label>
                    <select name="new_role" id="new_role" required onchange="toggleChangeRoleFields()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">Wybierz...</option>
                        <option value="patient">Pacjent</option>
                        <option value="doctor">Lekarz</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <!-- Pola dla Pacjenta -->
                <div id="change-patient-fields" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">PESEL *</label>
                    <input type="text" name="pesel" maxlength="11" pattern="[0-9]{11}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                           placeholder="Wprowadź PESEL (11 cyfr)">
                </div>

                <!-- Pola dla Lekarza -->
                <div id="change-doctor-fields" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specjalizacje *</label>
                    <div class="border border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto">
                        @foreach(\App\Models\Specialization::orderBy('name')->get() as $spec)
                        <label class="flex items-center py-2 hover:bg-gray-50 px-2 rounded cursor-pointer">
                            <input type="checkbox" name="specialization_ids[]" value="{{ $spec->id }}"
                                   class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="ml-3 text-sm text-gray-700">{{ $spec->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Pola dla Admina -->
                <div id="change-admin-fields" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stanowisko</label>
                    <input type="text" name="position"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                           placeholder="np. Administrator Systemu">
                </div>

                @if($totalVisits > 0)
                <!-- Checkbox potwierdzenia -->
                <div class="pt-4 border-t border-gray-200">
                    <label class="flex items-start">
                        <input type="checkbox" name="confirm_delete_visits" value="1" required
                               class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="ml-3 text-sm text-gray-700">
                            Potwierdzam, że rozumiem iż zmiana roli spowoduje <strong class="text-red-600">trwałe usunięcie {{ $totalVisits }} {{ $totalVisits === 1 ? 'wizyty' : ($totalVisits < 5 ? 'wizyt' : 'wizyt') }}</strong> z bazy danych.
                        </span>
                    </label>
                </div>
                @else
                <!-- Ukryty input jeśli brak wizyt -->
                <input type="hidden" name="confirm_delete_visits" value="1">
                @endif

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeChangeRoleModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Anuluj
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        {{ $totalVisits > 0 ? 'Zmień rolę i usuń wizyty' : 'Zmień rolę' }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Opcja usunięcia roli -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900 mb-2">Usuń rolę bez przypisywania nowej</h4>
            <p class="text-xs text-gray-600 mb-3">
                Status konta zostanie zmieniony na VERIFY
                @if($totalVisits > 0)
                    <span class="text-red-600 font-semibold">i usunięte zostaną {{ $totalVisits }} {{ $totalVisits === 1 ? 'wizyta' : ($totalVisits < 5 ? 'wizyty' : 'wizyt') }}</span>
                @endif
            </p>
            <form action="{{ route('admin.users.remove-role', $user) }}" method="POST"
                  onsubmit="return confirm('{{ $totalVisits > 0 ? 'UWAGA! Użytkownik ma ' . $totalVisits . ' wizyt w systemie. Czy na pewno chcesz usunąć rolę? Wszystkie wizyty zostaną TRWALE USUNIĘTE!' : 'Czy na pewno chcesz usunąć rolę?' }}')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                    Usuń rolę{{ $totalVisits > 0 ? ' i ' . $totalVisits . ' ' . ($totalVisits === 1 ? 'wizytę' : ($totalVisits < 5 ? 'wizyty' : 'wizyt')) : '' }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('role').value;
    
    document.getElementById('patient-fields').classList.add('hidden');
    document.getElementById('doctor-fields').classList.add('hidden');
    document.getElementById('admin-fields').classList.add('hidden');
    
    if (role === 'patient') {
        document.getElementById('patient-fields').classList.remove('hidden');
    } else if (role === 'doctor') {
        document.getElementById('doctor-fields').classList.remove('hidden');
    } else if (role === 'admin') {
        document.getElementById('admin-fields').classList.remove('hidden');
    }
}

function toggleChangeRoleFields() {
    const role = document.getElementById('new_role').value;
    
    document.getElementById('change-patient-fields').classList.add('hidden');
    document.getElementById('change-doctor-fields').classList.add('hidden');
    document.getElementById('change-admin-fields').classList.add('hidden');
    
    if (role === 'patient') {
        document.getElementById('change-patient-fields').classList.remove('hidden');
    } else if (role === 'doctor') {
        document.getElementById('change-doctor-fields').classList.remove('hidden');
    } else if (role === 'admin') {
        document.getElementById('change-admin-fields').classList.remove('hidden');
    }
}

function openChangeRoleModal() {
    document.getElementById('changeRoleModal').classList.remove('hidden');
}

function closeChangeRoleModal() {
    document.getElementById('changeRoleModal').classList.add('hidden');
}

// Zamknij modal klikając poza nim
document.getElementById('changeRoleModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeChangeRoleModal();
    }
});

// Restore fields on page load
document.addEventListener('DOMContentLoaded', function() {
    const role = document.getElementById('role')?.value;
    if (role) {
        toggleRoleFields();
    }
});
</script>
@endsection