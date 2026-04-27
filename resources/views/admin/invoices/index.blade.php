@extends('layouts.admin')
@section('title', 'Factures')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">Factures</h2>
    <a href="{{ route('admin.factures.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600">
        <i class="fas fa-plus mr-2"></i>Nouvelle facture
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="p-4 border-b flex gap-3">
        <form method="GET" class="flex gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="N° facture ou client..." class="border rounded-lg px-3 py-2 text-sm flex-1">
            <select name="type" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tous les types</option>
                <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Acompte</option>
                <option value="balance" {{ request('type') === 'balance' ? 'selected' : '' }}>Solde</option>
                <option value="damage" {{ request('type') === 'damage' ? 'selected' : '' }}>Dégradations</option>
                <option value="manual" {{ request('type') === 'manual' ? 'selected' : '' }}>Manuelle</option>
            </select>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payée</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>
            <button type="submit" class="bg-gray-100 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">Filtrer</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left text-gray-600 font-medium">N° Facture</th>
                    <th class="px-5 py-3 text-left text-gray-600 font-medium">Client</th>
                    <th class="px-5 py-3 text-left text-gray-600 font-medium">Réservation</th>
                    <th class="px-5 py-3 text-left text-gray-600 font-medium">Type</th>
                    <th class="px-5 py-3 text-right text-gray-600 font-medium">Montant</th>
                    <th class="px-5 py-3 text-left text-gray-600 font-medium">Statut</th>
                    <th class="px-5 py-3 text-left text-gray-600 font-medium">Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono font-medium text-gray-800">{{ $invoice->invoice_number }}</td>
                    <td class="px-5 py-3">{{ $invoice->client->full_name }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $invoice->reservation?->reference ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700">{{ $invoice->type_label }}</span></td>
                    <td class="px-5 py-3 text-right font-bold">{{ number_format($invoice->amount, 2, ',', ' ') }} €</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded text-xs
                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' :
                               ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $invoice->status_label }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $invoice->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 flex gap-2">
                        <a href="{{ route('admin.factures.show', $invoice) }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.invoices.download', $invoice) }}" class="text-red-400 hover:text-red-600"><i class="fas fa-file-pdf"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400">Aucune facture</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $invoices->links() }}</div>
</div>
@endsection
