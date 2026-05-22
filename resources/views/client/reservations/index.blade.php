@extends('layouts.client')

@section('title', 'Mes réservations')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Mes réservations</h1>
        @if($clientHasLateReturns)
        <span class="inline-flex items-center gap-2 bg-gray-200 text-gray-500 font-semibold px-4 py-2.5 rounded-lg text-sm cursor-not-allowed" title="Retour en retard non clôturé">
            <i class="fas fa-ban"></i> Nouvelle réservation impossible
        </span>
        @else
        <a href="{{ route('client.reservations.create') }}"
           class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2.5 rounded-lg text-sm transition-colors">
            <i class="fas fa-plus"></i> Nouvelle réservation
        </a>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        @if($reservations->isEmpty())
        <div class="px-6 py-16 text-center">
            <i class="fas fa-calendar-alt text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Aucune réservation</p>
            <p class="text-gray-400 text-sm mb-6">Vous n'avez pas encore effectué de réservation.</p>
            @if(!$clientHasLateReturns)
            <a href="{{ route('client.reservations.create') }}"
               class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-medium px-5 py-2.5 rounded-lg transition-colors">
                <i class="fas fa-plus"></i> Faire une réservation
            </a>
            @endif
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs border-b">
                    <tr>
                        <th class="px-6 py-3 text-left">Référence</th>
                        <th class="px-6 py-3 text-left">Dates</th>
                        <th class="px-6 py-3 text-left">Matériels</th>
                        <th class="px-6 py-3 text-right">Montant</th>
                        <th class="px-6 py-3 text-center">Statut</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reservations as $reservation)
                    @php
                        $statusColors = [
                            'pending_payment'    => 'bg-yellow-100 text-yellow-700',
                            'confirmed'          => 'bg-blue-100 text-blue-700',
                            'in_progress'        => 'bg-green-100 text-green-700',
                            'completed'          => 'bg-gray-100 text-gray-600',
                            'cancelled'          => 'bg-red-100 text-red-700',
                            'cancelled_no_refund'=> 'bg-red-100 text-red-700',
                        ];
                        $color = $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-600';
                        $canCancel = in_array($reservation->status, ['pending_payment', 'confirmed']);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono font-semibold text-gray-900 whitespace-nowrap">
                            {{ $reservation->reference }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            <div>{{ $reservation->start_date->format('d/m/Y') }} <span class="text-gray-400">à</span> {{ $reservation->start_time }}</div>
                            <div class="text-gray-400 text-xs">→ {{ $reservation->end_date->format('d/m/Y') }} à {{ $reservation->end_time }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @foreach($reservation->items as $item)
                                <div class="text-gray-700 truncate max-w-xs">
                                    <i class="fas fa-box text-gray-400 text-xs mr-1"></i>
                                    {{ $item->equipment->name }}
                                </div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">
                            {{ number_format($reservation->total_amount, 2, ',', ' ') }} €
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ $reservation->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('client.reservations.show', $reservation) }}"
                                   class="inline-flex items-center gap-1 text-orange-500 hover:text-orange-600 font-medium text-xs border border-orange-200 rounded-md px-2.5 py-1 hover:bg-orange-50 transition-colors">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                @if($canCancel)
                                <a href="{{ route('client.reservations.show', $reservation) }}#cancel"
                                   class="inline-flex items-center gap-1 text-red-500 hover:text-red-600 font-medium text-xs border border-red-200 rounded-md px-2.5 py-1 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($reservations->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $reservations->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
