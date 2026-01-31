@extends('layouts.admin')

@section('title', 'Specjalizacje')
@section('page-title', 'Specjalizacje')
@section('page-subtitle', 'Zarządzanie specjalizacjami lekarzy')

@section('content')
<div class="space-y-6">
    <!-- Przycisk dodawania -->
    <div class="flex justify-end">
        <a href="{{ route('admin.specializations.create') }}" 
           class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Dodaj specjalizację
        </a>
    </div>

    <!-- Statystyki -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="text-sm font-medium text-gray-600">Wszystkie specjalizacje</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $specializations->total() }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-emerald-200 bg-emerald-50">
            <div class="text-sm font-medium text-emerald-700">Specjalizacje z lekarzami</div>
            <div class="text-3xl font-bold text-emerald-900 mt-2">
                {{ $specializations->where('doctors_count', '>', 0)->count() }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="text-sm font-medium text-gray-600">Nieużywane</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">
                {{ $specializations->where('doctors_count', 0)->count() }}
            </div>
        </div>
    </div>

    <!-- Lista specjalizacji -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($specializations->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nazwa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liczba lekarzy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($specializations as $specialization)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $specialization->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $specialization->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($specialization->doctors_count > 0)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    {{ $specialization->doctors_count }} {{ $specialization->doctors_count === 1 ? 'lekarz' : 'lekarzy' }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                    Brak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.specializations.edit', $specialization) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    Edytuj
                                </a>
                                
                                @if($specialization->doctors_count === 0)
                                <form action="{{ route('admin.specializations.destroy', $specialization) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Czy na pewno chcesz usunąć tę specjalizację?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Usuń
                                    </button>
                                </form>
                                @else
                                <span class="text-gray-400 cursor-not-allowed" title="Nie można usunąć specjalizacji przypisanej do lekarzy">
                                    Usuń
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginacja -->
        @if($specializations->hasPages())
        <div class="p-6 border-t border-gray-200">
            {{ $specializations->links() }}
        </div>
        @endif
        @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Brak specjalizacji</h3>
            <p class="mt-1 text-sm text-gray-500">Rozpocznij od dodania pierwszej specjalizacji.</p>
            <div class="mt-6">
                <a href="{{ route('admin.specializations.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Dodaj specjalizację
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection