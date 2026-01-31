@extends('layouts.admin')

@section('title', 'Zarządzanie użytkownikami')
@section('page-title', 'Zarządzanie użytkownikami')
@section('page-subtitle', 'Lista wszystkich użytkowników w systemie')

@section('content')
<div class="space-y-6">
    <!-- Statystyki -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="text-sm font-medium text-gray-600">Wszyscy</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['all'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-yellow-200 bg-yellow-50">
            <div class="text-sm font-medium text-yellow-700">Oczekujące</div>
            <div class="text-3xl font-bold text-yellow-900 mt-2">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-emerald-200 bg-emerald-50">
            <div class="text-sm font-medium text-emerald-700">Aktywni</div>
            <div class="text-3xl font-bold text-emerald-900 mt-2">{{ $stats['active'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-red-200 bg-red-50">
            <div class="text-sm font-medium text-red-700">Nieaktywni</div>
            <div class="text-3xl font-bold text-red-900 mt-2">{{ $stats['inactive'] }}</div>
        </div>
    </div>

    <!-- Filtry -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.users.index', ['status' => 'all']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition {{ $status === 'all' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Wszyscy ({{ $stats['all'] }})
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition {{ $status === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Oczekujące ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'active']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition {{ $status === 'active' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Aktywni ({{ $stats['active'] }})
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition {{ $status === 'inactive' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Nieaktywni ({{ $stats['inactive'] }})
            </a>
        </div>
    </div>

    <!-- Lista użytkowników -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Użytkownik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rola</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data rejestracji</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 {{ $user->status === 'VERIFY' ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $user->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-semibold mr-3">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($user->admin)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Admin</span>
                            @elseif($user->doctor)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Lekarz</span>
                            @elseif($user->patient)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Pacjent</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Brak roli</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->status === 'VERIFY')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Oczekuje</span>
                            @elseif($user->status === 'ACTIVE')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Aktywny</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Nieaktywny</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $user->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                Szczegóły
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginacja -->
        <div class="p-6 border-t border-gray-200">
            {{ $users->appends(['status' => $status])->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <p class="text-gray-500">Brak użytkowników do wyświetlenia</p>
        </div>
        @endif
    </div>
</div>
@endsection