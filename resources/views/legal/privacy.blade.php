@extends('layouts.client')
@section('title', 'Politique de confidentialité')
@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Politique de confidentialité</h1>
    <p class="text-sm text-gray-500 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>

    <div class="bg-white rounded-xl shadow-sm border p-8 prose prose-gray max-w-none space-y-6 text-sm text-gray-700 leading-relaxed">
        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">1. Responsable du traitement</h2>
            <p>{{ \App\Models\Setting::get('company_name', 'LocaFiesta') }}, dont le siège social est situé à {{ \App\Models\Setting::get('company_address') }}, est responsable du traitement de vos données personnelles.</p>
            <p>Contact : <a href="mailto:{{ \App\Models\Setting::get('company_email') }}" class="text-orange-500">{{ \App\Models\Setting::get('company_email') }}</a></p>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">2. Données collectées</h2>
            <p>Dans le cadre de notre service de location de matériel, nous collectons les données suivantes :</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Nom, prénom, date de naissance</li>
                <li>Adresse email et numéro de téléphone</li>
                <li>Adresse postale (facturation et utilisation)</li>
                <li>Données de paiement (traitées par Stripe, nous ne stockons pas vos données bancaires)</li>
                <li>Historique des réservations et locations</li>
                <li>Informations relatives aux états des lieux</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">3. Finalités du traitement</h2>
            <p>Vos données sont utilisées pour :</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Gérer votre compte et vos réservations</li>
                <li>Traiter les paiements et émettre les factures</li>
                <li>Réaliser les états des lieux</li>
                <li>Vous envoyer les confirmations et rappels de réservation</li>
                <li>Respecter nos obligations légales</li>
                <li>Prévenir les fraudes et litiges</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">4. Base légale</h2>
            <p>Le traitement de vos données repose sur :</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li><strong>L'exécution du contrat</strong> : gestion des réservations et locations</li>
                <li><strong>Votre consentement</strong> : pour les communications marketing (si applicable)</li>
                <li><strong>Nos obligations légales</strong> : conservation des factures pendant 10 ans</li>
                <li><strong>Notre intérêt légitime</strong> : prévention de la fraude, amélioration du service</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">5. Conservation des données</h2>
            <p>Vos données personnelles sont conservées pendant :</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Données de compte actif : durée de la relation commerciale + 3 ans</li>
                <li>Données de facturation : 10 ans (obligation légale)</li>
                <li>Données d'état des lieux : durée de la relation + 5 ans</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">6. Vos droits</h2>
            <p>Conformément au RGPD, vous disposez des droits suivants :</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li><strong>Droit d'accès</strong> : obtenir une copie de vos données</li>
                <li><strong>Droit de rectification</strong> : corriger vos données inexactes</li>
                <li><strong>Droit à l'effacement</strong> : demander la suppression de votre compte</li>
                <li><strong>Droit à la portabilité</strong> : recevoir vos données dans un format structuré</li>
                <li><strong>Droit d'opposition</strong> : vous opposer à certains traitements</li>
                <li><strong>Droit de limitation</strong> : limiter le traitement de vos données</li>
            </ul>
            <p class="mt-3">Pour exercer vos droits, connectez-vous à votre espace client, section "Mon profil" > "Confidentialité", ou contactez-nous à <a href="mailto:{{ \App\Models\Setting::get('company_email') }}" class="text-orange-500">{{ \App\Models\Setting::get('company_email') }}</a>.</p>
            <p class="mt-2">Vous pouvez également introduire une réclamation auprès de la <strong>CNIL</strong> (www.cnil.fr).</p>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">7. Cookies</h2>
            <p>Nous utilisons uniquement des cookies strictement nécessaires au fonctionnement du site (session, sécurité CSRF). Aucun cookie de tracking ou publicitaire n'est utilisé.</p>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">8. Sécurité</h2>
            <p>Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données : chiffrement des communications (HTTPS), hachage des mots de passe, accès restreint aux données.</p>
        </section>
    </div>
</div>
@endsection
