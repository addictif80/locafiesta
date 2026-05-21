<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mon espace') - LocaFiesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('head')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full bg-gray-50">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('client.dashboard') }}" class="text-xl font-bold text-orange-500">
                <i class="fas fa-party-horn mr-2"></i>LocaFiesta
            </a>
            <div class="flex items-center gap-6">
                <a href="{{ route('client.reservations.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600">
                    <i class="fas fa-plus mr-1"></i>Nouvelle réservation
                </a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                        <i class="fas fa-user-circle text-xl"></i>
                        <span class="text-sm">{{ auth()->user()->first_name }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-1 z-50">
                        <a href="{{ route('client.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-chart-line w-4 mr-2"></i>Tableau de bord
                        </a>
                        <a href="{{ route('client.reservations.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-calendar w-4 mr-2"></i>Mes réservations
                        </a>
                        <a href="{{ route('client.invoices.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-file-invoice w-4 mr-2"></i>Mes factures
                        </a>
                        <a href="{{ route('client.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user-cog w-4 mr-2"></i>Mon profil
                        </a>
                        <hr class="my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt w-4 mr-2"></i>Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @if(!empty($clientLateReservations) && $clientLateReservations->isNotEmpty())
    <div class="bg-red-600 text-white py-2">
        <div class="max-w-6xl mx-auto px-4 flex items-center gap-3 text-sm">
            <i class="fas fa-exclamation-triangle flex-shrink-0"></i>
            <span>
                @if($clientLateReservations->count() === 1)
                    Votre location <strong>{{ $clientLateReservations->first()->reference }}</strong>
                    est en retard depuis le {{ $clientLateReservations->first()->end_date->format('d/m/Y') }}.
                    <a href="{{ route('client.reservations.show', $clientLateReservations->first()) }}" class="underline font-semibold ml-1">Voir la réservation</a>
                @else
                    <strong>{{ $clientLateReservations->count() }} locations</strong> sont en retard de retour.
                    <a href="{{ route('client.reservations.index') }}" class="underline font-semibold ml-1">Voir mes réservations</a>
                @endif
            </span>
        </div>
    </div>
    @endif
    <main class="max-w-6xl mx-auto px-4 py-8">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 text-green-700 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-12 border-t py-8 bg-white">
        <div class="max-w-6xl mx-auto px-4 flex flex-wrap gap-4 justify-between items-center text-sm text-gray-500">
            <span>© {{ date('Y') }} LocaFiesta. Tous droits réservés.</span>
            <div class="flex gap-4">
                <a href="{{ route('privacy') }}" class="hover:text-gray-700">Politique de confidentialité</a>
                <a href="{{ route('mentions') }}" class="hover:text-gray-700">Mentions légales</a>
                <a href="{{ route('cgv') }}" class="hover:text-gray-700">CGV</a>
            </div>
        </div>
    </footer>
    @include('components.cookie-banner')
    @stack('scripts')
</body>
</html>
