<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Contrat de location {{ $reservation->reference }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
    .container { padding: 36px; max-width: 800px; margin: 0 auto; }

    /* Header */
    .header { display: table; width: 100%; border-bottom: 3px solid #f97316; padding-bottom: 16px; margin-bottom: 24px; }
    .header-left { display: table-cell; vertical-align: middle; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; }
    .company-name { font-size: 24px; font-weight: 900; color: #f97316; }
    .company-sub { font-size: 9px; color: #6b7280; margin-top: 2px; }
    .doc-title { font-size: 18px; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 2px; }
    .doc-ref { font-size: 11px; color: #6b7280; margin-top: 4px; }

    /* Section titles */
    .section-title { font-size: 11px; font-weight: 700; color: #fff; background: #1f2937; padding: 6px 12px; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 12px; }

    /* Parties */
    .parties { display: table; width: 100%; margin-bottom: 8px; }
    .party-col { display: table-cell; width: 48%; vertical-align: top; }
    .party-spacer { display: table-cell; width: 4%; }
    .party-box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 12px; background: #f9fafb; }
    .party-role { font-size: 9px; font-weight: 700; color: #f97316; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
    .party-name { font-weight: 700; font-size: 13px; color: #1f2937; margin-bottom: 4px; }
    .party-detail { font-size: 10px; color: #4b5563; line-height: 1.6; }

    /* Table */
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .items-table th { background: #f97316; color: #fff; padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    .items-table th:last-child, .items-table td:last-child { text-align: right; }
    .items-table td { padding: 8px 10px; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
    .items-table tr:nth-child(even) td { background: #fffbf5; }
    .items-table tfoot td { font-weight: 700; background: #fff7ed; border-top: 2px solid #f97316; font-size: 11px; }

    /* Conditions */
    .conditions-grid { display: table; width: 100%; margin-bottom: 8px; }
    .condition-item { display: table-cell; width: 33%; vertical-align: top; padding-right: 12px; }
    .condition-box { background: #fffbf5; border: 1px solid #fed7aa; border-radius: 4px; padding: 10px; }
    .condition-label { font-size: 9px; font-weight: 700; color: #92400e; text-transform: uppercase; margin-bottom: 4px; }
    .condition-value { font-size: 12px; font-weight: 700; color: #1f2937; }
    .condition-note { font-size: 9px; color: #6b7280; margin-top: 2px; }

    /* CGV */
    .cgv-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 14px; font-size: 9.5px; color: #4b5563; line-height: 1.7; margin-bottom: 8px; }

    /* Signatures */
    .signatures { display: table; width: 100%; margin-top: 28px; }
    .signature-col { display: table-cell; width: 45%; vertical-align: top; }
    .signature-spacer { display: table-cell; width: 10%; }
    .signature-box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 16px; min-height: 110px; }
    .signature-title { font-size: 10px; font-weight: 700; color: #1f2937; text-transform: uppercase; margin-bottom: 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; }
    .signature-name { font-size: 10px; color: #6b7280; margin-bottom: 8px; }
    .signature-line { border-bottom: 1px solid #9ca3af; margin-top: 50px; }
    .signature-date { font-size: 9px; color: #9ca3af; margin-top: 4px; }

    /* Footer */
    .footer { border-top: 1px solid #e5e7eb; margin-top: 20px; padding-top: 10px; }
    .footer-text { font-size: 8.5px; color: #9ca3af; text-align: center; line-height: 1.6; }
</style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">LocaFiesta</div>
            <div class="company-sub">Location de matériel festif</div>
        </div>
        <div class="header-right">
            <div class="doc-title">Contrat de location</div>
            <div class="doc-ref">
                Réf. {{ $reservation->reference }}
                &nbsp;|&nbsp;
                Date : {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- Parties --}}
    <div class="section-title">Les parties</div>
    <div class="parties">
        <div class="party-col">
            <div class="party-box">
                <div class="party-role">Le loueur</div>
                <div class="party-name">{{ $company['name'] ?? 'LocaFiesta' }}</div>
                <div class="party-detail">
                    {{ $company['address'] ?? '' }}<br>
                    Tél : {{ $company['phone'] ?? '' }}<br>
                    Email : {{ $company['email'] ?? '' }}<br>
                    SIRET : {{ $company['siret'] ?? '' }}
                </div>
            </div>
        </div>
        <div class="party-spacer"></div>
        <div class="party-col">
            <div class="party-box">
                <div class="party-role">Le locataire</div>
                <div class="party-name">{{ $reservation->client->full_name }}</div>
                <div class="party-detail">
                    {{ $reservation->client->address }}<br>
                    {{ $reservation->client->postal_code }} {{ $reservation->client->city }}<br>
                    Tél : {{ $reservation->client->phone }}<br>
                    Email : {{ $reservation->client->email }}
                </div>
            </div>
        </div>
    </div>

    {{-- Equipment --}}
    <div class="section-title">Matériels loués</div>
    @php
        $days = $reservation->start_date->diffInDays($reservation->end_date) + 1;
    @endphp
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Référence</th>
                <th style="width: 35%;">Désignation</th>
                <th style="width: 15%; text-align: right;">Tarif/j</th>
                <th style="width: 20%; text-align: center;">Période</th>
                <th style="width: 15%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservation->items as $item)
            <tr>
                <td style="font-family: monospace; font-size: 9px;">{{ $item->equipment->reference }}</td>
                <td style="font-weight: 600;">{{ $item->equipment->name }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                <td style="text-align: center; font-size: 9px;">
                    {{ $reservation->start_date->format('d/m') }} — {{ $reservation->end_date->format('d/m/Y') }}<br>
                    <span style="color: #9ca3af;">({{ $item->days ?? $days }} jour(s))</span>
                </td>
                <td style="text-align: right; font-weight: 600;">{{ number_format($item->subtotal, 2, ',', ' ') }} €</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;">Sous-total</td>
                <td>{{ number_format($reservation->subtotal_amount, 2, ',', ' ') }} €</td>
            </tr>
            @if(($reservation->discount_amount ?? 0) > 0)
            <tr>
                <td colspan="4" style="text-align: right; color: #16a34a;">Remise</td>
                <td style="color: #16a34a;">- {{ number_format($reservation->discount_amount, 2, ',', ' ') }} €</td>
            </tr>
            @endif
            <tr>
                <td colspan="4" style="text-align: right;">Total TTC</td>
                <td>{{ number_format($reservation->total_amount, 2, ',', ' ') }} €</td>
            </tr>
        </tfoot>
    </table>

    {{-- Financial conditions --}}
    <div class="section-title">Conditions financières</div>
    <div class="conditions-grid">
        <div class="condition-item">
            <div class="condition-box">
                <div class="condition-label">Acompte</div>
                <div class="condition-value">{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</div>
                <div class="condition-note">
                    @if($reservation->deposit_paid_at)
                        Payé le {{ $reservation->deposit_paid_at->format('d/m/Y') }}
                    @else
                        À régler à la confirmation
                    @endif
                </div>
            </div>
        </div>
        <div class="condition-item">
            <div class="condition-box">
                <div class="condition-label">Solde</div>
                <div class="condition-value">{{ number_format($reservation->remaining_amount, 2, ',', ' ') }} €</div>
                <div class="condition-note">À régler lors de la remise</div>
            </div>
        </div>
        <div class="condition-item" style="padding-right: 0;">
            <div class="condition-box">
                <div class="condition-label">Caution</div>
                <div class="condition-value">{{ number_format($reservation->caution_amount ?? 0, 2, ',', ' ') }} €</div>
                <div class="condition-note">Restituée après retour en bon état</div>
            </div>
        </div>
    </div>

    {{-- CGV --}}
    <div class="section-title">Conditions générales de location</div>
    <div class="cgv-box">
        {!! nl2br(e($cgv ?? 'Les présentes conditions générales de vente régissent les relations contractuelles entre LocaFiesta (le loueur) et le client (le locataire). En réservant du matériel, le locataire accepte sans réserve les présentes conditions.

ARTICLE 1 - OBJET : Le présent contrat a pour objet la location du matériel désigné ci-dessus.
ARTICLE 2 - DURÉE : La location débute à la date et l\'heure de départ mentionnées et se termine à la date et l\'heure de retour convenues.
ARTICLE 3 - RESPONSABILITÉ : Le locataire est responsable du matériel pendant toute la durée de la location.
ARTICLE 4 - ÉTAT DES LIEUX : Un état des lieux contradictoire est effectué au départ et au retour du matériel.
ARTICLE 5 - ANNULATION : Toute annulation moins de 48h avant le début de la location entraîne la perte de l\'acompte.
ARTICLE 6 - RETOUR DU MATÉRIEL : Le matériel doit être restitué dans l\'état dans lequel il a été remis, propre et complet.')) !!}
    </div>

    {{-- Free message --}}
    @if(!empty($pdfMessage))
    <div style="border: 1px solid #fed7aa; border-radius: 4px; padding: 12px 14px; margin: 16px 0; background: #fffbf5; font-size: 10px; color: #4b5563; line-height: 1.6;">
        {!! $pdfMessage !!}
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature-col">
            <div class="signature-box">
                <div class="signature-title">Le loueur</div>
                <div class="signature-name">{{ $company['name'] ?? 'LocaFiesta' }}</div>
                <div class="signature-line"></div>
                <div class="signature-date">Signature et cachet</div>
            </div>
        </div>
        <div class="signature-spacer"></div>
        <div class="signature-col">
            <div class="signature-box">
                <div class="signature-title">Le locataire</div>
                <div class="signature-name">{{ $reservation->client->full_name }}<br>
                    <span style="font-size: 8px; color: #9ca3af;">Lu et approuvé — "Bon pour accord"</span>
                </div>
                <div class="signature-line"></div>
                <div class="signature-date">Date et signature</div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-text">
            {{ $company['name'] ?? 'LocaFiesta' }} — SIRET {{ $company['siret'] ?? '' }} — {{ $company['address'] ?? '' }}<br>
            Contrat généré le {{ now()->format('d/m/Y à H:i') }} — Document contractuel, à conserver.
        </div>
    </div>

</div>
</body>
</html>
