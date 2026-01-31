@extends('layouts.admin')

@section('title', 'Zmiana roli użytkownika')
@section('page-title', 'Zmiana roli użytkownika')
@section('page-subtitle', $user->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.show', $user) }}" class="text-red-600 hover:text-red-700">
            ← Powrót do użytkownika
        </a>
    </div>

    <!-- Ostrzeżenie -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Ostrzeżenie</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Zmiana roli spowoduje <strong>usunięcie obecnej roli</strong> i wszystkich powiązanych danych (z wyjątkiem wizyt).</p>
                    <p class="mt-1">Jeśli użytkownik ma aktywne wizyty, operacja zostanie zablokowana.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Obecna rola -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Obecna rola</h3>
        
        @if($user->admin)
            <div class="px-4 py-3 bg-red-100 rounded-lg text-center">
                <span class="font-semibold text-red-800">Administrator</span>
                @if($user->admin->position)
                <p class="text-sm text-red-700 mt-1">{{ $user->admin->position }}</p>
                @endif
            </div>
        @elseif($user->doctor)
            <div class="px-4 py-3 bg-emerald-100 rounded-lg text-center">
                <span class="font-semibold text-emerald-800">Lekarz</span>
                @if($user->doctor->specializations->count() > 0)
                <p class="text-sm text-emerald-700 mt-1">
                    {{ $user->doctor->specializations->pluck('name')->join(', ') }}
                </p>
                @endif
            </div>
        @elseif($user->patient)
            <div class="px-4 py-3 bg-blue-100 rounded-lg text-center">
                <span class="font-semibold text-blue-800">Pacjent</span>
                @if($user->patient->pesel)
                <p class="text-sm text-blue-700 mt-1">PESEL: {{ $user->patient->pesel }}</p>
                @endif
            </div>
        @endif
    </div>

    <!-- Formularz zmiany roli -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Nowa rola</h3>
        
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <strong>Błędy walidacji:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('admin.users.change-role.update', $user) }}" method="POST" id="changeRoleForm">
            @csrf
            @method('PATCH')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wybierz nową rolę *</label>
                    <select name="new_role" id="new_role" required onchange="toggleRoleFields()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">Wybierz...</option>
                        <option value="patient" {{ old('new_role') === 'patient' ? 'selected' : '' }}>Pacjent</option>
                        <option value="doctor" {{ old('new_role') === 'doctor' ? 'selected' : '' }}>Lekarz</option>
                        <option value="admin" {{ old('new_role') === 'admin' ? 'selected' : '' }}>Administrator</option>
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
                        <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto">
                            @foreach(\App\Models\Specialization::orderBy('name')->get() as $spec)
                            <label class="flex items-center py-2 hover:bg-gray-50 px-2 rounded cursor-pointer">
                                <input type="checkbox" 
                                       name="specialization_ids[]" 
                                       value="{{ $spec->id }}"
                                       {{ in_array($spec->id, old('specialization_ids', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="ml-3 text-sm text-gray-700">{{ $spec->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Wybierz co najmniej jedną specjalizację</p>
                    </div>
                </div>

                <!-- Pola dla Admina -->
                <div id="admin-fields" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stanowisko</label>
                    <input type="text" name="position" value="{{ old('position') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                           placeholder="np. Administrator Systemu">
                </div>

                <!-- Przyciski -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.show', $user) }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Anuluj
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                            onclick="return confirm('Czy na pewno chcesz zmienić rolę tego użytkownika? Obecna rola zostanie usunięta.')">
                        Zmień rolę
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Opcja usunięcia roli -->
    @if($user->patient || $user->doctor || $user->admin)
    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
        <h3 class="text-lg font-semibold text-red-900 mb-4">Usuń rolę</h3>
        <p class="text-sm text-gray-600 mb-4">
            Usunięcie roli spowoduje, że użytkownik będzie wymagał ponownej weryfikacji i przypisania roli.
        </p>
        
        <form action="{{ route('admin.users.remove-role', $user) }}" method="POST"
              onsubmit="return confirm('Czy na pewno chcesz usunąć rolę tego użytkownika? Status konta zostanie zmieniony na VERIFY.')">
            @csrf
            @method('DELETE')
            
            <button type="submit" 
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                Usuń obecną rolę
            </button>
        </form>
    </div>
    @endif
</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('new_role').value;
    
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

document.addEventListener('DOMContentLoaded', function() {
    const role = document.getElementById('new_role').value;
    if (role) {
        toggleRoleFields();
    }
});
</script>
@endsection