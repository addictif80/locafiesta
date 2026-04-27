<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f9fafb;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
      <tr><td style="background:#f97316;padding:32px 40px;text-align:center;">
        <h1 style="color:#fff;margin:0;font-size:24px;">🎉 LocaFiesta</h1>
        <p style="color:#fed7aa;margin:8px 0 0;font-size:14px;">Rappel de retour</p>
      </td></tr>
      <tr><td style="padding:40px;">
        <h2 style="color:#1f2937;margin:0 0 16px;font-size:20px;">Votre location se termine demain</h2>
        <p style="color:#6b7280;font-size:15px;line-height:1.6;margin:0 0 24px;">
          Bonjour {{ $reservation->client->first_name }},<br><br>
          Nous vous rappelons que votre location de matériel doit être retournée <strong>demain avant {{ substr($reservation->end_time, 0, 5) }}</strong>.
        </p>
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;margin-bottom:24px;">
          <tr><td style="padding:20px;">
            <p style="margin:0 0 8px;color:#166534;font-size:13px;font-weight:bold;text-transform:uppercase;">Référence</p>
            <p style="margin:0 0 16px;color:#1f2937;font-size:16px;font-weight:bold;">{{ $reservation->reference }}</p>
            <p style="margin:0 0 8px;color:#166534;font-size:13px;font-weight:bold;text-transform:uppercase;">Retour prévu le</p>
            <p style="margin:0 0 16px;color:#1f2937;font-size:16px;font-weight:bold;">{{ $reservation->end_date->format('d/m/Y') }} à {{ substr($reservation->end_time, 0, 5) }}</p>
            <p style="margin:0 0 8px;color:#166534;font-size:13px;font-weight:bold;text-transform:uppercase;">Matériel à retourner</p>
            <ul style="margin:0;padding-left:20px;color:#1f2937;">
              @foreach($reservation->items as $item)
              <li>{{ $item->equipment->name }}</li>
              @endforeach
            </ul>
          </td></tr>
        </table>
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:16px;margin-bottom:24px;">
          <p style="margin:0;color:#991b1b;font-size:14px;">
            <strong>Important :</strong> Veuillez retourner le matériel en bon état. Un état des lieux contradictoire sera réalisé lors du retour. Toute dégradation constatée fera l'objet d'une facturation complémentaire.
          </p>
        </div>
        <p style="color:#6b7280;font-size:14px;margin:0 0 24px;">
          Pour toute question : <a href="mailto:{{ \App\Models\Setting::get('company_email') }}" style="color:#f97316;">{{ \App\Models\Setting::get('company_email') }}</a> — {{ \App\Models\Setting::get('company_phone') }}
        </p>
        <p style="color:#6b7280;font-size:14px;margin:0;">À demain,<br><strong>L'équipe {{ \App\Models\Setting::get('company_name', 'LocaFiesta') }}</strong></p>
      </td></tr>
      <tr><td style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
        <p style="color:#9ca3af;font-size:12px;margin:0;">{{ \App\Models\Setting::get('company_name', 'LocaFiesta') }} — {{ \App\Models\Setting::get('company_address') }}</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
