@extends('layouts.client')
@section('title', 'Tableau de bord')
@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Bonjour, {{ auth()->user()->first_name }} 👋</h1>
    <p class="text-gray-500 mt-1">Bienvenue sur votre espace LocaFiesta</p>
</div>

<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <div class="text-2xl font-bold text-gray-800">{{ $reservations->total() }}</div>
        <div class="text-sm text-gray-500 mt-1">Réservation(s) au total</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <div class="text-2xl font-bold text-orange-500">{{ $reservations->where('status', 'confirmed')->count() }}</div>
        <div class="text-sm text-gray-500 mt-1">En attente</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <a href="{{ route('client.reservations.create') }}" class="flex items-center gap-3 h-full">
            <div class="bg-orange-500 text-white p-3 rounded-xl"><i class="fas fa-plus text-lg"></i></div>
            <div>
                <div class="font-semibold text-gray-800">Nouvelle réservation</div>
                <div class="text-xs text-gray-500">Réserver du matériel</div>
            </div>
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 class="font-semibold text-gray-800">Mes réservations</h3>
        <a href="{{ route('client.reservations.index') }}" class="text-sm text-orange-500 hover:underline">Tout voir</a>
    </div>
    <div class="divide-y">
        @forelse($reservations as $reservation)
        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
            <div>
                <div class="text-sm font-medium text-gray-800">{{ $reservation->reference }}</div>
                <div class="text-xs text-gray-500">
                    {{ $reservation->start_date->format('d/m/Y') }} → {{ $reservation->end_date->format('d/m/Y') }}
                    · {{ $reservation->items->count() }} matériel(s)
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2 py-0.5 rounded text-xs font-medium
                    {{ $reservation->status === 'confirmed' ? 'bg-blue-100 text-blue-700' :
                       ($reservation->status === 'in_progress' ? 'bg-green-100 text-green-700' :
                       ($reservation->status === 'completed' ? 'bg-gray-100 text-gray-700' :
                       ($reservation->status === 'pending_payment' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'))) }}">
                    {{ $reservation->status_label }}
                </span>
                <span class="text-sm font-semibold text-gray-700">{{ number_format($reservation->total_amount, 2, ',', ' ') }} €</span>
                <a href="{{ route('client.reservations.show', $reservation) }}" class="text-orange-500 hover:text-orange-600 text-sm">Voir</a>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center">
            <i class="fas fa-calendar-xmark text-4xl text-gray-200 mb-4"></i>
            <p class="text-gray-500 mb-4">Vous n'avez pas encore de réservation.</p>
            <a href="{{ route('client.reservations.create') }}" class="bg-orange-500 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-orange-600">
                Faire une réservation
            </a>
        </div>
        @endforelse
    </div>
    @if($reservations->hasPages())
    <div class="p-4 border-t">{{ $reservations->links() }}</div>
    @endif
</div>
@endsection
