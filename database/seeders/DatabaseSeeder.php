<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin
        User::firstOrCreate(
            ['email' => 'admin@locafiesta.fr'],
            [
                'name' => 'Admin LocaFiesta',
                'first_name' => 'Admin',
                'last_name' => 'LocaFiesta',
                'email' => 'admin@locafiesta.fr',
                'password' => Hash::make('Admin@2024!'),
                'role' => 'admin',
                'rgpd_consent' => true,
                'rgpd_consent_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Default settings
        $settings = [
            ['key' => 'deposit_percentage', 'value' => '30', 'type' => 'integer', 'label' => 'Pourcentage acompte (%)', 'group' => 'payment'],
            ['key' => 'cancellation_hours', 'value' => '48', 'type' => 'integer', 'label' => 'Délai annulation (heures)', 'group' => 'reservation'],
            ['key' => 'company_name', 'value' => 'LocaFiesta', 'type' => 'string', 'label' => 'Nom de la société', 'group' => 'company'],
            ['key' => 'company_address', 'value' => '1 rue de la Fête, 75001 Paris', 'type' => 'string', 'label' => 'Adresse', 'group' => 'company'],
            ['key' => 'company_phone', 'value' => '01 23 45 67 89', 'type' => 'string', 'label' => 'Téléphone', 'group' => 'company'],
            ['key' => 'company_email', 'value' => 'contact@locafiesta.fr', 'type' => 'string', 'label' => 'Email', 'group' => 'company'],
            ['key' => 'company_siret', 'value' => '', 'type' => 'string', 'label' => 'SIRET', 'group' => 'company'],
            ['key' => 'invoice_prefix', 'value' => 'FAC', 'type' => 'string', 'label' => 'Préfixe facture', 'group' => 'invoice'],
            ['key' => 'cgv_text', 'value' => "CONDITIONS GÉNÉRALES DE LOCATION\n\nArticle 1 - Objet\nLes présentes conditions générales régissent les relations entre LocaFiesta et ses clients.\n\nArticle 2 - Réservation et acompte\nToute réservation est soumise au versement d'un acompte. L'acompte est dû au moment de la confirmation de la réservation.\n\nArticle 3 - Annulation\nL'annulation plus de 48 heures avant le début de la location donne lieu au remboursement intégral de l'acompte. En cas d'annulation moins de 48 heures avant le début de la location, l'acompte est conservé.\n\nArticle 4 - État des lieux\nUn état des lieux contradictoire est réalisé au départ et au retour de la location. Toute dégradation constatée lors de l'état des lieux de retour sera facturée selon le barème en vigueur.", 'type' => 'text', 'label' => 'CGV', 'group' => 'legal'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
