<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f9fafb;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
      <tr><td style="background:#f97316;padding:32px 40px;text-align:center;">
        <h1 style="color:#fff;margin:0;font-size:24px;">🎉 LocaFiesta</h1>
        <p style="color:#fed7aa;margin:8px 0 0;font-size:14px;">Rappel de location</p>
      </td></tr>
      <tr><td style="padding:40px;">
        <h2 style="color:#1f2937;margin:0 0 16px;font-size:20px;">Votre location commence demain !</h2>
        <p style="color:#6b7280;font-size:15px;line-height:1.6;margin:0 0 24px;">
          Bonjour {{ $reservation->client->first_name }},<br><br>
          Nous vous rappelons que votre location de matériel commence <strong>demain</strong>.
          Voici le récapitulatif de votre réservation.
        </p>
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;margin-bottom:24px;">
          <tr><td style="padding:20px;">
            <p style="margin:0 0 8px;color:#92400e;font-size:13px;font-weight:bold;text-transform:uppercase;">Référence</p>
            <p style="margin:0 0 16px;color:#1f2937;font-size:16px;font-weight:bold;">{{ $reservation->reference }}</p>
            <p style="margin:0 0 8px;color:#92400e;font-size:13px;font-weight:bold;text-transform:uppercase;">Dates</p>
            <p style="margin:0 0 16px;color:#1f2937;">Du <strong>{{ $reservation->start_date->format('d/m/Y') }}</strong> à {{ substr($reservation->start_time, 0, 5) }} au <strong>{{ $reservation->end_date->format('d/m/Y') }}</strong> à {{ substr($reservation->end_time, 0, 5) }}</p>
            <p style="margin:0 0 8px;color:#92400e;font-size:13px;font-weight:bold;text-transform:uppercase;">Matériel</p>
            <ul style="margin:0 0 16px;padding-left:20px;color:#1f2937;">
              @foreach($reservation->items as $item)
              <li>{{ $item->equipment->name }} ({{ $item->equipment->reference }})</li>
              @endforeach
            </ul>
            <p style="margin:0 0 8px;color:#92400e;font-size:13px;font-weight:bold;text-transform:uppercase;">Solde restant à régler</p>
            <p style="margin:0;color:#1f2937;font-size:18px;font-weight:bold;">{{ number_format($reservation->balance_amount, 2, ',', ' ') }} €</p>
          </td></tr>
        </table>
        <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:16px;margin-bottom:24px;">
          <p style="margin:0;color:#92400e;font-size:14px;">
            <strong>⚠️ Rappel :</strong> Pensez à apporter votre pièce d'identité et un chèque de caution lors de la récupération du matériel.
          </p>
        </div>
        <p style="color:#6b7280;font-size:14px;margin:0 0 24px;">
          En cas de question, contactez-nous à <a href="mailto:{{ \App\Models\Setting::get('company_email') }}" style="color:#f97316;">{{ \App\Models\Setting::get('company_email') }}</a> ou au {{ \App\Models\Setting::get('company_phone') }}.
        </p>
        <p style="color:#6b7280;font-size:14px;margin:0;">À demain !<br><strong>L'équipe {{ \App\Models\Setting::get('company_name', 'LocaFiesta') }}</strong></p>
      </td></tr>
      <tr><td style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
        <p style="color:#9ca3af;font-size:12px;margin:0;">{{ \App\Models\Setting::get('company_name', 'LocaFiesta') }} — {{ \App\Models\Setting::get('company_address') }}</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
