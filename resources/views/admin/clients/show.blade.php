@extends('layouts.admin')

@section('title', $client->first_name . ' ' . $client->last_name)

@section('content')
@php $lateReservations = $client->reservations->filter(fn($r) => $r->isLate()); @endphp

@if($lateReservations->isNotEmpty())
<div class="mb-5 bg-red-50 border border-red-300 rounded-xl overflow-hidden">
    <div class="px-5 py-3 bg-red-600 flex items-center gap-3 text-white">
        <i class="fas fa-exclamation-triangle"></i>
        <span class="font-semibold text-sm">{{ $lateReservations->count() }} retour(s) en retard pour ce client</span>
    </div>
    <div class="divide-y divide-red-100">
        @foreach($lateReservations as $res)
        <div class="px-5 py-3 flex items-center justify-between">
            <div>
                <span class="font-mono text-sm font-semibold text-gray-800">{{ $res->reference }}</span>
                <span class="ml-3 text-sm text-red-600 font-medium">
                    Dû le {{ $res->end_date->format('d/m/Y') }} à {{ substr($res->end_time, 0, 5) }}
                    — <strong>{{ $res->days_late }} jour(s) de retard</strong>
                </span>
            </div>
            <a href="{{ route('admin.reservations.show', $res) }}"
               class="text-sm bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700">
                <i class="fas fa-eye mr-1"></i>Voir
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.clients.index') }}" class="text-gray-400 hover:text-gray-600 transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="text-xl font-semibold text-gray-800">{{ $client->first_name }} {{ $client->last_name }}</h2>
    @if($client->is_blacklisted)
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
            <i class="fas fa-ban text-[8px]"></i> Liste noire
        </span>
    @endif
    <div class="ml-auto flex items-center gap-2">
        <form method="POST" action="{{ route('admin.clients.impersonate', $client) }}">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition"
                    title="Se connecter en tant que ce client">
                <i class="fas fa-user-secret"></i> Impersonnifier
            </button>
        </form>
        <a href="{{ route('admin.clients.edit', $client) }}"
           class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <i class="fas fa-pencil"></i> Modifier
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne principale -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Réservations du client -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-700">
                    <i class="fas fa-calendar-check text-orange-400 mr-2"></i>Réservations
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($client->reservations ?? [] as $reservation)
                        <tr class="{{ $reservation->isLate() ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">
                            <td class="px-4 py-3 font-mono text-xs text-gray-700">
                                {{ $reservation->reference }}
                                @if($reservation->isLate())
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold bg-red-600 text-white">EN RETARD</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $reservation->start_date->format('d/m/Y') }} → {{ $reservation->end_date->format('d/m/Y') }}
                                @if($reservation->isLate())
                                    <span class="ml-1 text-red-600 text-xs font-medium">({{ $reservation->days_late }}j de retard)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-medium">{{ number_format($reservation->total_amount, 2, ',', ' ') }} €</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending_payment' => 'bg-yellow-100 text-yellow-700',
                                        'confirmed'       => 'bg-blue-100 text-blue-700',
                                        'in_progress'     => 'bg-green-100 text-green-700',
                                        'completed'       => 'bg-gray-100 text-gray-600',
                                        'cancelled'       => 'bg-red-100 text-red-700',
                                        'cancelled_no_refund' => 'bg-red-100 text-red-700',
                                    ];
                                    $colorClass = $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
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
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Aucune réservation.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Factures du client -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-700">
                    <i class="fas fa-file-invoice-dollar text-orange-400 mr-2"></i>Factures
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($client->invoices ?? [] as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $invoice->number }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $invoice->type_label ?? $invoice->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ number_format($invoice->amount, 2, ',', ' ') }} €</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $invoice->status_label ?? $invoice->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $invoice->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.factures.show', $invoice) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Aucune facture.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Colonne latérale -->
    <div class="space-y-6">

        <!-- Informations client -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                Informations client
            </h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Prénom</dt>
                    <dd class="mt-1 text-gray-700">{{ $client->first_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Nom</dt>
                    <dd class="mt-1 text-gray-700">{{ $client->last_name }}</dd>
                </div>
                @if($client->birth_date)
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Date de naissance</dt>
                    <dd class="mt-1 text-gray-700">{{ $client->birth_date->format('d/m/Y') }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Email</dt>
                    <dd class="mt-1 text-gray-700">
                        <a href="mailto:{{ $client->email }}" class="text-orange-500 hover:underline">{{ $client->email }}</a>
                    </dd>
                </div>
                @if($client->phone)
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Téléphone</dt>
                    <dd class="mt-1 text-gray-700">
                        <a href="tel:{{ $client->phone }}" class="text-orange-500 hover:underline">{{ $client->phone }}</a>
                    </dd>
                </div>
                @endif
                @if($client->address)
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Adresse</dt>
                    <dd class="mt-1 text-gray-600 leading-relaxed">
                        {{ $client->address }}<br>
                        @if($client->postal_code || $client->city)
                            {{ $client->postal_code }} {{ $client->city }}
                        @endif
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Inscrit le</dt>
                    <dd class="mt-1 text-gray-700">{{ $client->created_at->format('d/m/Y à H\hi') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Liste noire -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6" x-data="{ showBlacklistModal: false }">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                <i class="fas fa-ban text-red-400 mr-2"></i>Liste noire
            </h3>

            @if($client->is_blacklisted)
                @if($client->blacklist_reason)
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-xs font-medium text-red-700 mb-1">Raison :</p>
                    <p class="text-sm text-red-600">{{ $client->blacklist_reason }}</p>
                </div>
                @endif
                <form method="POST" action="{{ route('admin.clients.blacklist', $client) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="remove">
                    <button type="submit"
                            onclick="return confirm('Retirer ce client de la liste noire ?')"
                            class="w-full bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-2.5 rounded-lg transition">
                        <i class="fas fa-check mr-2"></i>Retirer de la liste noire
                    </button>
                </form>
            @else
                <button @click="showBlacklistModal = true"
                        class="w-full bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-2.5 rounded-lg transition">
                    <i class="fas fa-ban mr-2"></i>Mettre en liste noire
                </button>

                <!-- Modal -->
                <div x-show="showBlacklistModal" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                    <div @click.outside="showBlacklistModal = false"
                         class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                        <h4 class="text-base font-semibold text-gray-800 mb-4">Mettre en liste noire</h4>
                        <form method="POST" action="{{ route('admin.clients.blacklist', $client) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="add">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Raison <span class="text-red-500">*</span>
                                </label>
                                <textarea name="blacklist_reason" rows="4" required
                                          placeholder="Indiquez la raison de la mise en liste noire..."
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 outline-none resize-none"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="showBlacklistModal = false"
                                        class="flex-1 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 rounded-lg text-sm transition">
                                    Annuler
                                </button>
                                <button type="submit"
                                        class="flex-1 bg-red-500 hover:bg-red-600 text-white font-medium py-2 rounded-lg text-sm transition">
                                    Confirmer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
