@extends('layouts.client')

@section('title', 'Réservation ' . $reservation->reference)

@section('content')
<div x-data="{ showCancelModal: false }" class="space-y-6 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('client.reservations.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Mes réservations
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 font-mono">{{ $reservation->reference }}</h1>
        </div>
        @php
            $statusColors = [
                'pending_payment'    => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                'confirmed'          => 'bg-blue-100 text-blue-700 border-blue-200',
                'in_progress'        => 'bg-green-100 text-green-700 border-green-200',
                'completed'          => 'bg-gray-100 text-gray-600 border-gray-200',
                'cancelled'          => 'bg-red-100 text-red-700 border-red-200',
                'cancelled_no_refund'=> 'bg-red-100 text-red-700 border-red-200',
            ];
            $color = $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
        @endphp
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold border {{ $color }}">
            {{ $reservation->status_label }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Dates --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar text-orange-500"></i> Dates de location
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-orange-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Départ</p>
                        <p class="text-lg font-bold text-gray-900">{{ $reservation->start_date->format('d/m/Y') }}</p>
                        <p class="text-sm text-orange-600 font-medium">à {{ $reservation->start_time }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Retour</p>
                        <p class="text-lg font-bold text-gray-900">{{ $reservation->end_date->format('d/m/Y') }}</p>
                        <p class="text-sm text-blue-600 font-medium">à {{ $reservation->end_time }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-3">
                    <i class="fas fa-clock text-gray-400 mr-1"></i>
                    Durée : <strong>{{ $reservation->days_count }} jour(s)</strong>
                </p>
            </div>

            {{-- Equipment --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-boxes-stacked text-orange-500"></i> Matériels réservés
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs border-b">
                            <tr>
                                <th class="px-4 py-2 text-left">Matériel</th>
                                <th class="px-4 py-2 text-left">Référence</th>
                                <th class="px-4 py-2 text-right">Tarif/j</th>
                                <th class="px-4 py-2 text-right">Jours</th>
                                <th class="px-4 py-2 text-right">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reservation->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $item->equipment->name }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">
                                    {{ $item->equipment->reference }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    {{ number_format($item->daily_rate, 2, ',', ' ') }} €
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    {{ $reservation->days_count }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    {{ number_format($item->daily_rate * $reservation->days_count, 2, ',', ' ') }} €
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Address --}}
            @if($reservation->use_different_address && $reservation->use_address)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-orange-500"></i> Adresse d'utilisation
                </h2>
                <p class="text-gray-700">{{ $reservation->use_address }}</p>
                <p class="text-gray-700">{{ $reservation->use_postal_code }} {{ $reservation->use_city }}</p>
            </div>
            @endif

            {{-- Inspection --}}
            @if($reservation->departure_inspection || $reservation->return_inspection)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-orange-500"></i> États des lieux
                </h2>
                <div class="space-y-3">
                    @if($reservation->departure_inspection)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-sm font-medium text-green-800">État des lieux de départ</span>
                            <span class="text-xs text-green-600">{{ $reservation->departure_inspection->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <a href="{{ route('client.inspections.pdf', $reservation->departure_inspection) }}"
                           class="text-xs text-green-600 hover:text-green-700 border border-green-300 rounded px-2 py-1">
                            <i class="fas fa-download mr-1"></i>PDF
                        </a>
                    </div>
                    @endif
                    @if($reservation->return_inspection)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span class="text-sm font-medium text-blue-800">État des lieux de retour</span>
                            <span class="text-xs text-blue-600">{{ $reservation->return_inspection->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <a href="{{ route('client.inspections.pdf', $reservation->return_inspection) }}"
                           class="text-xs text-blue-600 hover:text-blue-700 border border-blue-300 rounded px-2 py-1">
                            <i class="fas fa-download mr-1"></i>PDF
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- Right column --}}
        <div class="space-y-6">

            {{-- Financial summary --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-receipt text-orange-500"></i> Récapitulatif financier
                </h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Sous-total</span>
                        <span>{{ number_format($reservation->subtotal_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @if($reservation->discount_amount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Remise <span class="text-xs">({{ $reservation->promo_code }})</span></span>
                        <span>- {{ number_format($reservation->discount_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-gray-900 text-base border-t pt-2 mt-2">
                        <span>Total</span>
                        <span>{{ number_format($reservation->total_amount, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between text-green-600 font-medium">
                        <span>Acompte payé</span>
                        <span>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @if($reservation->remaining_amount > 0)
                    <div class="flex justify-between text-orange-600 font-semibold border-t pt-2">
                        <span>Solde restant</span>
                        <span>{{ number_format($reservation->remaining_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Invoices --}}
            @if($reservation->invoices->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-orange-500"></i> Factures
                </h2>
                <div class="space-y-2">
                    @foreach($reservation->invoices as $invoice)
                    <a href="{{ route('client.invoices.download', $invoice) }}"
                       class="flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors group">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($invoice->type) }} — {{ $invoice->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($invoice->amount, 2, ',', ' ') }} €</p>
                            <i class="fas fa-download text-orange-400 group-hover:text-orange-600 text-xs mt-0.5"></i>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Late return penalty --}}
            @if($reservation->isLate())
            @php $penalty = $reservation->days_late * $latePenaltyPerDay; @endphp
            <div class="bg-red-50 border border-red-300 rounded-xl p-5">
                <h2 class="text-base font-semibold text-red-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-clock text-red-500"></i> Retour en retard
                </h2>
                <p class="text-sm text-red-700 mb-3">
                    Votre retour était prévu le <strong>{{ $reservation->end_date->format('d/m/Y') }}</strong> à <strong>{{ substr($reservation->end_time, 0, 5) }}</strong>.
                    Retard constaté : <strong>{{ $reservation->days_late }} jour(s)</strong>.
                </p>
                @if($latePenaltyPerDay > 0)
                <div class="bg-white border-2 border-red-400 rounded-xl p-4 text-center">
                    <div class="text-xs text-red-600 font-semibold uppercase tracking-wide mb-1">Pénalités de retard accumulées</div>
                    <div class="text-4xl font-bold text-red-600">{{ number_format($penalty, 2, ',', ' ') }} €</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $reservation->days_late }} jour(s) × {{ number_format($latePenaltyPerDay, 2, ',', ' ') }} €/jour</div>
                </div>
                @endif
                <p class="text-xs text-red-500 mt-3">Contactez-nous pour organiser le retour dès que possible.</p>
            </div>
            @endif

            {{-- Cancel section --}}
            @if(in_array($reservation->status, ['pending_payment', 'confirmed']))
            <div id="cancel" class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                <h2 class="text-base font-semibold text-red-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-times-circle text-red-500"></i> Annulation
                </h2>
                @if($reservation->canBeCancelledWithRefund())
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                        <p class="text-sm text-green-700 flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
                            <span>Annulation avec remboursement de l'acompte (plus de 48h avant le début)</span>
                        </p>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                        <p class="text-sm text-yellow-700 flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
                            <span>Attention : L'acompte ne sera pas remboursé (moins de 48h avant le début)</span>
                        </p>
                    </div>
                @endif
                <button @click="showCancelModal = true" type="button"
                        class="w-full inline-flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
                    <i class="fas fa-times"></i>
                    Annuler cette réservation
                </button>
            </div>
            @endif

        </div>
    </div>

    {{-- Cancel Modal --}}
    @if(in_array($reservation->status, ['pending_payment', 'confirmed']))
    <div x-show="showCancelModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showCancelModal = false">
        <div @click.stop class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <div class="text-center mb-5">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Confirmer l'annulation</h3>
                <p class="text-sm text-gray-500 mt-2">
                    @if($reservation->canBeCancelledWithRefund())
                        Votre acompte de <strong>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</strong> vous sera remboursé.
                    @else
                        L'acompte de <strong>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</strong> ne sera pas remboursé.
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('client.reservations.cancel', $reservation) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Raison de l'annulation <span class="text-gray-400">(optionnel)</span>
                    </label>
                    <textarea name="reason" rows="3" placeholder="Précisez la raison si vous le souhaitez..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button @click="showCancelModal = false" type="button"
                            class="flex-1 border border-gray-300 text-gray-700 font-medium py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        Retour
                    </button>
                    <button type="submit"
                            class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                        Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
