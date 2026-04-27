@extends('layouts.admin')
@section('title', 'Tableau de bord')
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Réservations ce mois</span>
            <span class="text-orange-400 bg-orange-50 p-2 rounded-lg"><i class="fas fa-calendar-check"></i></span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['reservations_month'] }}</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">En cours / Confirmées</span>
            <span class="text-blue-400 bg-blue-50 p-2 rounded-lg"><i class="fas fa-clock"></i></span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['active_reservations'] }}</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Retours à faire</span>
            <span class="text-red-400 bg-red-50 p-2 rounded-lg"><i class="fas fa-truck-ramp-box"></i></span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['pending_returns'] }}</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Clients</span>
            <span class="text-green-400 bg-green-50 p-2 rounded-lg"><i class="fas fa-users"></i></span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['clients_count'] }}</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">CA ce mois (encaissé)</span>
            <span class="text-purple-400 bg-purple-50 p-2 rounded-lg"><i class="fas fa-euro-sign"></i></span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['revenue_month'], 2, ',', ' ') }} €</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Nouvelles réservations aujourd'hui</span>
            <span class="text-amber-400 bg-amber-50 p-2 rounded-lg"><i class="fas fa-star"></i></span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['reservations_today'] }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Prochaines locations</h3>
            <a href="{{ route('admin.reservations.index') }}" class="text-sm text-orange-500 hover:underline">Tout voir</a>
        </div>
        <div class="divide-y">
            @forelse($upcoming as $res)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $res->client->full_name }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $res->start_date->format('d/m/Y') }} → {{ $res->end_date->format('d/m/Y') }}
                        · {{ $res->items->count() }} matériel(s)
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        {{ $res->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                        {{ $res->status_label }}
                    </span>
                    <a href="{{ route('admin.reservations.show', $res) }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">Aucune location à venir</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Dernières réservations</h3>
        </div>
        <div class="divide-y">
            @forelse($recent_reservations as $res)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $res->reference }}</div>
                    <div class="text-xs text-gray-500">{{ $res->client->full_name }} · {{ $res->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-semibold text-sm text-gray-700">{{ number_format($res->total_amount, 2, ',', ' ') }} €</span>
                    <a href="{{ route('admin.reservations.show', $res) }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">Aucune réservation</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
