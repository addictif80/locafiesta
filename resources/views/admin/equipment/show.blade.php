@extends('layouts.admin')

@section('title', $equipment->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.materiel.index') }}" class="text-gray-400 hover:text-gray-600 transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="text-xl font-semibold text-gray-800">{{ $equipment->name }}</h2>
    <span class="ml-2">
        @if($equipment->is_active)
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                <i class="fas fa-circle text-[8px]"></i> Actif
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                <i class="fas fa-circle text-[8px]"></i> Inactif
            </span>
        @endif
    </span>
    <div class="ml-auto flex items-center gap-2">
        <a href="{{ route('admin.materiel.edit', $equipment) }}"
           class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <i class="fas fa-pencil"></i> Modifier
        </a>
        <form method="POST" action="{{ route('admin.materiel.destroy', $equipment) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('Supprimer ce matériel ? Cette action est irréversible.')"
                    class="inline-flex items-center gap-2 border border-red-300 text-red-500 hover:bg-red-50 text-sm font-medium px-4 py-2 rounded-lg transition">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne principale -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Galerie photos -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                <i class="fas fa-images text-orange-400 mr-2"></i>Photos
            </h3>
            @if($equipment->photos->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach($equipment->photos as $photo)
                <div class="relative aspect-square">
                    <img src="{{ Storage::url($photo->path) }}" alt="{{ $equipment->name }}"
                         class="w-full h-full object-cover rounded-lg border-2 {{ $photo->is_main ? 'border-orange-400' : 'border-gray-200' }}">
                    @if($photo->is_main)
                        <span class="absolute top-1 left-1 bg-orange-500 text-white text-[10px] px-1.5 py-0.5 rounded font-medium">
                            <i class="fas fa-star mr-0.5"></i> Principale
                        </span>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-10 text-gray-300">
                <i class="fas fa-image text-5xl mb-3"></i>
                <p class="text-sm">Aucune photo pour ce matériel</p>
            </div>
            @endif
        </div>

        <!-- Description -->
        @if($equipment->description)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                <i class="fas fa-align-left text-orange-400 mr-2"></i>Description
            </h3>
            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $equipment->description }}</div>
        </div>
        @endif

        <!-- Checklist état des lieux -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                <i class="fas fa-clipboard-list text-orange-400 mr-2"></i>Checklist état des lieux
            </h3>
            @if($equipment->checklistItems->count())
            <ul class="divide-y divide-gray-100">
                @foreach($equipment->checklistItems as $item)
                <li class="py-3 flex items-start gap-3">
                    <div class="mt-0.5 w-5 h-5 rounded border-2 border-gray-300 flex-shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $item->label }}</p>
                        @if($item->description)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->description }}</p>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-sm text-gray-400">Aucun élément de checklist configuré.</p>
            @endif
        </div>

        <!-- Barème dégradations -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                <i class="fas fa-triangle-exclamation text-orange-400 mr-2"></i>Barème dégradations
            </h3>
            @if($equipment->degradationItems->count())
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase bg-gray-50">
                            <th class="text-left px-3 py-2 rounded-l">Label</th>
                            <th class="text-left px-3 py-2">Description</th>
                            <th class="text-right px-3 py-2 rounded-r">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($equipment->degradationItems as $item)
                        <tr>
                            <td class="px-3 py-2.5 font-medium text-gray-800">{{ $item->label }}</td>
                            <td class="px-3 py-2.5 text-gray-500">{{ $item->description ?? '—' }}</td>
                            <td class="px-3 py-2.5 text-right font-semibold text-gray-800">{{ number_format($item->amount, 2, ',', ' ') }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-400">Aucun barème de dégradation configuré.</p>
            @endif
        </div>

    </div>

    <!-- Colonne latérale -->
    <div class="space-y-6">

        <!-- Informations -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                Informations
            </h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Référence interne</dt>
                    <dd class="mt-1 font-mono text-gray-700">{{ $equipment->internal_reference ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Tarif journalier</dt>
                    <dd class="mt-1 font-semibold text-gray-800 text-base">{{ number_format($equipment->daily_rate, 2, ',', ' ') }} €</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Caution</dt>
                    <dd class="mt-1 font-semibold text-gray-800">{{ number_format($equipment->deposit, 2, ',', ' ') }} €</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Statut</dt>
                    <dd class="mt-1">
                        @if($equipment->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-circle text-[8px]"></i> Actif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                <i class="fas fa-circle text-[8px]"></i> Inactif
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Réservations</dt>
                    <dd class="mt-1 text-gray-700">{{ $equipment->reservations_count ?? $equipment->reservations->count() }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Créé le</dt>
                    <dd class="mt-1 text-gray-700">{{ $equipment->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">Actions</h3>
            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.materiel.edit', $equipment) }}"
                   class="w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-medium py-2.5 rounded-lg text-sm transition">
                    <i class="fas fa-pencil mr-2"></i>Modifier ce matériel
                </a>
                <form method="POST" action="{{ route('admin.materiel.destroy', $equipment) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Supprimer ce matériel ? Cette action est irréversible.')"
                            class="w-full border border-red-300 text-red-500 hover:bg-red-50 font-medium py-2.5 rounded-lg text-sm transition">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
