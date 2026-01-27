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
            <!-- Dane podstawowe -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dane podstawowe</h3>
                
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
                        <p class="text-sm text-gray-600">Data rejestracji</p>
                        <p class="font-medium text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Ostatnia aktualizacja</p>
                        <p class="font-medium text-gray-900">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Przypisz rolę -->
            @if(!$user->patient && !$user->doctor && !$user->admin)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Przypisz rolę</h3>
                
                <form action="{{ route('admin.users.assign-role', $user) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Wybierz rolę *</label>
                            <select name="role" id="role" required onchange="toggleRoleFields()"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                <option value="">Wybierz...</option>
                                <option value="patient">Pacjent</option>
                                <option value="doctor">Lekarz</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>

                        <!-- Pola dla Pacjenta -->
                        <div id="patient-fields" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">PESEL *</label>
                            <input type="text" name="pesel" maxlength="11" pattern="[0-9]{11}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                   placeholder="Wprowadź PESEL (11 cyfr)">
                        </div>

                        <!-- Pola dla Lekarza -->
                        <div id="doctor-fields" class="hidden space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Numer PWZ *</label>
                                <input type="text" name="pwz"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                       placeholder="Wprowadź numer PWZ">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Specjalizacje *</label>
                                <select name="specialization_ids[]" multiple size="5"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    @foreach(\App\Models\Specialization::orderBy('name')->get() as $spec)
                                    <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Przytrzymaj Ctrl/Cmd aby wybrać wiele</p>
                            </div>
                        </div>

                        <!-- Pola dla Admina -->
                        <div id="admin-fields" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stanowisko</label>
                            <input type="text" name="position"
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
                    <div class="px-4 py-3 bg-red-100 rounded-lg text-center">
                        <span class="font-semibold text-red-800">Administrator</span>
                        @if($user->admin->position)
                        <p class="text-sm text-red-700 mt-1">{{ $user->admin->position }}</p>
                        @endif
                    </div>
                @elseif($user->doctor)
                    <div class="px-4 py-3 bg-emerald-100 rounded-lg text-center">
                        <span class="font-semibold text-emerald-800">Lekarz</span>
                        @if($user->doctor->pwz)
                        <p class="text-sm text-emerald-700 mt-1">PWZ: {{ $user->doctor->pwz }}</p>
                        @endif
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
                @else
                    <div class="px-4 py-3 bg-gray-100 rounded-lg text-center">
                        <span class="font-semibold text-gray-800">Brak przypisanej roli</span>
                    </div>
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
</script>
@endsection