@extends('layouts.admin')
@section('title', 'Paramètres')
@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Paramètres de l'application</h2>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-3"><i class="fas fa-building mr-2 text-orange-400"></i>Informations société</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la société *</label>
                    <input type="text" name="company_name" value="{{ $settings['company_name']->value ?? 'LocaFiesta' }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SIRET</label>
                    <input type="text" name="company_siret" value="{{ $settings['company_siret']->value ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                    <input type="text" name="company_address" value="{{ $settings['company_address']->value ?? '' }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input type="text" name="company_phone" value="{{ $settings['company_phone']->value ?? '' }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="company_email" value="{{ $settings['company_email']->value ?? '' }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-3"><i class="fas fa-euro-sign mr-2 text-orange-400"></i>Paiement & réservation</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Acompte (%)</label>
                    <div class="relative">
                        <input type="number" name="deposit_percentage" value="{{ $settings['deposit_percentage']->value ?? 30 }}" min="1" max="100" required class="w-full border rounded-lg px-3 py-2 text-sm pr-8">
                        <span class="absolute right-3 top-2.5 text-gray-400 text-sm">%</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Délai annulation (h)</label>
                    <input type="number" name="cancellation_hours" value="{{ $settings['cancellation_hours']->value ?? 48 }}" min="1" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Préfixe facture</label>
                    <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix']->value ?? 'FAC' }}" maxlength="10" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-3"><i class="fas fa-file-contract mr-2 text-orange-400"></i>Conditions Générales de Location</h3>
            <textarea name="cgv_text" rows="12" class="w-full border rounded-lg px-3 py-2 text-sm font-mono text-xs">{{ $settings['cgv_text']->value ?? '' }}</textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600">
                <i class="fas fa-save mr-2"></i>Sauvegarder les paramètres
            </button>
        </div>
    </form>
</div>
@endsection
