@extends('layouts.admin')

@section('title', 'États des lieux')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold text-gray-800">États des lieux</h2>
</div>

{{-- Filtres --}}
<form method="GET" class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
        <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 outline-none">
            <option value="">Tous</option>
            <option value="departure" {{ request('type') === 'departure' ? 'selected' : '' }}>Départ</option>
            <option value="return"    {{ request('type') === 'return'    ? 'selected' : '' }}>Retour</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Réservation</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Référence…"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 outline-none w-44">
    </div>
    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        <i class="fas fa-search mr-1"></i> Filtrer
    </button>
    @if(request()->hasAny(['type', 'search']))
    <a href="{{ route('admin.inspections.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
        <i class="fas fa-times mr-1"></i> Réinitialiser
    </a>
    @endif
</form>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Réservation</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date signature</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($inspections as $inspection)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.reservations.show', $inspection->reservation) }}"
                           class="font-mono text-xs font-medium text-orange-600 hover:text-orange-700">
                            {{ $inspection->reservation->reference }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ $inspection->reservation->client->full_name ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($inspection->isDeparture())
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <i class="fas fa-arrow-right text-[10px]"></i> Départ
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                <i class="fas fa-arrow-left text-[10px]"></i> Retour
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $inspection->signed_at ? $inspection->signed_at->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $inspection->admin?->full_name ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.inspections.show', $inspection) }}"
                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.inspections.pdf', $inspection) }}"
                               class="p-1.5 text-red-500 hover:bg-red-50 rounded transition" title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-clipboard-list text-4xl mb-3 block"></i>
                        Aucun état des lieux enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($inspections->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $inspections->links() }}
    </div>
    @endif
</div>
@endsection
