<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LocaFiesta') - LocaFiesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('head')
    <style>
        [x-cloak] { display: none !important; }
        .fc-day-disabled { background: #f3f4f6 !important; cursor: not-allowed !important; }
    </style>
</head>
<body class="h-full bg-gray-50">
    @yield('body')

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed bottom-4 right-4 z-50 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="fixed bottom-4 right-4 z-50 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    @stack('scripts')
</body>
</html>
