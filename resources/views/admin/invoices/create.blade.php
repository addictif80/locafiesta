@extends('layouts.admin')
@section('title', 'Nouvelle facture')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.factures.index') }}" class="text-sm text-orange-500 hover:underline"><i class="fas fa-arrow-left mr-1"></i>Factures</a>
        <h2 class="text-xl font-bold text-gray-800 mt-2">Créer une facture</h2>
    </div>
    <form method="POST" action="{{ route('admin.factures.store') }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client *</label>
                <select name="client_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Sélectionner...</option>
                    @foreach($clients as $client)<option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->full_name }}</option>@endforeach
                </select>
                @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Réservation (optionnel)</label>
                <select name="reservation_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Aucune</option>
                    @foreach($reservations as $res)<option value="{{ $res->id }}" {{ old('reservation_id') == $res->id ? 'selected' : '' }}>{{ $res->reference }} — {{ $res->client->full_name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                <select name="type" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="deposit" {{ old('type') === 'deposit' ? 'selected' : '' }}>Acompte</option>
                    <option value="balance" {{ old('type') === 'balance' ? 'selected' : '' }}>Solde</option>
                    <option value="damage" {{ old('type') === 'damage' ? 'selected' : '' }}>Dégradations</option>
                    <option value="manual" {{ old('type') === 'manual' ? 'selected' : '' }}>Manuelle</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Montant (€) *</label>
                <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="pending">En attente</option>
                    <option value="paid">Payée</option>
                    <option value="cancelled">Annulée</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.factures.index') }}" class="px-6 py-2.5 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600">Créer la facture</button>
        </div>
    </form>
</div>
@endsection
