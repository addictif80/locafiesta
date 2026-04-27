@extends('layouts.admin')
@section('title', 'Modifier réservation')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-sm text-orange-500 hover:underline"><i class="fas fa-arrow-left mr-1"></i>{{ $reservation->reference }}</a>
        <h2 class="text-xl font-bold text-gray-800 mt-2">Modifier la réservation</h2>
    </div>
    <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $reservation->start_date->format('Y-m-d')) }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Heure de début</label>
                    <select name="start_time" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @for($h = 8; $h <= 20; $h++)
                        <option value="{{ sprintf('%02d:00:00', $h) }}" {{ old('start_time', $reservation->start_time) === sprintf('%02d:00:00', $h) ? 'selected' : '' }}>{{ sprintf('%02dh00', $h) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $reservation->end_date->format('Y-m-d')) }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Heure de fin</label>
                    <select name="end_time" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @for($h = 8; $h <= 20; $h++)
                        <option value="{{ sprintf('%02d:00:00', $h) }}" {{ old('end_time', $reservation->end_time) === sprintf('%02d:00:00', $h) ? 'selected' : '' }}>{{ sprintf('%02dh00', $h) }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                <select name="status" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    @foreach(['pending_payment' => 'En attente de paiement', 'confirmed' => 'Confirmée', 'in_progress' => 'En cours', 'completed' => 'Terminée', 'cancelled' => 'Annulée (remboursée)', 'cancelled_no_refund' => 'Annulée (non remboursée)'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $reservation->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes admin</label>
                <textarea name="admin_notes" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('admin_notes', $reservation->admin_notes) }}</textarea>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.reservations.show', $reservation) }}" class="px-6 py-2.5 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600">Sauvegarder</button>
        </div>
    </form>
</div>
@endsection
