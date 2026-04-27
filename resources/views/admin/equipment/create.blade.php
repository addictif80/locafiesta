@extends('layouts.admin')

@section('title', 'Ajouter un matériel')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.materiel.index') }}" class="text-gray-400 hover:text-gray-600 transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="text-xl font-semibold text-gray-800">Ajouter un matériel</h2>
</div>

<form method="POST" action="{{ route('admin.materiel.store') }}" enctype="multipart/form-data">
    @csrf

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
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="internal_reference" class="block text-sm font-medium text-gray-700 mb-1">Référence interne</label>
                        <input type="text" id="internal_reference" name="internal_reference" value="{{ old('internal_reference') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('internal_reference') border-red-400 @enderror">
                        @error('internal_reference')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="5"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none resize-y @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Photos -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    Photos
                </h3>
                <div>
                    <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">Ajouter des photos</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition cursor-pointer"
                         onclick="document.getElementById('photos').click()">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500">Cliquez pour sélectionner des photos</p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP — plusieurs fichiers acceptés</p>
                    </div>
                    <input type="file" id="photos" name="photos[]" accept="image/*" multiple class="hidden"
                           onchange="previewPhotos(this)">
                    @error('photos.*')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <div id="photo-preview" class="grid grid-cols-4 gap-3 mt-4"></div>
                </div>
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
                            <input type="number" id="daily_rate" name="daily_rate" value="{{ old('daily_rate') }}"
                                   step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded-lg pl-3 pr-8 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('daily_rate') border-red-400 @enderror">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">€</span>
                        </div>
                        @error('daily_rate')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deposit" class="block text-sm font-medium text-gray-700 mb-1">Caution (€)</label>
                        <div class="relative">
                            <input type="number" id="deposit" name="deposit" value="{{ old('deposit', 0) }}"
                                   step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded-lg pl-3 pr-8 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('deposit') border-red-400 @enderror">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">€</span>
                        </div>
                        @error('deposit')
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
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-300">
                    <span class="text-sm font-medium text-gray-700">Matériel actif (visible à la réservation)</span>
                </label>
                @error('is_active')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2">
                <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2.5 rounded-lg text-sm transition">
                    <i class="fas fa-save mr-2"></i> Enregistrer le matériel
                </button>
                <a href="{{ route('admin.materiel.index') }}"
                   class="w-full text-center border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 rounded-lg text-sm transition">
                    Annuler
                </a>
            </div>
        </div>
    </div>
</form>
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
