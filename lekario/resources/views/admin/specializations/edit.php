@extends('layouts.admin')

@section('title', 'Edytuj specjalizację')
@section('page-title', 'Edytuj specjalizację')
@section('page-subtitle', $specialization->name)

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.specializations.index') }}" class="text-red-600 hover:text-red-700">
            ← Powrót do listy
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
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

        <form action="{{ route('admin.specializations.update', $specialization) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="space-y-6">
                <!-- Nazwa -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nazwa specjalizacji *
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $specialization->name) }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror"
                           placeholder="np. Kardiologia, Pediatria">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Nazwa musi być unikalna</p>
                </div>



                <!-- Przyciski -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.specializations.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Anuluj
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Zapisz zmiany
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statystyki użycia -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statystyki użycia</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-emerald-50 rounded-lg">
                <p class="text-2xl font-bold text-emerald-600">{{ $specialization->doctors()->count() }}</p>
                <p class="text-sm text-gray-600 mt-1">Lekarzy z tą specjalizacją</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-600">
                    {{ $specialization->doctors()->withCount('visits')->get()->sum('visits_count') }}
                </p>
                <p class="text-sm text-gray-600 mt-1">Wizyt łącznie</p>
            </div>
        </div>
        
        @if($specialization->doctors()->count() > 0)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-700 mb-2">Lekarze z tą specjalizacją:</p>
            <ul class="space-y-1">
                @foreach($specialization->doctors()->with('user')->limit(5)->get() as $doctor)
                <li class="text-sm text-gray-600">• {{ $doctor->user->full_name }}</li>
                @endforeach
                @if($specialization->doctors()->count() > 5)
                <li class="text-sm text-gray-500">... i {{ $specialization->doctors()->count() - 5 }} więcej</li>
                @endif
            </ul>
        </div>
        @endif
    </div>

    <!-- Usuń specjalizację -->
    @if($specialization->doctors()->count() === 0)
    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6 mt-6">
        <h3 class="text-lg font-semibold text-red-900 mb-4">Strefa niebezpieczna</h3>
        <p class="text-sm text-gray-600 mb-4">
            Usunięcie specjalizacji jest nieodwracalne. Ta akcja jest możliwa tylko wtedy, gdy żaden lekarz nie ma przypisanej tej specjalizacji.
        </p>
        
        <form action="{{ route('admin.specializations.destroy', $specialization) }}" method="POST"
              onsubmit="return confirm('Czy na pewno chcesz usunąć tę specjalizację? Ta operacja jest nieodwracalna.')">
            @csrf
            @method('DELETE')
            
            <button type="submit" 
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                Usuń specjalizację
            </button>
        </form>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
        <div class="flex">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="ml-3 text-sm text-yellow-700">
                Nie można usunąć tej specjalizacji, ponieważ jest przypisana do {{ $specialization->doctors()->count() }} 
                {{ $specialization->doctors()->count() === 1 ? 'lekarza' : 'lekarzy' }}.
                Najpierw usuń ją z profili lekarzy.
            </div>
        </div>
    </div>
    @endif
</div>
@endsection