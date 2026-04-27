@extends('layouts.admin')
@section('title', 'Codes promo')
@section('content')
<div class="max-w-5xl mx-auto">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Codes promo</h2>
    <div class="grid grid-cols-3 gap-6">
        <div>
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Créer un code</h3>
                <form method="POST" action="{{ route('admin.promo-codes.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Code *</label>
                        <input type="text" name="code" value="{{ old('code') }}" required placeholder="EX: FETE2024" class="w-full border rounded-lg px-3 py-2 text-sm uppercase">
                        @error('code')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="percentage">Pourcentage (%)</option>
                                <option value="fixed">Montant fixe (€)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Valeur *</label>
                            <input type="number" name="value" value="{{ old('value') }}" required min="0" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nb d'utilisations max</label>
                        <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="Illimité" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Valide du</label>
                            <input type="date" name="valid_from" value="{{ old('valid_from') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Au</label>
                            <input type="date" name="valid_until" value="{{ old('valid_until') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="description" value="{{ old('description') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded-lg text-sm font-medium hover:bg-orange-600">Créer le code</button>
                </form>
            </div>
        </div>
        <div class="col-span-2">
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Code</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Remise</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Utilisations</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Validité</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Statut</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($codes as $code)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono font-bold text-orange-600">{{ $code->code }}</td>
                            <td class="px-4 py-3">
                                @if($code->type === 'percentage') -{{ $code->value }}%
                                @else -{{ number_format($code->value, 2, ',', ' ') }}€ @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $code->used_count }}{{ $code->max_uses ? '/'.$code->max_uses : '' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $code->valid_from?->format('d/m/Y') ?? '∞' }} → {{ $code->valid_until?->format('d/m/Y') ?? '∞' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs {{ $code->isValid() ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $code->isValid() ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 flex gap-2">
                                <form method="POST" action="{{ route('admin.promo-codes.toggle', $code) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-500 hover:text-blue-700">{{ $code->is_active ? 'Désactiver' : 'Activer' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.promo-codes.destroy', $code) }}" onsubmit="return confirm('Supprimer ce code ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Aucun code promo</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t">{{ $codes->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
