<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') - LocaFiesta Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('head')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full bg-gray-100" x-data="{ sidebarOpen: true }">
<div class="flex h-full">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col flex-shrink-0" x-show="sidebarOpen">
        <div class="h-16 flex items-center px-6 border-b border-gray-700">
            <span class="text-xl font-bold text-orange-400"><i class="fas fa-party-horn mr-2"></i>LocaFiesta</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-chart-line w-5 mr-3"></i>Tableau de bord
            </a>
            <a href="{{ route('admin.reservations.index') }}" class="nav-link {{ request()->routeIs('admin.reservations.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-calendar-check w-5 mr-3"></i>Réservations
                @if(!empty($adminLateReturnsCount) && $adminLateReturnsCount > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5 min-w-[20px] text-center animate-pulse">{{ $adminLateReturnsCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.materiel.index') }}" class="nav-link {{ request()->routeIs('admin.materiel.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-boxes-stacked w-5 mr-3"></i>Matériel
            </a>
            <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-users w-5 mr-3"></i>Clients
            </a>
            <a href="{{ route('admin.factures.index') }}" class="nav-link {{ request()->routeIs('admin.factures.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>Factures
            </a>
            <a href="{{ route('admin.inspections.index') }}" class="nav-link {{ request()->routeIs('admin.inspections.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-clipboard-check w-5 mr-3"></i>États des lieux
            </a>
            <a href="{{ route('admin.security-deposits.index') }}" class="nav-link {{ request()->routeIs('admin.security-deposits.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-money-check w-5 mr-3"></i>Cautions
            </a>
            <a href="{{ route('admin.mail-log.index') }}" class="nav-link {{ request()->routeIs('admin.mail-log.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-envelope w-5 mr-3"></i>Mails envoyés
            </a>
            <hr class="border-gray-700 my-3">
            <a href="{{ route('admin.promo-codes.index') }}" class="nav-link {{ request()->routeIs('admin.promo-codes.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-tag w-5 mr-3"></i>Codes promo
            </a>
            <a href="{{ route('admin.blocked-dates.index') }}" class="nav-link {{ request()->routeIs('admin.blocked-dates.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-calendar-xmark w-5 mr-3"></i>Blocages de dates
            </a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.team.index') }}" class="nav-link {{ request()->routeIs('admin.team.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-user-gear w-5 mr-3"></i>Équipe
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'bg-orange-600' : 'hover:bg-gray-800' }} flex items-center px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-cog w-5 mr-3"></i>Paramètres
            </a>
            @endif
        </nav>
        <div class="px-4 py-4 border-t border-gray-700">
            <div class="text-xs text-gray-400 mb-2">{{ auth()->user()->full_name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-white flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white border-b flex items-center px-6 gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Administration')</h1>
            <div class="ml-auto flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Agent' }}</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
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
    </div>
</div>
@stack('scripts')
</body>
</html>
