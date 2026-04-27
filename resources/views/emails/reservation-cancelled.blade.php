<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réservation annulée — LocaFiesta</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6;">
  <tr>
    <td align="center" style="padding:32px 16px;">

      <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);max-width:600px;width:100%;">

        <!-- Header -->
        <tr>
          <td style="background-color:#f97316;padding:28px 32px;text-align:center;">
            <p style="margin:0;font-size:28px;font-weight:900;color:#ffffff;">LocaFiesta</p>
            <p style="margin:4px 0 0;font-size:12px;color:#ffedd5;">Location de matériel festif</p>
          </td>
        </tr>

        <!-- Icon + Title -->
        <tr>
          <td style="padding:32px 32px 0;text-align:center;">
            <div style="width:64px;height:64px;background-color:#fee2e2;border-radius:50%;display:inline-block;line-height:64px;text-align:center;font-size:32px;">❌</div>
            <h1 style="margin:16px 0 4px;font-size:22px;font-weight:700;color:#1f2937;">Réservation annulée</h1>
            <p style="margin:0;color:#6b7280;font-size:14px;">
                Bonjour {{ $reservation->user->first_name }}, votre réservation a été annulée.
            </p>
          </td>
        </tr>

        <!-- Details -->
        <tr>
          <td style="padding:24px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:8px;overflow:hidden;">
              <tr>
                <td style="padding:14px 20px;border-bottom:1px solid #fecaca;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:11px;color:#991b1b;text-transform:uppercase;font-weight:700;width:40%;">Référence</td>
                      <td style="font-size:13px;font-weight:700;color:#1f2937;font-family:monospace;">{{ $reservation->reference }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding:12px 20px;border-bottom:1px solid #fecaca;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:11px;color:#991b1b;text-transform:uppercase;font-weight:700;width:40%;vertical-align:top;">Période</td>
                      <td style="font-size:12px;color:#1f2937;">
                        Du {{ $reservation->start_date->format('d/m/Y') }} au {{ $reservation->end_date->format('d/m/Y') }}
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding:12px 20px;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:11px;color:#991b1b;text-transform:uppercase;font-weight:700;width:40%;vertical-align:top;">Matériels</td>
                      <td style="font-size:12px;color:#1f2937;">
                        @foreach($reservation->items as $item)
                          {{ $item->equipment->name }}@if(!$loop->last)<br>@endif
                        @endforeach
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Refund info -->
        <tr>
          <td style="padding:0 32px 24px;">
            @if($reservation->deposit_refunded)
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;">
              <tr>
                <td style="padding:14px 16px;font-size:13px;color:#15803d;line-height:1.6;">
                  <strong>✅ Remboursement de l'acompte</strong><br>
                  <span style="font-size:12px;">
                    Votre acompte de <strong>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</strong>
                    vous sera remboursé sous 5 à 10 jours ouvrés sur votre moyen de paiement d'origine.
                  </span>
                </td>
              </tr>
            </table>
            @else
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fffbeb;border:1px solid #fde68a;border-radius:8px;">
              <tr>
                <td style="padding:14px 16px;font-size:13px;color:#92400e;line-height:1.6;">
                  <strong>⚠️ Acompte non remboursable</strong><br>
                  <span style="font-size:12px;">
                    Conformément à nos conditions générales, l'annulation intervenant moins de 48h avant le début
                    de la location entraîne la perte de l'acompte de
                    <strong>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</strong>.
                  </span>
                </td>
              </tr>
            </table>
            @endif
          </td>
        </tr>

        <!-- CTA -->
        <tr>
          <td style="padding:0 32px 32px;text-align:center;">
            <a href="{{ route('client.reservations.create') }}"
               style="display:inline-block;background-color:#f97316;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:8px;">
              Faire une nouvelle réservation
            </a>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#f9fafb;border-top:1px solid #f3f4f6;padding:20px 32px;text-align:center;">
            <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.6;">
              © {{ date('Y') }} LocaFiesta. Tous droits réservés.<br>
              Pour toute question, contactez-nous à <a href="mailto:contact@locafiesta.fr" style="color:#f97316;">contact@locafiesta.fr</a>
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
