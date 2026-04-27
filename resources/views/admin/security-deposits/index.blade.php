@extends('layouts.admin')

@section('title', 'Cautions')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold text-gray-800">Gestion des cautions (chèques)</h2>
</div>

{{-- Filtres --}}
<form method="GET" class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 outline-none">
            <option value="">Tous</option>
            <option value="held"     {{ request('status') === 'held'     ? 'selected' : '' }}>Conservé</option>
            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Restitué</option>
            <option value="cashed"   {{ request('status') === 'cashed'   ? 'selected' : '' }}>Encaissé</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Phase</label>
        <select name="phase" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 outline-none">
            <option value="">Toutes</option>
            <option value="departure" {{ request('phase') === 'departure' ? 'selected' : '' }}>Départ</option>
            <option value="return"    {{ request('phase') === 'return'    ? 'selected' : '' }}>Retour</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Réf., titulaire, n° chèque…"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 outline-none w-56">
    </div>
    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        <i class="fas fa-search mr-1"></i> Filtrer
    </button>
    @if(request()->hasAny(['status', 'phase', 'search']))
    <a href="{{ route('admin.security-deposits.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
        <i class="fas fa-times mr-1"></i> Réinitialiser
    </a>
    @endif
</form>

{{-- Compteurs rapides --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @php
        $allDeposits = \App\Models\SecurityDeposit::selectRaw('status, count(*) as count, sum(amount) as total')->groupBy('status')->get()->keyBy('status');
    @endphp
    <div class="bg-white rounded-lg border border-yellow-200 shadow-sm p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock text-yellow-600"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Conservés</p>
            <p class="text-xl font-bold text-gray-800">{{ $allDeposits->get('held')?->count ?? 0 }}</p>
            <p class="text-xs text-yellow-600 font-medium">{{ number_format($allDeposits->get('held')?->total ?? 0, 2, ',', ' ') }} €</p>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-green-200 shadow-sm p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-600"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Restitués</p>
            <p class="text-xl font-bold text-gray-800">{{ $allDeposits->get('returned')?->count ?? 0 }}</p>
            <p class="text-xs text-green-600 font-medium">{{ number_format($allDeposits->get('returned')?->total ?? 0, 2, ',', ' ') }} €</p>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-red-200 shadow-sm p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-money-bill text-red-600"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Encaissés</p>
            <p class="text-xl font-bold text-gray-800">{{ $allDeposits->get('cashed')?->count ?? 0 }}</p>
            <p class="text-xs text-red-600 font-medium">{{ number_format($allDeposits->get('cashed')?->total ?? 0, 2, ',', ' ') }} €</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Réservation</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Phase</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Titulaire</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Banque</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">N° chèque</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date action</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($deposits as $deposit)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.reservations.show', $deposit->reservation) }}"
                           class="font-mono text-xs font-medium text-orange-600 hover:text-orange-700">
                            {{ $deposit->reservation->reference }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ $deposit->reservation->client->full_name ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($deposit->phase === 'departure')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <i class="fas fa-arrow-right text-[10px]"></i> Départ
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                <i class="fas fa-arrow-left text-[10px]"></i> Retour
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $deposit->holder_name }}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $deposit->bank_name }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $deposit->check_number }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">
                        {{ number_format($deposit->amount, 2, ',', ' ') }} €
                    </td>
                    <td class="px-4 py-3">
                        @if($deposit->status === 'held')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock text-[10px]"></i> Conservé
                            </span>
                        @elseif($deposit->status === 'returned')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check text-[10px]"></i> Restitué
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-money-bill text-[10px]"></i> Encaissé
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $deposit->action_at ? $deposit->action_at->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="openModal({{ $deposit->id }}, '{{ $deposit->status }}', '{{ e($deposit->notes ?? '') }}')"
                                class="p-1.5 text-orange-500 hover:bg-orange-50 rounded transition" title="Modifier le statut">
                            <i class="fas fa-pencil"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-money-check text-4xl mb-3 block"></i>
                        Aucune caution enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($deposits->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $deposits->links() }}
    </div>
    @endif
</div>

{{-- Modal mise à jour statut --}}
<div id="statusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">
            <i class="fas fa-money-check text-orange-500 mr-2"></i>Mettre à jour le statut
        </h3>
        <form id="statusForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau statut</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-yellow-50 has-[:checked]:border-yellow-400 has-[:checked]:bg-yellow-50">
                        <input type="radio" name="status" value="held" class="text-orange-500">
                        <span class="text-sm font-medium text-yellow-700"><i class="fas fa-clock mr-1"></i> Conservé</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-green-50 has-[:checked]:border-green-400 has-[:checked]:bg-green-50">
                        <input type="radio" name="status" value="returned" class="text-orange-500">
                        <span class="text-sm font-medium text-green-700"><i class="fas fa-check mr-1"></i> Restitué au client</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-red-50 has-[:checked]:border-red-400 has-[:checked]:bg-red-50">
                        <input type="radio" name="status" value="cashed" class="text-orange-500">
                        <span class="text-sm font-medium text-red-700"><i class="fas fa-money-bill mr-1"></i> Encaissé</span>
                    </label>
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optionnel)</label>
                <textarea name="notes" id="modalNotes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 outline-none resize-none"
                          placeholder="Motif, date, commentaire…"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModal()"
                        class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(depositId, currentStatus, currentNotes) {
    const modal = document.getElementById('statusModal');
    const form  = document.getElementById('statusForm');
    form.action = `/admin/cautions/${depositId}/statut`;
    // Pre-select current status
    form.querySelectorAll('input[name="status"]').forEach(r => {
        r.checked = r.value === currentStatus;
    });
    document.getElementById('modalNotes').value = currentNotes;
    modal.classList.remove('hidden');
}
function closeModal() {
    document.getElementById('statusModal').classList.add('hidden');
}
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
