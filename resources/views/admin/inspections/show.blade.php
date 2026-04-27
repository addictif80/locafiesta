@extends('layouts.admin')
@section('title', $inspection->type_label)
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.reservations.show', $inspection->reservation) }}" class="text-sm text-orange-500 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i>Réservation {{ $inspection->reservation->reference }}
            </a>
            <h2 class="text-xl font-bold text-gray-800 mt-1">{{ $inspection->type_label }}</h2>
        </div>
        <a href="{{ route('admin.inspections.pdf', $inspection) }}"
           class="flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-600">
            <i class="fas fa-file-pdf"></i>Télécharger PDF
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500">Date:</span> <span class="font-medium">{{ $inspection->signed_at?->format('d/m/Y H:i') ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">Agent:</span> <span class="font-medium">{{ $inspection->admin?->full_name ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">Client:</span> <span class="font-medium">{{ $inspection->reservation->client->full_name }}</span></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border mb-6">
        <div class="px-6 py-4 border-b"><h3 class="font-semibold text-gray-800">Éléments vérifiés</h3></div>
        <div class="divide-y">
            @foreach($inspection->items as $item)
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <span class="font-medium text-sm text-gray-800">{{ $item->checklistItem->label }}</span>
                    @if($item->notes)<p class="text-xs text-gray-500 mt-0.5">{{ $item->notes }}</p>@endif
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium
                    {{ $item->condition === 'good' ? 'bg-green-100 text-green-700' :
                       ($item->condition === 'worn' ? 'bg-yellow-100 text-yellow-700' :
                       ($item->condition === 'damaged' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                    {{ $item->condition_label }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    @if($inspection->general_notes)
    <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Notes générales</h3>
        <p class="text-sm text-gray-700">{{ $inspection->general_notes }}</p>
    </div>
    @endif

    @if($inspection->photos->count())
    <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Photos</h3>
        <div class="grid grid-cols-4 gap-3">
            @foreach($inspection->photos as $photo)
            <img src="{{ asset('storage/' . $photo->path) }}" class="w-full h-32 object-cover rounded-lg border cursor-pointer"
                 onclick="window.open(this.src)">
            @endforeach
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.inspections.update-message', $inspection) }}" class="bg-white rounded-xl shadow-sm border p-6 mb-6" id="inspectionMsgForm">
        @csrf @method('PATCH')
        <input type="hidden" name="pdf_message" id="inspection_pdf_message_input">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-800"><i class="fas fa-comment-alt mr-2 text-orange-400"></i>Message libre sur le PDF</h3>
            <button type="button" id="resetInspectionMsg" class="text-xs text-orange-500 hover:underline">Message par défaut</button>
        </div>
        <div id="inspection_pdf_message_editor" class="bg-white border rounded-lg mb-4" style="min-height: 120px;">{!! $inspection->pdf_message !!}</div>
        <div class="flex justify-end">
            <button type="submit" class="bg-orange-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-orange-600">
                <i class="fas fa-save mr-1"></i>Sauvegarder le message
            </button>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Signatures</h3>
        <div class="grid grid-cols-2 gap-8">
            <div>
                <p class="text-sm text-gray-500 mb-2">Signature client</p>
                @if($inspection->client_signature_path)
                <img src="{{ asset('storage/' . $inspection->client_signature_path) }}" class="border rounded-lg p-2 max-h-24">
                @else
                <span class="text-sm text-gray-400">Non disponible</span>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-2">Signature agent</p>
                @if($inspection->admin_signature_path)
                <img src="{{ asset('storage/' . $inspection->admin_signature_path) }}" class="border rounded-lg p-2 max-h-24">
                @else
                <span class="text-sm text-gray-400">Non disponible</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
const inspectionQuill = new Quill('#inspection_pdf_message_editor', {
    theme: 'snow',
    modules: { toolbar: [['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],['clean']] }
});

document.getElementById('inspectionMsgForm').addEventListener('submit', function() {
    const html = inspectionQuill.root.innerHTML;
    document.getElementById('inspection_pdf_message_input').value = html === '<p><br></p>' ? '' : html;
});

document.getElementById('resetInspectionMsg').addEventListener('click', function() {
    const defaultMsg = @json(\App\Models\Setting::get('inspection_message_default', ''));
    inspectionQuill.root.innerHTML = defaultMsg || '';
});
</script>
@endpush
