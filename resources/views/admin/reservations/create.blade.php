@extends('layouts.admin')
@section('title', 'Nouvelle réservation')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.reservations.index') }}" class="text-sm text-orange-500 hover:underline"><i class="fas fa-arrow-left mr-1"></i>Réservations</a>
        <h2 class="text-xl font-bold text-gray-800 mt-2">Créer une réservation</h2>
    </div>
    <form method="POST" action="{{ route('admin.reservations.store') }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-3">Client & matériel</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client *</label>
                <select name="client_id" required class="w-full border rounded-lg px-3 py-2 text-sm @error('client_id') border-red-400 @enderror">
                    <option value="">Sélectionner un client</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->full_name }} ({{ $client->email }})
                    </option>
                    @endforeach
                </select>
                @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Matériels *</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($equipment as $equip)
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-orange-50 has-[:checked]:border-orange-400 has-[:checked]:bg-orange-50">
                        <input type="checkbox" name="equipment_ids[]" value="{{ $equip->id }}"
                               {{ in_array($equip->id, old('equipment_ids', [])) ? 'checked' : '' }}>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-medium text-gray-800">{{ $equip->name }}</span>
                            <span class="text-xs text-gray-400 block">{{ $equip->reference }} — {{ number_format($equip->daily_rate, 2, ',', ' ') }} €/j</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('equipment_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-3">Dates et horaires</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Heure de début *</label>
                    <select name="start_time" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @for($h = 8; $h <= 20; $h++)
                        <option value="{{ sprintf('%02d:00:00', $h) }}" {{ old('start_time', '09:00:00') === sprintf('%02d:00:00', $h) ? 'selected' : '' }}>{{ sprintf('%02dh00', $h) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Heure de fin *</label>
                    <select name="end_time" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @for($h = 8; $h <= 20; $h++)
                        <option value="{{ sprintf('%02d:00:00', $h) }}" {{ old('end_time', '18:00:00') === sprintf('%02d:00:00', $h) ? 'selected' : '' }}>{{ sprintf('%02dh00', $h) }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4" x-data="{ diff: false }">
            <h3 class="font-semibold text-gray-800 border-b pb-3">Adresse d'utilisation</h3>
            <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input type="checkbox" name="use_different_address" value="1" x-model="diff" {{ old('use_different_address') ? 'checked' : '' }}>
                Adresse d'utilisation différente de l'adresse de facturation
            </label>
            <div x-show="diff" x-cloak class="grid grid-cols-3 gap-4">
                <div class="col-span-3">
                    <input type="text" name="use_address" value="{{ old('use_address') }}" placeholder="Adresse d'utilisation *" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <input type="text" name="use_postal_code" value="{{ old('use_postal_code') }}" placeholder="Code postal *" maxlength="10" class="border rounded-lg px-3 py-2 text-sm">
                <div class="col-span-2">
                    <input type="text" name="use_city" value="{{ old('use_city') }}" placeholder="Ville *" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code promo</label>
                    <input type="text" name="promo_code" value="{{ old('promo_code') }}" placeholder="Optionnel" class="w-full border rounded-lg px-3 py-2 text-sm uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes admin</label>
                    <input type="text" name="admin_notes" value="{{ old('admin_notes') }}" placeholder="Optionnel" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.reservations.index') }}" class="px-6 py-2.5 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600">Créer la réservation</button>
        </div>
    </form>
</div>
@endsection
