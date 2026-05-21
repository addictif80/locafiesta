<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f9fafb;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
      <tr><td style="background:#dc2626;padding:32px 40px;text-align:center;">
        <h1 style="color:#fff;margin:0;font-size:24px;">⚠️ LocaFiesta</h1>
        <p style="color:#fca5a5;margin:8px 0 0;font-size:14px;">
          @if($isAdmin)Alerte retour en retard@else Retour en retard@endif
        </p>
      </td></tr>
      <tr><td style="padding:40px;">
        @if($isAdmin)
        <h2 style="color:#991b1b;margin:0 0 16px;font-size:20px;">Un retour est en retard</h2>
        <p style="color:#6b7280;font-size:15px;line-height:1.6;margin:0 0 24px;">
          La réservation <strong>{{ $reservation->reference }}</strong> de <strong>{{ $reservation->client->full_name }}</strong>
          aurait dû être retournée le <strong>{{ $reservation->end_date->format('d/m/Y') }} à {{ substr($reservation->end_time, 0, 5) }}</strong>.
          Le retard est de <strong>{{ $reservation->days_late }} jour(s)</strong>.
        </p>
        @else
        <h2 style="color:#991b1b;margin:0 0 16px;font-size:20px;">Votre retour est en retard</h2>
        <p style="color:#6b7280;font-size:15px;line-height:1.6;margin:0 0 24px;">
          Bonjour {{ $reservation->client->first_name }},<br><br>
          Votre location de matériel aurait dû être retournée le <strong>{{ $reservation->end_date->format('d/m/Y') }} à {{ substr($reservation->end_time, 0, 5) }}</strong>.
          Nous n'avons pas encore enregistré votre retour. Merci de nous contacter dès que possible.
        </p>
        @endif

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;margin-bottom:24px;">
          <tr><td style="padding:20px;">
            <p style="margin:0 0 8px;color:#991b1b;font-size:13px;font-weight:bold;text-transform:uppercase;">Référence</p>
            <p style="margin:0 0 16px;color:#1f2937;font-size:16px;font-weight:bold;">{{ $reservation->reference }}</p>
            @if($isAdmin)
            <p style="margin:0 0 8px;color:#991b1b;font-size:13px;font-weight:bold;text-transform:uppercase;">Client</p>
            <p style="margin:0 0 16px;color:#1f2937;font-size:15px;">{{ $reservation->client->full_name }} — {{ $reservation->client->email }} — {{ $reservation->client->phone }}</p>
            @endif
            <p style="margin:0 0 8px;color:#991b1b;font-size:13px;font-weight:bold;text-transform:uppercase;">Retour prévu le</p>
            <p style="margin:0 0 16px;color:#1f2937;font-size:16px;font-weight:bold;">{{ $reservation->end_date->format('d/m/Y') }} à {{ substr($reservation->end_time, 0, 5) }}</p>
            <p style="margin:0 0 8px;color:#991b1b;font-size:13px;font-weight:bold;text-transform:uppercase;">Matériel concerné</p>
            <ul style="margin:0;padding-left:20px;color:#1f2937;">
              @foreach($reservation->items as $item)
              <li>{{ $item->equipment->name }}</li>
              @endforeach
            </ul>
          </td></tr>
        </table>

        <div style="background:#fff7ed;border:1px solid #fdba74;border-radius:8px;padding:16px;margin-bottom:24px;">
          <p style="margin:0;color:#9a3412;font-size:14px;">
            @if($isAdmin)
            <strong>Action requise :</strong> Contactez le client pour organiser le retour du matériel dans les plus brefs délais.
            @else
            <strong>Important :</strong> Des frais de retard peuvent s'appliquer. Merci de nous contacter immédiatement pour organiser le retour du matériel.
            @endif
          </p>
        </div>

        <p style="color:#6b7280;font-size:14px;margin:0 0 24px;">
          Pour toute question : <a href="mailto:{{ \App\Models\Setting::get('company_email') }}" style="color:#dc2626;">{{ \App\Models\Setting::get('company_email') }}</a> — {{ \App\Models\Setting::get('company_phone') }}
        </p>
        <p style="color:#6b7280;font-size:14px;margin:0;">Cordialement,<br><strong>L'équipe {{ \App\Models\Setting::get('company_name', 'LocaFiesta') }}</strong></p>
      </td></tr>
      <tr><td style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
        <p style="color:#9ca3af;font-size:12px;margin:0;">{{ \App\Models\Setting::get('company_name', 'LocaFiesta') }} — {{ \App\Models\Setting::get('company_address') }}</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
