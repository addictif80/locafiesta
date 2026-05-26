@extends('layouts.admin')

@section('title', 'Réservations')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold text-gray-800">Réservations</h2>
    <a href="{{ route('admin.reservations.create') }}"
       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus"></i> Nouvelle réservation
    </a>
</div>

<!-- Filtres -->
<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.reservations.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-3">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Référence, nom client..."
                       class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
            </div>
        </div>

        <div>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none bg-white">
                <option value="">Tous les statuts</option>
                <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>En attente de paiement</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminée</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                <option value="cancelled_no_refund" {{ request('status') === 'cancelled_no_refund' ? 'selected' : '' }}>Annulée (sans remboursement)</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
            <span class="text-gray-400 text-sm">→</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
        </div>

        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <i class="fas fa-filter mr-1"></i> Filtrer
        </button>
        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
        <a href="{{ route('admin.reservations.index') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition text-center">
            <i class="fas fa-times mr-1"></i> Réinitialiser
        </a>
        @endif
    </form>
</div>

<div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Matériels</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Acompte</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reservations as $reservation)
                @php
                    $statusColors = [
                        'pending_payment'    => 'bg-yellow-100 text-yellow-700',
                        'confirmed'          => 'bg-blue-100 text-blue-700',
                        'in_progress'        => 'bg-green-100 text-green-700',
                        'completed'          => 'bg-gray-100 text-gray-600',
                        'cancelled'          => 'bg-red-100 text-red-700',
                        'cancelled_no_refund'=> 'bg-red-100 text-red-700',
                    ];
                    $colorClass = $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-700 font-medium">{{ $reservation->reference }}</td>
                    <td class="px-4 py-3">
                        @if($reservation->client)
                        <a href="{{ route('admin.clients.show', $reservation->client) }}" class="text-gray-800 hover:text-orange-500 font-medium">
                            {{ $reservation->client->first_name }} {{ $reservation->client->last_name }}
                        </a>
                        @else
                        <span class="text-gray-400 italic text-xs">Client supprimé</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                        {{ $reservation->start_date->format('d/m/Y') }}<br>
                        <span class="text-gray-400">→ {{ $reservation->end_date->format('d/m/Y') }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        @foreach($reservation->items->take(2) as $item)
                            <span class="text-xs">{{ $item->equipment->name }}</span>@if(!$loop->last),@endif
                        @endforeach
                        @if($reservation->items->count() > 2)
                            <span class="text-xs text-gray-400">+{{ $reservation->items->count() - 2 }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-800">{{ number_format($reservation->total_amount, 2, ',', ' ') }} €</td>
                    <td class="px-4 py-3 text-gray-600">{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                            {{ $reservation->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.reservations.show', $reservation) }}"
                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-calendar-check text-4xl mb-3 block"></i>
                        Aucune réservation trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reservations->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $reservations->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
