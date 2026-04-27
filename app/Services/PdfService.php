<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Inspection;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateInvoice(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load(['client', 'reservation.items.equipment']);

        $company = [
            'name' => Setting::get('company_name', 'LocaFiesta'),
            'address' => Setting::get('company_address', ''),
            'phone' => Setting::get('company_phone', ''),
            'email' => Setting::get('company_email', ''),
            'siret' => Setting::get('company_siret', ''),
        ];

        return Pdf::loadView('pdf.invoice', compact('invoice', 'company'))
            ->setPaper('a4', 'portrait');
    }

    public function generateContract(Reservation $reservation): \Barryvdh\DomPDF\PDF
    {
        $reservation->load(['client', 'items.equipment']);

        $company = [
            'name' => Setting::get('company_name', 'LocaFiesta'),
            'address' => Setting::get('company_address', ''),
            'phone' => Setting::get('company_phone', ''),
            'email' => Setting::get('company_email', ''),
            'siret' => Setting::get('company_siret', ''),
        ];

        $cgv = Setting::get('cgv_text', '');

        return Pdf::loadView('pdf.contract', compact('reservation', 'company', 'cgv'))
            ->setPaper('a4', 'portrait');
    }

    public function generateInspectionReport(Inspection $inspection): \Barryvdh\DomPDF\PDF
    {
        $inspection->load(['reservation.client', 'items.checklistItem', 'photos', 'admin']);

        $company = [
            'name' => Setting::get('company_name', 'LocaFiesta'),
            'address' => Setting::get('company_address', ''),
        ];

        return Pdf::loadView('pdf.inspection', compact('inspection', 'company'))
            ->setPaper('a4', 'portrait');
    }
}
