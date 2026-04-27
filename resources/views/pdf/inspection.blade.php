<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>
    {{ $inspection->type === 'departure' ? 'État des lieux de départ' : 'État des lieux de retour' }}
    — {{ $reservation->reference }}
</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
    .container { padding: 36px; max-width: 800px; margin: 0 auto; }

    /* Header */
    .header { display: table; width: 100%; border-bottom: 3px solid #f97316; padding-bottom: 14px; margin-bottom: 20px; }
    .header-left { display: table-cell; vertical-align: middle; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; }
    .company-name { font-size: 22px; font-weight: 900; color: #f97316; }
    .doc-title { font-size: 16px; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 2px; }
    .doc-type-badge-departure { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 4px; display: inline-block; }
    .doc-type-badge-return { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 4px; display: inline-block; }

    /* Section */
    .section-title { font-size: 10px; font-weight: 700; color: #fff; background: #1f2937; padding: 5px 10px; text-transform: uppercase; letter-spacing: 1px; margin: 16px 0 10px; }

    /* Info grid */
    .info-grid { display: table; width: 100%; margin-bottom: 12px; }
    .info-col { display: table-cell; vertical-align: top; width: 50%; padding-right: 12px; }
    .info-col:last-child { padding-right: 0; }
    .info-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; }
    .info-row { display: table; width: 100%; margin-bottom: 4px; }
    .info-label { display: table-cell; font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; width: 40%; vertical-align: top; padding-top: 1px; }
    .info-value { display: table-cell; font-size: 10px; color: #1f2937; font-weight: 600; }

    /* Checklist table */
    .check-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .check-table th { background: #374151; color: #fff; padding: 7px 9px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    .check-table td { padding: 7px 9px; font-size: 10px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .check-table tr:nth-child(even) td { background: #f9fafb; }
    .check-table .status-good { color: #15803d; font-weight: 600; }
    .check-table .status-normal { color: #92400e; font-weight: 600; }
    .check-table .status-damaged { color: #dc2626; font-weight: 700; }
    .check-table .status-missing { color: #7c3aed; font-weight: 700; }

    /* Comparison table for return */
    .compare-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .compare-table th { background: #1e40af; color: #fff; padding: 7px 9px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    .compare-table td { padding: 7px 9px; font-size: 10px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .compare-table tr:nth-child(even) td { background: #f0f9ff; }
    .diff-ok { color: #15803d; }
    .diff-changed { color: #dc2626; font-weight: 700; }

    /* Notes */
    .notes-box { background: #fffbf5; border: 1px solid #fed7aa; border-radius: 4px; padding: 12px; margin-bottom: 16px; min-height: 60px; }
    .notes-label { font-size: 9px; font-weight: 700; color: #92400e; text-transform: uppercase; margin-bottom: 6px; }
    .notes-text { font-size: 10px; color: #4b5563; line-height: 1.6; }

    /* Signatures */
    .signatures { display: table; width: 100%; margin-top: 20px; }
    .sig-col { display: table-cell; width: 45%; vertical-align: top; }
    .sig-spacer { display: table-cell; width: 10%; }
    .sig-box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 12px; min-height: 100px; }
    .sig-title { font-size: 10px; font-weight: 700; color: #1f2937; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 8px; }
    .sig-name { font-size: 10px; color: #6b7280; margin-bottom: 6px; }
    .sig-image { max-width: 100%; max-height: 60px; border: 1px solid #e5e7eb; border-radius: 2px; }
    .sig-line { border-bottom: 1px solid #9ca3af; margin-top: 50px; }
    .sig-date { font-size: 9px; color: #9ca3af; margin-top: 3px; }

    /* Footer */
    .footer { border-top: 1px solid #e5e7eb; margin-top: 16px; padding-top: 8px; }
    .footer-text { font-size: 8px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">LocaFiesta</div>
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
                    <span class="info-value">{{ $reservation->user->full_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Téléphone</span>
                    <span class="info-value">{{ $reservation->user->phone }}</span>
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
                    <span class="info-value">{{ $inspection->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Agent</span>
                    <span class="info-value">{{ $inspection->agent?->full_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Checklist --}}
    @if($inspection->type === 'departure' || !$inspection->departure_inspection_id)
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
            @if($inspection->checklist && count($inspection->checklist) > 0)
                @foreach($inspection->checklist as $checkItem)
                <tr>
                    <td style="font-weight: 600;">{{ $checkItem['element'] ?? $checkItem['name'] ?? '—' }}</td>
                    <td>
                        @php
                            $status = $checkItem['status'] ?? $checkItem['state'] ?? '';
                            $statusClass = match($status) {
                                'good', 'bon_etat', 'bon état' => 'status-good',
                                'normal', 'usure_normale', 'usure normale' => 'status-normal',
                                'damaged', 'degrade', 'dégradé' => 'status-damaged',
                                'missing', 'manquant' => 'status-missing',
                                default => '',
                            };
                            $statusLabel = match($status) {
                                'good', 'bon_etat', 'bon état' => 'Bon état',
                                'normal', 'usure_normale', 'usure normale' => 'Usure normale',
                                'damaged', 'degrade', 'dégradé' => 'Dégradé',
                                'missing', 'manquant' => 'Manquant',
                                default => $status,
                            };
                        @endphp
                        <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td style="color: #6b7280;">{{ $checkItem['observations'] ?? $checkItem['notes'] ?? '—' }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" style="text-align: center; color: #9ca3af; font-style: italic;">Aucun élément enregistré</td>
                </tr>
            @endif
        </tbody>
    </table>
    @endif

    {{-- Comparison (return only) --}}
    @if($inspection->type === 'return' && $inspection->departureInspection)
    <div class="section-title">Comparaison Départ / Retour</div>
    <table class="compare-table">
        <thead>
            <tr>
                <th style="width: 28%;">Élément</th>
                <th style="width: 22%;">État au départ</th>
                <th style="width: 22%;">État au retour</th>
                <th style="width: 28%;">Différence / Observations</th>
            </tr>
        </thead>
        <tbody>
            @php
                $departureChecklist = $inspection->departureInspection->checklist ?? [];
                $returnChecklist = $inspection->checklist ?? [];
                $departureMap = collect($departureChecklist)->keyBy('element');
            @endphp
            @foreach($returnChecklist as $returnItem)
            @php
                $elementKey = $returnItem['element'] ?? $returnItem['name'] ?? '';
                $departureItem = $departureMap->get($elementKey);
                $dStatus = $departureItem['status'] ?? $departureItem['state'] ?? '—';
                $rStatus = $returnItem['status'] ?? $returnItem['state'] ?? '—';
                $isDifferent = $dStatus !== $rStatus;
            @endphp
            <tr>
                <td style="font-weight: 600;">{{ $elementKey }}</td>
                <td>{{ $dStatus }}</td>
                <td class="{{ $isDifferent ? 'diff-changed' : '' }}">{{ $rStatus }}</td>
                <td class="{{ $isDifferent ? 'diff-changed' : 'diff-ok' }}">
                    @if($isDifferent)
                        <strong>Différence constatée</strong><br>
                        <span style="font-size: 9px;">{{ $returnItem['observations'] ?? '' }}</span>
                    @else
                        <span>Aucune différence</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- General notes --}}
    <div class="section-title">Notes générales</div>
    <div class="notes-box">
        @if($inspection->notes)
            <div class="notes-text">{{ $inspection->notes }}</div>
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
                <div class="sig-name">{{ $inspection->agent?->full_name ?? '—' }}</div>
                @if($inspection->agent_signature_path && Str::startsWith($inspection->agent_signature_path, 'data:image'))
                    <img src="{{ $inspection->agent_signature_path }}" class="sig-image" alt="Signature agent">
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
                    {{ $reservation->user->full_name }}<br>
                    <span style="font-size: 9px; color: #9ca3af;">Lu et approuvé</span>
                </div>
                @if($inspection->client_signature_path && Str::startsWith($inspection->client_signature_path, 'data:image'))
                    <img src="{{ $inspection->client_signature_path }}" class="sig-image" alt="Signature client">
                @else
                    <div class="sig-line"></div>
                    <div class="sig-date">Signature</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-text">
            LocaFiesta — {{ $company['address'] ?? '' }} — SIRET {{ $company['siret'] ?? '' }}<br>
            Document généré le {{ now()->format('d/m/Y à H:i') }} — Réservation {{ $reservation->reference }}
        </div>
    </div>

</div>
</body>
</html>
