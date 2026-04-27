@extends('layouts.admin')
@section('title', 'Facture ' . $invoice->invoice_number)
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.factures.index') }}" class="text-sm text-orange-500 hover:underline"><i class="fas fa-arrow-left mr-1"></i>Factures</a>
            <h2 class="text-xl font-bold text-gray-800 mt-2">{{ $invoice->invoice_number }}</h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.invoices.download', $invoice) }}" class="flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600">
                <i class="fas fa-file-pdf"></i>Télécharger PDF
            </a>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Client:</span> <span class="font-medium">{{ $invoice->client->full_name }}</span></div>
            <div><span class="text-gray-500">Réservation:</span> <span class="font-medium">{{ $invoice->reservation?->reference ?? '—' }}</span></div>
            <div><span class="text-gray-500">Type:</span> <span class="font-medium">{{ $invoice->type_label }}</span></div>
            <div><span class="text-gray-500">Montant:</span> <span class="font-bold text-lg">{{ number_format($invoice->amount, 2, ',', ' ') }} €</span></div>
            <div><span class="text-gray-500">Statut:</span>
                <span class="ml-1 px-2 py-0.5 rounded text-xs {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $invoice->status_label }}</span>
            </div>
            <div><span class="text-gray-500">Date:</span> <span class="font-medium">{{ $invoice->created_at->format('d/m/Y') }}</span></div>
            @if($invoice->paid_at)<div><span class="text-gray-500">Payé le:</span> <span class="font-medium">{{ $invoice->paid_at->format('d/m/Y H:i') }}</span></div>@endif
            @if($invoice->payment_method)<div><span class="text-gray-500">Moyen:</span> <span class="font-medium">{{ $invoice->payment_method }}</span></div>@endif
        </div>
        @if($invoice->notes)<div class="border-t pt-4"><p class="text-sm text-gray-600">{{ $invoice->notes }}</p></div>@endif
    </div>
    <div class="mt-4 bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Modifier le statut</h3>
        <form method="POST" action="{{ route('admin.factures.update', $invoice) }}" class="space-y-4" id="invoiceUpdateForm">
            @csrf @method('PUT')
            <input type="hidden" name="amount" value="{{ $invoice->amount }}">
            <input type="hidden" name="pdf_message" id="invoice_pdf_message_input">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @foreach(['pending' => 'En attente', 'paid' => 'Payée', 'cancelled' => 'Annulée', 'refunded' => 'Remboursée'] as $val => $label)
                        <option value="{{ $val }}" {{ $invoice->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Moyen de paiement</label>
                    <input type="text" name="payment_method" value="{{ $invoice->payment_method }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700"><i class="fas fa-comment-alt mr-1 text-orange-400"></i>Message libre sur la facture PDF</label>
                    <button type="button" id="resetInvoiceMsg" class="text-xs text-orange-500 hover:underline">Message par défaut</button>
                </div>
                <div id="invoice_pdf_message_editor" class="bg-white border rounded-lg" style="min-height: 120px;">{!! $invoice->pdf_message !!}</div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-orange-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-orange-600">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
const invoiceQuill = new Quill('#invoice_pdf_message_editor', {
    theme: 'snow',
    modules: { toolbar: [['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],['clean']] }
});

document.getElementById('invoiceUpdateForm').addEventListener('submit', function() {
    const html = invoiceQuill.root.innerHTML;
    document.getElementById('invoice_pdf_message_input').value = html === '<p><br></p>' ? '' : html;
});

document.getElementById('resetInvoiceMsg').addEventListener('click', function() {
    const defaultMsg = @json(\App\Models\Setting::get('invoice_message_default', ''));
    invoiceQuill.root.innerHTML = defaultMsg || '';
});
</script>
@endpush
