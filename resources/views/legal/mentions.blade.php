@extends('layouts.client')
@section('title', 'Mentions légales')
@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-8">Mentions légales</h1>
    <div class="bg-white rounded-xl shadow-sm border p-8 space-y-6 text-sm text-gray-700">
        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Éditeur du site</h2>
            <p><strong>{{ \App\Models\Setting::get('company_name', 'LocaFiesta') }}</strong></p>
            <p>{{ \App\Models\Setting::get('company_address') }}</p>
            @if(\App\Models\Setting::get('company_siret'))
            <p>SIRET : {{ \App\Models\Setting::get('company_siret') }}</p>
            @endif
            <p>Téléphone : {{ \App\Models\Setting::get('company_phone') }}</p>
            <p>Email : <a href="mailto:{{ \App\Models\Setting::get('company_email') }}" class="text-orange-500">{{ \App\Models\Setting::get('company_email') }}</a></p>
        </section>
        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Hébergement</h2>
            <p>Ce site est hébergé chez un prestataire d'hébergement professionnel.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Propriété intellectuelle</h2>
            <p>L'ensemble du contenu de ce site (textes, images, logos) est la propriété exclusive de {{ \App\Models\Setting::get('company_name', 'LocaFiesta') }}. Toute reproduction, même partielle, est interdite sans autorisation préalable.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Données personnelles</h2>
            <p>Pour toute information relative au traitement de vos données personnelles, consultez notre <a href="{{ route('privacy') }}" class="text-orange-500">politique de confidentialité</a>.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Paiements en ligne</h2>
            <p>Les paiements en ligne sont sécurisés et traités par <strong>Stripe</strong>, prestataire certifié PCI-DSS. Nous ne stockons aucune donnée bancaire.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Droit applicable</h2>
            <p>Le présent site et ses mentions légales sont soumis au droit français. En cas de litige, les tribunaux français seront compétents.</p>
        </section>
    </div>
</div>
@endsection
