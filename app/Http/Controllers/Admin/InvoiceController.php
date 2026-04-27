<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Reservation;
use App\Services\PdfService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private PdfService $pdfService) {}

    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'reservation']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->search) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                  ->orWhereHas('client', fn($q) => $q->where('last_name', 'like', "%{$request->search}%"));
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();
        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'reservation.items.equipment']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function create()
    {
        $clients = User::where('role', 'client')->orderBy('last_name')->get();
        $reservations = Reservation::with('client')->latest()->limit(100)->get();
        return view('admin.invoices.create', compact('clients', 'reservations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:users,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'type' => 'required|in:deposit,balance,damage,manual',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::create($data);

        if ($invoice->status === 'paid') {
            $invoice->update(['paid_at' => now()]);
        }

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Facture créée.');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,paid,cancelled,refunded',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($data['status'] === 'paid' && $invoice->status !== 'paid') {
            $data['paid_at'] = now();
        }

        $invoice->update($data);
        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Facture mise à jour.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('success', 'Facture supprimée.');
    }

    public function download(Invoice $invoice)
    {
        $pdf = $this->pdfService->generateInvoice($invoice);
        return $pdf->download('facture-' . $invoice->invoice_number . '.pdf');
    }
}
