<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contrat de location — LocaFiesta</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6;">
  <tr>
    <td align="center" style="padding:32px 16px;">

      <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);max-width:600px;width:100%;">

        <!-- Header -->
        <tr>
          <td style="background-color:#f97316;padding:28px 32px;text-align:center;">
            <p style="margin:0;font-size:28px;font-weight:900;color:#ffffff;letter-spacing:-0.5px;">LocaFiesta</p>
            <p style="margin:4px 0 0;font-size:12px;color:#ffedd5;">Location de matériel festif</p>
          </td>
        </tr>

        <!-- Icon + Title -->
        <tr>
          <td style="padding:32px 32px 0;text-align:center;">
            <div style="width:64px;height:64px;background-color:#fff7ed;border-radius:50%;display:inline-block;line-height:64px;text-align:center;font-size:32px;">📄</div>
            <h1 style="margin:16px 0 4px;font-size:22px;font-weight:700;color:#1f2937;">Votre contrat de location</h1>
            <p style="margin:0;color:#6b7280;font-size:14px;">
              Bonjour {{ $reservation->client->first_name }}, veuillez trouver ci-joint votre contrat de location.
            </p>
          </td>
        </tr>

        <!-- Reservation details -->
        <tr>
          <td style="padding:24px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fff7ed;border:1px solid #fed7aa;border-radius:8px;overflow:hidden;">
              <tr>
                <td style="padding:16px 20px;border-bottom:1px solid #fed7aa;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:11px;color:#92400e;text-transform:uppercase;font-weight:700;width:40%;">Référence</td>
                      <td style="font-size:13px;font-weight:700;color:#1f2937;font-family:monospace;">{{ $reservation->reference }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding:12px 20px;border-bottom:1px solid #fed7aa;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:11px;color:#92400e;text-transform:uppercase;font-weight:700;width:40%;vertical-align:top;">Période</td>
                      <td style="font-size:12px;color:#1f2937;">
                        Du <strong>{{ $reservation->start_date->format('d/m/Y') }}</strong> à {{ $reservation->start_time }}<br>
                        au <strong>{{ $reservation->end_date->format('d/m/Y') }}</strong> à {{ $reservation->end_time }}
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding:12px 20px;border-bottom:1px solid #fed7aa;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:11px;color:#92400e;text-transform:uppercase;font-weight:700;width:40%;vertical-align:top;">Matériels</td>
                      <td style="font-size:12px;color:#1f2937;">
                        @foreach($reservation->items as $item)
                          {{ $item->equipment->name }}@if(!$loop->last)<br>@endif
                        @endforeach
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding:12px 20px;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:11px;color:#92400e;text-transform:uppercase;font-weight:700;width:40%;">Montant total</td>
                      <td style="font-size:14px;font-weight:700;color:#f97316;">{{ number_format($reservation->total_amount, 2, ',', ' ') }} €</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Info -->
        <tr>
          <td style="padding:0 32px 24px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;">
              <tr>
                <td style="padding:14px 16px;font-size:12px;color:#1d4ed8;line-height:1.6;">
                  ℹ️ Le contrat de location est joint en pièce jointe (PDF). Conservez ce document pour votre référence.
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- CTA -->
        <tr>
          <td style="padding:0 32px 32px;text-align:center;">
            <a href="{{ route('client.reservations.show', $reservation) }}"
               style="display:inline-block;background-color:#f97316;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;padding:14px 32px;border-radius:8px;">
              Voir ma réservation
            </a>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#f9fafb;border-top:1px solid #f3f4f6;padding:20px 32px;text-align:center;">
            <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.6;">
              © {{ date('Y') }} LocaFiesta. Tous droits réservés.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
