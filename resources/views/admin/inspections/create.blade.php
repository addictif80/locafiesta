@extends('layouts.admin')
@section('title', $type === 'departure' ? 'État des lieux de départ' : 'État des lieux de retour')
@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.min.css">
@endpush
@section('content')
<div x-data="inspectionForm()" class="max-w-4xl mx-auto px-2 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-sm text-orange-500 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i>Retour à la réservation {{ $reservation->reference }}
        </a>
        <h2 class="text-xl font-bold text-gray-800 mt-2">
            {{ $type === 'departure' ? '🚀 État des lieux de départ' : '🏁 État des lieux de retour' }}
        </h2>
        <p class="text-sm text-gray-500">
            Client: {{ $reservation->client->full_name }} —
            Du {{ $reservation->start_date->format('d/m/Y') }} au {{ $reservation->end_date->format('d/m/Y') }}
        </p>
    </div>

    <form method="POST" action="{{ route('admin.inspections.store', [$reservation, $type]) }}" enctype="multipart/form-data" id="inspectionForm">
        @csrf

        {{-- Checklist --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="px-4 sm:px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-clipboard-check mr-2 text-orange-400"></i>Checklist état des lieux</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
                @foreach($reservation->items as $item)
                    @foreach($item->equipment->checklistItems as $checklistItem)
                    <div class="border rounded-lg p-4">
                        <div class="mb-3">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-medium text-gray-800 text-sm">{{ $checklistItem->label }}</span>
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $item->equipment->name }}</span>
                            </div>
                            @if($checklistItem->description)
                                <p class="text-xs text-gray-500">{{ $checklistItem->description }}</p>
                            @endif
                            @if($type === 'return' && $reservation->departureInspection)
                                @php
                                    $depItem = $reservation->departureInspection->items
                                        ->where('checklist_item_id', $checklistItem->id)->first();
                                @endphp
                                @if($depItem)
                                    <div class="mt-1 text-xs">
                                        <span class="text-gray-400">État au départ:</span>
                                        <span class="ml-1 font-medium
                                            {{ $depItem->condition === 'good' ? 'text-green-600' :
                                               ($depItem->condition === 'worn' ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $depItem->condition_label }}
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['good' => ['label' => 'Bon état', 'color' => 'green'], 'worn' => ['label' => 'Usure', 'color' => 'yellow'], 'damaged' => ['label' => 'Dégradé', 'color' => 'red'], 'missing' => ['label' => 'Manquant', 'color' => 'gray']] as $value => $meta)
                            <label class="cursor-pointer">
                                <input type="radio" name="items[{{ $loop->parent->index * 100 + $loop->index }}][condition]"
                                       value="{{ $value }}" class="sr-only peer" required
                                       {{ $value === 'good' ? 'checked' : '' }}>
                                <span class="inline-block px-3 py-1.5 text-xs font-medium rounded-full border-2 border-transparent
                                    peer-checked:border-{{ $meta['color'] }}-500 peer-checked:bg-{{ $meta['color'] }}-50 peer-checked:text-{{ $meta['color'] }}-700
                                    bg-gray-50 text-gray-600 hover:bg-gray-100 select-none whitespace-nowrap">
                                    {{ $meta['label'] }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                        <input type="hidden" name="items[{{ $loop->parent->index * 100 + $loop->index }}][checklist_item_id]" value="{{ $checklistItem->id }}">
                        <textarea name="items[{{ $loop->parent->index * 100 + $loop->index }}][notes]"
                                  placeholder="Observations (optionnel)"
                                  class="mt-3 w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-1 focus:ring-orange-400 resize-none" rows="2"></textarea>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Photos --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="px-4 sm:px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-camera mr-2 text-orange-400"></i>Photos</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row gap-3 mb-3">
                    {{-- Galerie --}}
                    <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer border-2 border-dashed border-gray-300 hover:border-orange-400 rounded-xl p-4 transition">
                        <i class="fas fa-images text-orange-400 text-lg"></i>
                        <span class="text-sm font-medium text-gray-700">Depuis la galerie</span>
                        <input type="file" name="photos[]" id="photoGallery" multiple accept="image/*"
                               class="hidden" onchange="addPhotoPreviews(this.files)">
                    </label>
                    {{-- Appareil photo --}}
                    <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer border-2 border-dashed border-orange-200 hover:border-orange-400 bg-orange-50 hover:bg-orange-100 rounded-xl p-4 transition">
                        <i class="fas fa-camera text-orange-500 text-lg"></i>
                        <span class="text-sm font-medium text-orange-700">Prendre une photo</span>
                        <input type="file" name="photos[]" id="photoCamera" accept="image/*" capture="environment"
                               class="hidden" onchange="addPhotoPreviews(this.files)">
                    </label>
                </div>
                <p class="text-xs text-gray-400">Max 5 Mo par photo. Les photos des deux sources sont cumulées.</p>
                <div id="photoPreview" class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
            </div>
        </div>

        {{-- Notes générales --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="px-4 sm:px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-sticky-note mr-2 text-orange-400"></i>Notes générales</h3>
            </div>
            <div class="p-4 sm:p-6">
                <textarea name="general_notes" rows="4" placeholder="Observations générales sur l'état des lieux..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400"></textarea>
            </div>
        </div>

        @if($type === 'departure')
        {{-- Caution --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="px-4 sm:px-6 py-4 border-b bg-amber-50">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-money-check mr-2 text-amber-500"></i>Caution (chèque)</h3>
            </div>
            <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom/Prénom du titulaire *</label>
                    <input type="text" name="deposit_holder_name" required class="w-full border rounded-lg px-3 py-2 text-sm @error('deposit_holder_name') border-red-400 @enderror">
                    @error('deposit_holder_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la banque *</label>
                    <input type="text" name="deposit_bank_name" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse de la banque *</label>
                    <input type="text" name="deposit_bank_address" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro du chèque *</label>
                    <input type="text" name="deposit_check_number" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant du chèque (€) *</label>
                    <input type="number" name="deposit_amount" step="0.01" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>
        @endif

        @if($type === 'return')
        {{-- Dégradations --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="px-4 sm:px-6 py-4 border-b bg-red-50">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>Facturation dégradations</h3>
                <p class="text-xs text-gray-500 mt-1">Les différences avec l'état des lieux de départ sont pré-identifiées. Ajoutez les montants selon votre barème.</p>
            </div>
            <div class="p-4 sm:p-6">
                @foreach($reservation->items as $item)
                    @foreach($item->equipment->damageScaleItems as $scaleItem)
                    <div class="border rounded-lg p-3 mb-3">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <span class="font-medium text-sm text-gray-800">{{ $scaleItem->label }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $item->equipment->name }}</span>
                            </div>
                            <span class="text-sm font-bold text-red-600 shrink-0">{{ number_format($scaleItem->amount, 2, ',', ' ') }} €</span>
                        </div>
                        @if($scaleItem->description)<p class="text-xs text-gray-500 mt-1">{{ $scaleItem->description }}</p>@endif
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Gestion caution retour --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6" x-data="{ action: 'return' }">
            <div class="px-4 sm:px-6 py-4 border-b bg-blue-50">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-money-check mr-2 text-blue-500"></i>Gestion de la caution</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-3 mb-6">
                    <label class="flex items-center gap-3 cursor-pointer p-3 border rounded-lg hover:bg-green-50"
                           :class="action === 'return' ? 'border-green-400 bg-green-50' : 'border-gray-200'">
                        <input type="radio" name="deposit_action" value="return" x-model="action" class="text-green-500 shrink-0">
                        <div>
                            <span class="font-medium text-sm text-gray-800">Restituer le chèque au client</span>
                            <p class="text-xs text-gray-500">Pas de dégradation ou compensée différemment</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-3 border rounded-lg hover:bg-red-50"
                           :class="action === 'cash_new' ? 'border-red-400 bg-red-50' : 'border-gray-200'">
                        <input type="radio" name="deposit_action" value="cash_new" x-model="action" class="text-red-500 shrink-0">
                        <div>
                            <span class="font-medium text-sm text-gray-800">Encaisser et réclamer un nouveau chèque</span>
                            <p class="text-xs text-gray-500">En règlement des dégradations</p>
                        </div>
                    </label>
                </div>
                <div x-show="action === 'cash_new'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t pt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titulaire du nouveau chèque *</label>
                        <input type="text" name="new_holder_name" :required="action === 'cash_new'" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Banque *</label>
                        <input type="text" name="new_bank_name" :required="action === 'cash_new'" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse banque *</label>
                        <input type="text" name="new_bank_address" :required="action === 'cash_new'" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">N° chèque *</label>
                        <input type="text" name="new_check_number" :required="action === 'cash_new'" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant (€) *</label>
                        <input type="number" name="new_check_amount" step="0.01" min="0" :required="action === 'cash_new'" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Signatures --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="px-4 sm:px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-signature mr-2 text-orange-400"></i>Signatures</h3>
            </div>
            <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Signature du client</label>
                    <div class="border-2 border-gray-200 rounded-lg overflow-hidden bg-white" id="clientSigWrapper">
                        <canvas id="clientSignaturePad"></canvas>
                    </div>
                    <button type="button" onclick="clearSignature('client')" class="mt-2 text-xs text-red-500 hover:text-red-700">
                        <i class="fas fa-eraser mr-1"></i>Effacer
                    </button>
                    <input type="hidden" name="client_signature" id="clientSignatureData">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Signature de l'agent</label>
                    <div class="border-2 border-gray-200 rounded-lg overflow-hidden bg-white" id="adminSigWrapper">
                        <canvas id="adminSignaturePad"></canvas>
                    </div>
                    <button type="button" onclick="clearSignature('admin')" class="mt-2 text-xs text-red-500 hover:text-red-700">
                        <i class="fas fa-eraser mr-1"></i>Effacer
                    </button>
                    <input type="hidden" name="admin_signature" id="adminSignatureData">
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3 pb-6">
            <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-center px-6 py-2.5 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600">
                <i class="fas fa-save mr-2"></i>Enregistrer l'état des lieux
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
// Size canvas to its wrapper width
function resizeCanvas(canvas, wrapper) {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const w = wrapper.offsetWidth;
    const h = Math.round(w * 0.4);
    canvas.width = w * ratio;
    canvas.height = h * ratio;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    canvas.getContext('2d').scale(ratio, ratio);
}

const clientCanvas  = document.getElementById('clientSignaturePad');
const adminCanvas   = document.getElementById('adminSignaturePad');
const clientWrapper = document.getElementById('clientSigWrapper');
const adminWrapper  = document.getElementById('adminSigWrapper');

resizeCanvas(clientCanvas, clientWrapper);
resizeCanvas(adminCanvas,  adminWrapper);

const clientPad = new SignaturePad(clientCanvas, { backgroundColor: 'rgb(255,255,255)' });
const adminPad  = new SignaturePad(adminCanvas,  { backgroundColor: 'rgb(255,255,255)' });

window.addEventListener('resize', () => {
    const cData = clientPad.toData();
    const aData = adminPad.toData();
    resizeCanvas(clientCanvas, clientWrapper);
    resizeCanvas(adminCanvas,  adminWrapper);
    clientPad.fromData(cData);
    adminPad.fromData(aData);
});

function clearSignature(type) {
    if (type === 'client') clientPad.clear();
    else adminPad.clear();
}

// Cumulative photo previews (gallery + camera)
const allFiles = new DataTransfer();

function addPhotoPreviews(files) {
    const preview = document.getElementById('photoPreview');
    [...files].forEach(file => {
        allFiles.items.add(file);
        const reader = new FileReader();
        reader.onload = ev => {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `<img src="${ev.target.result}" class="w-full h-24 object-cover rounded-lg border">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

document.getElementById('inspectionForm').addEventListener('submit', function(e) {
    if (clientPad.isEmpty() || adminPad.isEmpty()) {
        e.preventDefault();
        alert('Les deux signatures sont obligatoires.');
        return;
    }
    document.getElementById('clientSignatureData').value = clientPad.toDataURL();
    document.getElementById('adminSignatureData').value  = adminPad.toDataURL();
});

function inspectionForm() { return {}; }
</script>
@endpush
