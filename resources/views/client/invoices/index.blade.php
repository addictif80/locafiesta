@extends('layouts.client')

@section('title', 'Mes factures')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Mes factures</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        @if($invoices->isEmpty())
        <div class="px-6 py-16 text-center">
            <i class="fas fa-file-invoice text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Aucune facture</p>
            <p class="text-gray-400 text-sm">Vos factures apparaîtront ici après vos réservations.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs border-b">
                    <tr>
                        <th class="px-6 py-3 text-left">Numéro</th>
                        <th class="px-6 py-3 text-left">Type</th>
                        <th class="px-6 py-3 text-left">Réservation</th>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-right">Montant</th>
                        <th class="px-6 py-3 text-center">Statut</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invoices as $invoice)
                    @php
                        $typeColors = [
                            'deposit'  => 'bg-orange-100 text-orange-700',
                            'balance'  => 'bg-blue-100 text-blue-700',
                            'full'     => 'bg-green-100 text-green-700',
                            'credit'   => 'bg-purple-100 text-purple-700',
                        ];
                        $typeLabels = [
                            'deposit'  => 'Acompte',
                            'balance'  => 'Solde',
                            'full'     => 'Facture complète',
                            'credit'   => 'Avoir',
                        ];
                        $statusColors = [
                            'paid'    => 'bg-green-100 text-green-700',
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'overdue' => 'bg-red-100 text-red-700',
                            'cancelled' => 'bg-gray-100 text-gray-500',
                        ];
                        $statusLabels = [
                            'paid'    => 'Payée',
                            'pending' => 'En attente',
                            'overdue' => 'En retard',
                            'cancelled' => 'Annulée',
                        ];
                        $typeColor   = $typeColors[$invoice->type] ?? 'bg-gray-100 text-gray-600';
                        $typeLabel   = $typeLabels[$invoice->type] ?? ucfirst($invoice->type);
                        $statusColor = $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-600';
                        $statusLabel = $statusLabels[$invoice->status] ?? ucfirst($invoice->status);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono font-semibold text-gray-900 whitespace-nowrap">
                            {{ $invoice->invoice_number }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColor }}">
                                {{ $typeLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            @if($invoice->reservation)
                            <a href="{{ route('client.reservations.show', $invoice->reservation) }}"
                               class="font-mono text-orange-600 hover:text-orange-700 hover:underline text-xs">
                                {{ $invoice->reservation->reference }}
                            </a>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ $invoice->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">
                            {{ number_format($invoice->amount, 2, ',', ' ') }} €
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('client.invoices.download', $invoice) }}"
                               class="inline-flex items-center gap-1 text-orange-500 hover:text-orange-600 font-medium text-xs border border-orange-200 rounded-md px-2.5 py-1.5 hover:bg-orange-50 transition-colors whitespace-nowrap">
                                <i class="fas fa-download"></i>
                                Télécharger
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $invoices->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
