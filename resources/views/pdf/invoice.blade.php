<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture {{ $invoice->invoice_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2937; background: #fff; }
    .container { padding: 40px; max-width: 800px; margin: 0 auto; }

    /* Header */
    .header { display: table; width: 100%; margin-bottom: 40px; }
    .header-left { display: table-cell; vertical-align: top; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; }
    .company-name { font-size: 26px; font-weight: 700; color: #f97316; letter-spacing: -0.5px; }
    .company-tagline { font-size: 10px; color: #6b7280; margin-top: 2px; }
    .company-details { font-size: 10px; color: #6b7280; margin-top: 8px; line-height: 1.6; }

    /* Invoice title box */
    .invoice-title-box { background: #fff7ed; border-left: 4px solid #f97316; padding: 16px 20px; margin-bottom: 32px; }
    .invoice-title { font-size: 22px; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 2px; }
    .invoice-meta { display: table; width: 100%; margin-top: 8px; }
    .invoice-meta-item { display: table-cell; font-size: 11px; color: #6b7280; }
    .invoice-meta-value { font-weight: 700; color: #1f2937; }

    /* Parties */
    .parties { display: table; width: 100%; margin-bottom: 28px; }
    .party { display: table-cell; width: 48%; vertical-align: top; }
    .party-spacer { display: table-cell; width: 4%; }
    .party-label { font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
    .party-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 14px; }
    .party-name { font-weight: 700; font-size: 13px; color: #1f2937; margin-bottom: 4px; }
    .party-detail { font-size: 11px; color: #4b5563; line-height: 1.5; }

    /* Reservation info */
    .reservation-info { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; font-size: 11px; color: #1e40af; }
    .reservation-info strong { color: #1e3a8a; }

    /* Table */
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .items-table th { background: #1f2937; color: #fff; padding: 10px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .items-table th:last-child, .items-table td:last-child { text-align: right; }
    .items-table td { padding: 10px 12px; font-size: 11px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
    .items-table tr:nth-child(even) td { background: #f9fafb; }
    .items-table .item-name { font-weight: 600; color: #1f2937; }
    .items-table .item-desc { font-size: 10px; color: #9ca3af; margin-top: 2px; }

    /* Totals */
    .totals { margin-left: auto; width: 260px; margin-bottom: 28px; }
    .totals-row { display: table; width: 100%; padding: 5px 0; }
    .totals-label { display: table-cell; font-size: 11px; color: #6b7280; }
    .totals-value { display: table-cell; text-align: right; font-size: 11px; color: #1f2937; font-weight: 600; }
    .totals-divider { border-top: 1px solid #e5e7eb; margin: 6px 0; }
    .totals-total { background: #1f2937; border-radius: 4px; padding: 8px 12px; }
    .totals-total .totals-label, .totals-total .totals-value { color: #fff; font-size: 13px; font-weight: 700; }

    /* TVA mention */
    .tva-mention { font-size: 10px; color: #9ca3af; margin-bottom: 20px; font-style: italic; }

    /* Payment info */
    .payment-info { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px 14px; margin-bottom: 28px; }
    .payment-info-title { font-size: 10px; font-weight: 700; color: #16a34a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .payment-info p { font-size: 10px; color: #15803d; line-height: 1.6; }

    /* Footer */
    .footer { border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 20px; }
    .footer-text { font-size: 9px; color: #9ca3af; line-height: 1.6; text-align: center; }
</style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">LocaFiesta</div>
            <div class="company-tagline">Location de matériel festif</div>
            <div class="company-details">
                {{ $company['address'] ?? '' }}<br>
                Tél : {{ $company['phone'] ?? '' }}<br>
                Email : {{ $company['email'] ?? '' }}<br>
                SIRET : {{ $company['siret'] ?? '' }}
            </div>
        </div>
        <div class="header-right">
            <div style="font-size: 48px; color: #f97316; opacity: 0.15; font-weight: 900;">🎉</div>
        </div>
    </div>

    {{-- Invoice title --}}
    <div class="invoice-title-box">
        <div class="invoice-title">Facture</div>
        <div class="invoice-meta">
            <div class="invoice-meta-item">
                N° <span class="invoice-meta-value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="invoice-meta-item" style="text-align: center;">
                Date : <span class="invoice-meta-value">{{ $invoice->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="invoice-meta-item" style="text-align: right;">
                Type : <span class="invoice-meta-value">
                    @php
                        $typeLabels = ['deposit' => 'Acompte', 'balance' => 'Solde', 'full' => 'Facture complète', 'credit' => 'Avoir'];
                    @endphp
                    {{ $typeLabels[$invoice->type] ?? ucfirst($invoice->type) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Parties --}}
    <div class="parties">
        <div class="party">
            <div class="party-label">Émetteur</div>
            <div class="party-box">
                <div class="party-name">{{ $company['name'] ?? 'LocaFiesta' }}</div>
                <div class="party-detail">
                    {{ $company['address'] ?? '' }}<br>
                    {{ $company['phone'] ?? '' }}<br>
                    {{ $company['email'] ?? '' }}
                </div>
            </div>
        </div>
        <div class="party-spacer"></div>
        <div class="party">
            <div class="party-label">Client</div>
            <div class="party-box">
                <div class="party-name">{{ $invoice->user->full_name ?? ($invoice->reservation->user->full_name ?? '') }}</div>
                <div class="party-detail">
                    @php $user = $invoice->user ?? $invoice->reservation?->user; @endphp
                    @if($user)
                        {{ $user->address }}<br>
                        {{ $user->postal_code }} {{ $user->city }}<br>
                        {{ $user->email }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Reservation info --}}
    @if($invoice->reservation)
    <div class="reservation-info">
        <strong>Réservation :</strong> {{ $invoice->reservation->reference }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Période :</strong>
        du {{ $invoice->reservation->start_date->format('d/m/Y') }}
        au {{ $invoice->reservation->end_date->format('d/m/Y') }}
    </div>
    @endif

    {{-- Line items --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Description</th>
                <th style="width: 15%; text-align: right;">Tarif/j</th>
                <th style="width: 10%; text-align: right;">Jours</th>
                <th style="width: 15%; text-align: right;">Sous-total</th>
                <th style="width: 15%; text-align: right;">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @if($invoice->reservation && $invoice->reservation->items->isNotEmpty())
                @foreach($invoice->reservation->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->equipment->name }}</div>
                        <div class="item-desc">Réf. {{ $item->equipment->reference }}</div>
                    </td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                    <td style="text-align: right;">{{ $item->days }}</td>
                    <td style="text-align: right;">{{ number_format($item->subtotal, 2, ',', ' ') }} €</td>
                    <td style="text-align: right;">{{ number_format($item->subtotal, 2, ',', ' ') }} €</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td>
                        <div class="item-name">{{ $invoice->description ?? 'Prestation de location' }}</div>
                    </td>
                    <td style="text-align: right;">—</td>
                    <td style="text-align: right;">—</td>
                    <td style="text-align: right;">{{ number_format($invoice->amount, 2, ',', ' ') }} €</td>
                    <td style="text-align: right;">{{ number_format($invoice->amount, 2, ',', ' ') }} €</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals">
        @if($invoice->reservation)
        <div class="totals-row">
            <span class="totals-label">Sous-total</span>
            <span class="totals-value">{{ number_format($invoice->reservation->subtotal_amount ?? $invoice->amount, 2, ',', ' ') }} €</span>
        </div>
        @if(($invoice->reservation->discount_amount ?? 0) > 0)
        <div class="totals-row">
            <span class="totals-label">Remise ({{ $invoice->reservation->promo_code }})</span>
            <span class="totals-value" style="color: #16a34a;">- {{ number_format($invoice->reservation->discount_amount, 2, ',', ' ') }} €</span>
        </div>
        @endif
        <div class="totals-divider"></div>
        @endif
        <div class="totals-row">
            <span class="totals-label">Total HT</span>
            <span class="totals-value">{{ number_format($invoice->amount, 2, ',', ' ') }} €</span>
        </div>
        <div class="tva-mention">TVA non applicable — Art. 293B du CGI</div>
        <div class="totals-total">
            <div class="totals-row">
                <span class="totals-label">Total TTC</span>
                <span class="totals-value">{{ number_format($invoice->amount, 2, ',', ' ') }} €</span>
            </div>
        </div>
    </div>

    {{-- Payment info --}}
    <div class="payment-info">
        <div class="payment-info-title">Informations de paiement</div>
        <p>
            @if($invoice->status === 'paid')
                ✓ Cette facture a été réglée le {{ $invoice->paid_at?->format('d/m/Y') ?? $invoice->updated_at->format('d/m/Y') }}.
            @else
                Paiement à effectuer par virement ou en ligne sur votre espace client.<br>
                IBAN : {{ $company['iban'] ?? 'FR76 XXXX XXXX XXXX XXXX XXXX XXX' }}<br>
                Référence : {{ $invoice->invoice_number }}
            @endif
        </p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-text">
            {{ $company['name'] ?? 'LocaFiesta' }} — SIRET {{ $company['siret'] ?? '' }}<br>
            {{ $company['address'] ?? '' }}<br>
            Toute contestation doit être adressée dans un délai de 30 jours suivant la date de réception de la présente facture.<br>
            Conformément aux conditions générales de vente disponibles sur notre site.
        </div>
    </div>

</div>
</body>
</html>
