<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>
    {{ $inspection->type_label }} — {{ $reservation->reference }}
</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
    .container { padding: 36px; max-width: 800px; margin: 0 auto; }

    .header { display: table; width: 100%; border-bottom: 3px solid #f97316; padding-bottom: 14px; margin-bottom: 20px; }
    .header-left { display: table-cell; vertical-align: middle; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; }
    .company-name { font-size: 22px; font-weight: 900; color: #f97316; }
    .doc-title { font-size: 16px; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 2px; }
    .doc-type-badge-departure { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 4px; display: inline-block; }
    .doc-type-badge-return { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 4px; display: inline-block; }

    .section-title { font-size: 10px; font-weight: 700; color: #fff; background: #1f2937; padding: 5px 10px; text-transform: uppercase; letter-spacing: 1px; margin: 16px 0 10px; }

    .info-grid { display: table; width: 100%; margin-bottom: 12px; }
    .info-col { display: table-cell; vertical-align: top; width: 50%; padding-right: 12px; }
    .info-col:last-child { padding-right: 0; }
    .info-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; }
    .info-row { display: table; width: 100%; margin-bottom: 4px; }
    .info-label { display: table-cell; font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; width: 40%; vertical-align: top; padding-top: 1px; }
    .info-value { display: table-cell; font-size: 10px; color: #1f2937; font-weight: 600; }

    .check-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .check-table th { background: #374151; color: #fff; padding: 7px 9px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    .check-table td { padding: 7px 9px; font-size: 10px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .check-table tr:nth-child(even) td { background: #f9fafb; }
    .status-good { color: #15803d; font-weight: 600; }
    .status-worn { color: #92400e; font-weight: 600; }
    .status-damaged { color: #dc2626; font-weight: 700; }
    .status-missing { color: #7c3aed; font-weight: 700; }

    .compare-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .compare-table th { background: #1e40af; color: #fff; padding: 7px 9px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    .compare-table td { padding: 7px 9px; font-size: 10px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .compare-table tr:nth-child(even) td { background: #f0f9ff; }
    .diff-ok { color: #15803d; }
    .diff-changed { color: #dc2626; font-weight: 700; }

    .notes-box { background: #fffbf5; border: 1px solid #fed7aa; border-radius: 4px; padding: 12px; margin-bottom: 16px; min-height: 60px; }
    .notes-label { font-size: 9px; font-weight: 700; color: #92400e; text-transform: uppercase; margin-bottom: 6px; }
    .notes-text { font-size: 10px; color: #4b5563; line-height: 1.6; }

    .signatures { display: table; width: 100%; margin-top: 20px; }
    .sig-col { display: table-cell; width: 45%; vertical-align: top; }
    .sig-spacer { display: table-cell; width: 10%; }
    .sig-box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 12px; min-height: 100px; }
    .sig-title { font-size: 10px; font-weight: 700; color: #1f2937; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 8px; }
    .sig-name { font-size: 10px; color: #6b7280; margin-bottom: 6px; }
    .sig-image { max-width: 100%; max-height: 60px; }
    .sig-line { border-bottom: 1px solid #9ca3af; margin-top: 50px; }
    .sig-date { font-size: 9px; color: #9ca3af; margin-top: 3px; }

    .footer { border-top: 1px solid #e5e7eb; margin-top: 16px; padding-top: 8px; }
    .footer-text { font-size: 8px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">{{ $company['name'] }}</div>
            <div style="font-size: 9px; color: #6b7280; margin-top: 2px;">Location de matériel festif</div>
        </div>
        <div class="header-right">
            <div class="doc-title">État des lieux</div>
            @if($inspection->type === 'departure')
                <div class="doc-type-badge-departure">Départ</div>
            @else
                <div class="doc-type-badge-return">Retour</div>
            @endif
            <div style="font-size: 9px; color: #9ca3af; margin-top: 4px;">
                Généré le {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>

    {{-- Reservation info --}}
    <div class="section-title">Informations de la réservation</div>
    <div class="info-grid">
        <div class="info-col">
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Réservation</span>
                    <span class="info-value" style="font-family: monospace;">{{ $reservation->reference }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Client</span>
                    <span class="info-value">{{ $reservation->client->full_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Téléphone</span>
                    <span class="info-value">{{ $reservation->client->phone }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Matériels</span>
                    <span class="info-value">
                        @foreach($reservation->items as $item)
                            {{ $item->equipment->name }}@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
            </div>
        </div>
        <div class="info-col">
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Départ</span>
                    <span class="info-value">{{ $reservation->start_date->format('d/m/Y') }} à {{ $reservation->start_time }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Retour</span>
                    <span class="info-value">{{ $reservation->end_date->format('d/m/Y') }} à {{ $reservation->end_time }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date EDL</span>
                    <span class="info-value">{{ $inspection->signed_at?->format('d/m/Y à H:i') ?? $inspection->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Agent</span>
                    <span class="info-value">{{ $inspection->admin?->full_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Checklist (departure) --}}
    @if($inspection->type === 'departure')
    <div class="section-title">Checklist — État du matériel</div>
    <table class="check-table">
        <thead>
            <tr>
                <th style="width: 35%;">Élément</th>
                <th style="width: 20%;">État</th>
                <th style="width: 45%;">Observations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inspection->items as $item)
            <tr>
                <td style="font-weight: 600;">{{ $item->checklistItem->label }}</td>
                <td>
                    @php
                        $condClass = match($item->condition) {
                            'good'    => 'status-good',
                            'worn'    => 'status-worn',
                            'damaged' => 'status-damaged',
                            'missing' => 'status-missing',
                            default   => '',
                        };
                    @endphp
                    <span class="{{ $condClass }}">{{ $item->condition_label }}</span>
                </td>
                <td style="color: #6b7280;">{{ $item->notes ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #9ca3af; font-style: italic;">Aucun élément enregistré</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    {{-- Comparison (return only) --}}
    @if($inspection->type === 'return')
    @php
        $departureInspection = $reservation->departureInspection;
        $departureMap = $departureInspection
            ? $departureInspection->load('items')->items->keyBy('checklist_item_id')
            : collect();
    @endphp

    <div class="section-title">Comparaison Départ / Retour</div>
    <table class="compare-table">
        <thead>
            <tr>
                <th style="width: 28%;">Élément</th>
                <th style="width: 22%;">État au départ</th>
                <th style="width: 22%;">État au retour</th>
                <th style="width: 28%;">Observations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inspection->items as $item)
            @php
                $depItem    = $departureMap->get($item->checklist_item_id);
                $dLabel     = $depItem?->condition_label ?? '—';
                $isDifferent = $depItem && $depItem->condition !== $item->condition;
            @endphp
            <tr>
                <td style="font-weight: 600;">{{ $item->checklistItem->label }}</td>
                <td>{{ $dLabel }}</td>
                <td class="{{ $isDifferent ? 'diff-changed' : '' }}">{{ $item->condition_label }}</td>
                <td class="{{ $isDifferent ? 'diff-changed' : 'diff-ok' }}">
                    @if($isDifferent)
                        <strong>Différence constatée</strong>
                        @if($item->notes)<br><span style="font-size: 9px;">{{ $item->notes }}</span>@endif
                    @else
                        Aucune différence
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #9ca3af; font-style: italic;">Aucun élément enregistré</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    {{-- General notes --}}
    <div class="section-title">Notes générales</div>
    <div class="notes-box">
        @if($inspection->general_notes)
            <div class="notes-text">{{ $inspection->general_notes }}</div>
        @else
            <div class="notes-text" style="color: #d1d5db; font-style: italic;">Aucune note particulière.</div>
        @endif
    </div>

    {{-- Signatures --}}
    <div class="section-title">Signatures</div>
    <div class="signatures">
        <div class="sig-col">
            <div class="sig-box">
                <div class="sig-title">Agent LocaFiesta</div>
                <div class="sig-name">{{ $inspection->admin?->full_name ?? '—' }}</div>
                @if($inspection->admin_signature_path)
                    <img src="{{ storage_path('app/public/' . $inspection->admin_signature_path) }}" class="sig-image" alt="Signature agent">
                @else
                    <div class="sig-line"></div>
                    <div class="sig-date">Signature</div>
                @endif
            </div>
        </div>
        <div class="sig-spacer"></div>
        <div class="sig-col">
            <div class="sig-box">
                <div class="sig-title">Client</div>
                <div class="sig-name">
                    {{ $reservation->client->full_name }}<br>
                    <span style="font-size: 9px; color: #9ca3af;">Lu et approuvé</span>
                </div>
                @if($inspection->client_signature_path)
                    <img src="{{ storage_path('app/public/' . $inspection->client_signature_path) }}" class="sig-image" alt="Signature client">
                @else
                    <div class="sig-line"></div>
                    <div class="sig-date">Signature</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Free message --}}
    @if(!empty($pdfMessage))
    <div style="border: 1px solid #e5e7eb; border-radius: 4px; padding: 12px 14px; margin: 16px 0; background: #f9fafb; font-size: 10px; color: #4b5563; line-height: 1.6;">
        {!! $pdfMessage !!}
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-text">
            {{ $company['name'] }}{{ !empty($company['address']) ? ' — ' . $company['address'] : '' }}{{ !empty($company['siret']) ? ' — SIRET ' . $company['siret'] : '' }}<br>
            Document généré le {{ now()->format('d/m/Y à H:i') }} — Réservation {{ $reservation->reference }}
        </div>
    </div>

</div>
</body>
</html>
