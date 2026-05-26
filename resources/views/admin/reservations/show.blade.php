@extends('layouts.admin')
@section('title', 'Réservation ' . $reservation->reference)
@section('content')
<div class="max-w-5xl mx-auto" x-data="{ cancelModal: false }">
    @if($reservation->isLate())
    <div class="mb-5 bg-red-50 border border-red-300 rounded-xl px-5 py-4 flex items-start gap-4">
        <i class="fas fa-exclamation-triangle text-red-500 text-xl mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="font-semibold text-red-700">Retour en retard</p>
            <p class="text-sm text-red-600 mt-0.5">
                Cette réservation aurait dû être retournée le <strong>{{ $reservation->end_date->format('d/m/Y') }} à {{ substr($reservation->end_time, 0, 5) }}</strong>.
                Retard : <strong>{{ $reservation->days_late }} jour(s)</strong>.
            </p>
        </div>
    </div>
    @endif
    <div class="mb-6 flex items-start justify-between">
        <div>
            <a href="{{ route('admin.reservations.index') }}" class="text-sm text-orange-500 hover:underline"><i class="fas fa-arrow-left mr-1"></i>Réservations</a>
            <div class="flex items-center gap-3 mt-2">
                <h2 class="text-xl font-bold text-gray-800">{{ $reservation->reference }}</h2>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $reservation->status === 'confirmed' ? 'bg-blue-100 text-blue-700' :
                       ($reservation->status === 'in_progress' ? 'bg-green-100 text-green-700' :
                       ($reservation->status === 'completed' ? 'bg-gray-100 text-gray-700' :
                       ($reservation->status === 'pending_payment' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'))) }}">
                    {{ $reservation->status_label }}
                </span>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap justify-end">
            <a href="{{ route('admin.reservations.contract', $reservation) }}"
               class="flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-file-contract"></i>Contrat PDF
            </a>
            <form method="POST" action="{{ route('admin.reservations.send-contract', $reservation) }}" class="inline">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg text-sm hover:bg-blue-100">
                    <i class="fas fa-envelope"></i>Envoyer par mail
                </button>
            </form>
            @if($reservation->status === 'confirmed' && !$reservation->departureInspection)
            <a href="{{ route('admin.inspections.create', [$reservation, 'departure']) }}"
               class="flex items-center gap-2 bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600">
                <i class="fas fa-clipboard-list"></i>État des lieux départ
            </a>
            @endif
            @if($reservation->status === 'in_progress' && !$reservation->returnInspection)
            <a href="{{ route('admin.inspections.create', [$reservation, 'return']) }}"
               class="flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600">
                <i class="fas fa-clipboard-check"></i>État des lieux retour
            </a>
            @endif
            @if(!$reservation->isCancelled())
            <button @click="cancelModal = true"
                    class="flex items-center gap-2 bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm hover:bg-red-100">
                <i class="fas fa-times"></i>Annuler
            </button>
            @endif
            <a href="{{ route('admin.reservations.edit', $reservation) }}"
               class="flex items-center gap-2 bg-white border text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                <i class="fas fa-edit"></i>Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-6">
            {{-- Client --}}
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800"><i class="fas fa-user mr-2 text-orange-400"></i>Client</h3>
                    @if($reservation->client)
                    <a href="{{ route('admin.clients.show', $reservation->client) }}" class="text-sm text-orange-500 hover:underline">Voir le profil</a>
                    @endif
                </div>
                <div class="p-5 grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Nom:</span> <span class="font-medium">{{ $reservation->client?->full_name ?? '(client supprimé)' }}</span></div>
                    <div><span class="text-gray-500">Email:</span> <span class="font-medium">{{ $reservation->client?->email ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Téléphone:</span> <span class="font-medium">{{ $reservation->client?->phone ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Adresse:</span> <span class="font-medium">{{ $reservation->client ? $reservation->client->address . ', ' . $reservation->client->postal_code . ' ' . $reservation->client->city : '—' }}</span></div>
                    @if($reservation->use_different_address)
                    <div class="col-span-2 bg-amber-50 rounded-lg p-3">
                        <span class="text-amber-700 text-xs font-medium">Adresse d'utilisation différente:</span>
                        <span class="text-amber-800 ml-1">{{ $reservation->use_address }}, {{ $reservation->use_postal_code }} {{ $reservation->use_city }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Matériels --}}
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-5 py-4 border-b"><h3 class="font-semibold text-gray-800"><i class="fas fa-boxes-stacked mr-2 text-orange-400"></i>Matériel réservé</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-5 py-3 text-left text-gray-600 font-medium">Matériel</th>
                                <th class="px-5 py-3 text-right text-gray-600 font-medium">Tarif/j</th>
                                <th class="px-5 py-3 text-right text-gray-600 font-medium">Jours</th>
                                <th class="px-5 py-3 text-right text-gray-600 font-medium">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($reservation->items as $item)
                            <tr>
                                <td class="px-5 py-3">
                                    <span class="font-medium text-gray-800">{{ $item->equipment->name }}</span>
                                    <span class="text-xs text-gray-400 ml-2">{{ $item->equipment->reference }}</span>
                                </td>
                                <td class="px-5 py-3 text-right">{{ number_format($item->daily_rate, 2, ',', ' ') }} €</td>
                                <td class="px-5 py-3 text-right">{{ $reservation->days_count }}</td>
                                <td class="px-5 py-3 text-right font-medium">{{ number_format($item->daily_rate * $reservation->days_count, 2, ',', ' ') }} €</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- États des lieux --}}
            @if($reservation->inspections->count())
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-5 py-4 border-b"><h3 class="font-semibold text-gray-800"><i class="fas fa-clipboard-list mr-2 text-orange-400"></i>États des lieux</h3></div>
                <div class="divide-y">
                    @foreach($reservation->inspections as $inspection)
                    <div class="px-5 py-4 flex items-center justify-between">
                        <div>
                            <span class="font-medium text-sm">{{ $inspection->type_label }}</span>
                            <span class="text-xs text-gray-500 ml-2">{{ $inspection->signed_at?->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.inspections.show', $inspection) }}" class="text-sm text-orange-500 hover:underline">Voir</a>
                            <a href="{{ route('admin.inspections.pdf', $inspection) }}" class="text-sm text-red-500 hover:underline">PDF</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Caution --}}
            @if($reservation->securityDeposits->count())
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-5 py-4 border-b"><h3 class="font-semibold text-gray-800"><i class="fas fa-money-check mr-2 text-orange-400"></i>Caution</h3></div>
                <div class="divide-y">
                    @foreach($reservation->securityDeposits as $deposit)
                    <div class="px-5 py-4 grid grid-cols-3 gap-4 text-sm">
                        <div><span class="text-gray-500">Phase:</span> <span class="font-medium capitalize">{{ $deposit->phase === 'departure' ? 'Départ' : 'Retour' }}</span></div>
                        <div><span class="text-gray-500">Titulaire:</span> <span class="font-medium">{{ $deposit->holder_name }}</span></div>
                        <div><span class="text-gray-500">Banque:</span> <span class="font-medium">{{ $deposit->bank_name }}</span></div>
                        <div><span class="text-gray-500">N° chèque:</span> <span class="font-medium">{{ $deposit->check_number }}</span></div>
                        <div><span class="text-gray-500">Montant:</span> <span class="font-bold text-gray-800">{{ number_format($deposit->amount, 2, ',', ' ') }} €</span></div>
                        <div>
                            <span class="px-2 py-1 rounded text-xs font-medium
                                {{ $deposit->status === 'held' ? 'bg-blue-100 text-blue-700' :
                                   ($deposit->status === 'returned' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                                {{ $deposit->status_label }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Factures --}}
            @if($reservation->invoices->count())
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-5 py-4 border-b"><h3 class="font-semibold text-gray-800"><i class="fas fa-file-invoice-dollar mr-2 text-orange-400"></i>Factures</h3></div>
                <div class="divide-y">
                    @foreach($reservation->invoices as $invoice)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <span class="font-medium text-sm">{{ $invoice->invoice_number }}</span>
                            <span class="text-xs text-gray-500 ml-2">{{ $invoice->type_label }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-sm">{{ number_format($invoice->amount, 2, ',', ' ') }} €</span>
                            <span class="px-2 py-0.5 rounded text-xs {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $invoice->status_label }}
                            </span>
                            <a href="{{ route('admin.invoices.download', $invoice) }}" class="text-sm text-red-500 hover:text-red-700"><i class="fas fa-file-pdf"></i></a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar financier --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-5 py-4 border-b"><h3 class="font-semibold text-gray-800">Récapitulatif</h3></div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Début:</span><span class="font-medium">{{ $reservation->start_date->format('d/m/Y') }} {{ $reservation->start_time }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Fin:</span><span class="font-medium">{{ $reservation->end_date->format('d/m/Y') }} {{ $reservation->end_time }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Durée:</span><span class="font-medium">{{ $reservation->days_count }} jour(s)</span></div>
                    <hr>
                    <div class="flex justify-between"><span class="text-gray-500">Sous-total:</span><span class="font-medium">{{ number_format($reservation->subtotal, 2, ',', ' ') }} €</span></div>
                    @if($reservation->discount_amount > 0)
                    <div class="flex justify-between text-green-600"><span>Remise promo:</span><span>-{{ number_format($reservation->discount_amount, 2, ',', ' ') }} €</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-base border-t pt-3"><span>Total:</span><span>{{ number_format($reservation->total_amount, 2, ',', ' ') }} €</span></div>
                    <hr>
                    <div class="flex justify-between text-blue-600"><span>Acompte ({{ $reservation->deposit_percentage }}%):</span><span>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</span></div>
                    <div class="flex justify-between text-orange-600"><span>Solde restant:</span><span>{{ number_format($reservation->balance_amount, 2, ',', ' ') }} €</span></div>
                    @if($reservation->deposit_paid_at)
                    <div class="bg-green-50 rounded-lg p-2 text-xs text-green-700">
                        <i class="fas fa-check-circle mr-1"></i>Acompte payé le {{ $reservation->deposit_paid_at->format('d/m/Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>

            @if($reservation->admin_notes)
            <div class="bg-amber-50 rounded-xl border border-amber-200 p-4">
                <h4 class="text-xs font-semibold text-amber-700 mb-2"><i class="fas fa-sticky-note mr-1"></i>Notes admin</h4>
                <p class="text-sm text-amber-800">{{ $reservation->admin_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Modal annulation --}}
    <div x-show="cancelModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full">
            <h3 class="font-bold text-gray-800 mb-4">Annuler la réservation</h3>
            <p class="text-sm text-gray-600 mb-4">Êtes-vous sûr de vouloir annuler la réservation {{ $reservation->reference }} ?</p>
            <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}">
                @csrf
                <textarea name="reason" placeholder="Raison de l'annulation (optionnel)"
                          class="w-full border rounded-lg px-3 py-2 text-sm mb-4 resize-none" rows="3"></textarea>
                <div class="flex gap-3">
                    <button type="button" @click="cancelModal = false" class="flex-1 border rounded-lg py-2 text-sm text-gray-700 hover:bg-gray-50">Retour</button>
                    <button type="submit" class="flex-1 bg-red-500 text-white rounded-lg py-2 text-sm font-medium hover:bg-red-600">Confirmer l'annulation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
