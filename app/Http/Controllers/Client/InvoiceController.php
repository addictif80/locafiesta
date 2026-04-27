<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PdfService;

class InvoiceController extends Controller
{
    public function __construct(private PdfService $pdfService) {}

    public function index()
    {
        $invoices = auth()->user()->invoices()->with('reservation')->latest()->paginate(10);
        return view('client.invoices.index', compact('invoices'));
    }

    public function download(Invoice $invoice)
    {
        abort_unless($invoice->client_id === auth()->id(), 403);
        $pdf = $this->pdfService->generateInvoice($invoice);
        return $pdf->download('facture-' . $invoice->invoice_number . '.pdf');
    }
}
