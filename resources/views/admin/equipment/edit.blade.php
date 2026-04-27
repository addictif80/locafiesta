@extends('layouts.admin')

@section('title', 'Modifier — ' . $equipment->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.materiel.index') }}" class="text-gray-400 hover:text-gray-600 transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="text-xl font-semibold text-gray-800">Modifier : {{ $equipment->name }}</h2>
</div>

<form id="equipment-edit-form" method="POST" action="{{ route('admin.materiel.update', $equipment) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Informations générales -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    Informations générales
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $equipment->name) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Référence interne <span class="text-red-500">*</span></label>
                        <input type="text" id="reference" name="reference" value="{{ old('reference', $equipment->reference) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('reference') border-red-400 @enderror">
                        @error('reference')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="5"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none resize-y @error('description') border-red-400 @enderror">{{ old('description', $equipment->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Photos existantes -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    Photos existantes
                </h3>
                @if($equipment->photos->count())
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
                    @foreach($equipment->photos as $photo)
                    <div class="relative group">
                        <img src="{{ Storage::url($photo->path) }}" alt=""
                             class="w-full aspect-square object-cover rounded-lg border-2 {{ $photo->is_primary ? 'border-orange-400' : 'border-gray-200' }}">
                        @if($photo->is_primary)
                            <span class="absolute top-1 left-1 bg-orange-500 text-white text-[10px] px-1.5 py-0.5 rounded font-medium">
                                Principale
                            </span>
                        @endif
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-2">
                            @if(!$photo->is_primary)
                            <form method="POST" action="{{ route('admin.equipment.photo.primary', $photo) }}">
                                @csrf
                                <button type="submit" class="bg-orange-500 text-white text-xs px-2 py-1 rounded hover:bg-orange-600" title="Définir comme principale">
                                    <i class="fas fa-star"></i>
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.equipment.photo.delete', $photo) }}"
                                  onsubmit="return confirm('Supprimer cette photo ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white text-xs px-2 py-1 rounded hover:bg-red-600" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400 mb-4">Aucune photo pour ce matériel.</p>
                @endif

                <div>
                    <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">Ajouter de nouvelles photos</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-orange-400 transition cursor-pointer"
                         onclick="document.getElementById('photos').click()">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 mb-1"></i>
                        <p class="text-xs text-gray-500">Cliquez pour sélectionner des photos</p>
                    </div>
                    <input type="file" id="photos" name="photos[]" accept="image/*" multiple class="hidden"
                           onchange="previewPhotos(this)">
                    @error('photos.*')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <div id="photo-preview" class="grid grid-cols-4 gap-3 mt-3"></div>
                </div>
            </div>
            </form>{{-- fin formulaire principal --}}

            <!-- Checklist état des lieux -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    <i class="fas fa-clipboard-list text-orange-400 mr-2"></i>Checklist état des lieux
                </h3>

                @if($equipment->checklistItems->count())
                <ul class="divide-y divide-gray-100 mb-4">
                    @foreach($equipment->checklistItems as $item)
                    <li class="py-2.5 flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">{{ $item->label }}</p>
                            @if($item->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $item->description }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.equipment.checklist.destroy', $item) }}"
                              onsubmit="return confirm('Supprimer cet élément ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs p-1 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-sm text-gray-400 mb-4">Aucun élément de checklist.</p>
                @endif

                <!-- Formulaire ajout -->
                <form method="POST" action="{{ route('admin.equipment.checklist.store', $equipment) }}"
                      class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    @csrf
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Ajouter un élément</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Label <span class="text-red-500">*</span></label>
                            <input type="text" name="label"
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" name="description"
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
                        </div>
                        <button type="submit"
                                class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium px-3 py-1.5 rounded transition">
                            <i class="fas fa-plus mr-1"></i> Ajouter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Barème dégradations -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    <i class="fas fa-triangle-exclamation text-orange-400 mr-2"></i>Barème dégradations
                </h3>

                @if($equipment->damageScaleItems->count())
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-500 uppercase">
                                <th class="text-left py-2 pr-4">Label</th>
                                <th class="text-left py-2 pr-4">Description</th>
                                <th class="text-right py-2 pr-4">Montant</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($equipment->damageScaleItems as $item)
                            <tr>
                                <td class="py-2 pr-4 font-medium text-gray-800">{{ $item->label }}</td>
                                <td class="py-2 pr-4 text-gray-500">{{ $item->description ?? '—' }}</td>
                                <td class="py-2 pr-4 text-right font-medium text-gray-800">{{ number_format($item->amount, 2, ',', ' ') }} €</td>
                                <td class="py-2">
                                    <form method="POST" action="{{ route('admin.equipment.damage-scale.destroy', $item) }}"
                                          onsubmit="return confirm('Supprimer ce barème ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs p-1 transition">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400 mb-4">Aucun barème de dégradation.</p>
                @endif

                <!-- Formulaire ajout -->
                <form method="POST" action="{{ route('admin.equipment.damage-scale.store', $equipment) }}"
                      class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    @csrf
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Ajouter un barème</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Label <span class="text-red-500">*</span></label>
                            <input type="text" name="label"
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" name="description"
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Montant (€) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
                        </div>
                    </div>
                    <button type="submit"
                            class="mt-3 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium px-3 py-1.5 rounded transition">
                        <i class="fas fa-plus mr-1"></i> Ajouter
                    </button>
                </form>
            </div>

        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">

            <!-- Tarification -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    Tarification
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="daily_rate" class="block text-sm font-medium text-gray-700 mb-1">Tarif journalier (€) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $equipment->daily_rate) }}"
                                   step="0.01" min="0" form="equipment-edit-form"
                                   class="w-full border border-gray-300 rounded-lg pl-3 pr-8 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('daily_rate') border-red-400 @enderror">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">€</span>
                        </div>
                        @error('daily_rate')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deposit_amount" class="block text-sm font-medium text-gray-700 mb-1">Caution (€)</label>
                        <div class="relative">
                            <input type="number" id="deposit_amount" name="deposit_amount" value="{{ old('deposit_amount', $equipment->deposit_amount) }}"
                                   step="0.01" min="0" form="equipment-edit-form"
                                   class="w-full border border-gray-300 rounded-lg pl-3 pr-8 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('deposit_amount') border-red-400 @enderror">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">€</span>
                        </div>
                        @error('deposit_amount')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    Statut
                </h3>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $equipment->is_active) ? 'checked' : '' }}
                           form="equipment-edit-form"
                           class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-300">
                    <span class="text-sm font-medium text-gray-700">Matériel actif</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2">
                <button type="submit" form="equipment-edit-form"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2.5 rounded-lg text-sm transition">
                    <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                </button>
                <a href="{{ route('admin.materiel.show', $equipment) }}"
                   class="w-full text-center border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 rounded-lg text-sm transition">
                    Annuler
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewPhotos(input) {
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative aspect-square';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg border border-gray-200">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
