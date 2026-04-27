@extends('layouts.client')

@section('title', 'Réservation confirmée !')

@section('content')
<div class="max-w-lg mx-auto py-8 text-center space-y-6">

    {{-- Success icon --}}
    <div class="flex items-center justify-center">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
            <i class="fas fa-check-circle text-green-500 text-5xl"></i>
        </div>
    </div>

    {{-- Message --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Votre réservation est confirmée !</h1>
        <p class="text-gray-500">Merci pour votre réservation chez LocaFiesta. Vous allez recevoir un email de confirmation.</p>
    </div>

    {{-- Reservation card --}}
    <div class="bg-white rounded-xl shadow-sm border p-6 text-left">
        <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-calendar-check text-green-500"></i>
            Résumé de votre réservation
        </h2>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-500">Référence</span>
                <span class="font-mono font-bold text-gray-900">{{ $reservation->reference }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-500">Période</span>
                <div class="text-right">
                    <span class="font-medium text-gray-900">
                        {{ $reservation->start_date->format('d/m/Y') }} à {{ $reservation->start_time }}
                    </span>
                    <br>
                    <span class="text-gray-500 text-xs">
                        → {{ $reservation->end_date->format('d/m/Y') }} à {{ $reservation->end_time }}
                    </span>
                </div>
            </div>
            <div class="py-2 border-b border-gray-100">
                <span class="text-gray-500 block mb-2">Matériels</span>
                @foreach($reservation->items as $item)
                <div class="flex items-center gap-2 text-gray-900">
                    <i class="fas fa-box text-gray-400 text-xs"></i>
                    {{ $item->equipment->name }}
                </div>
                @endforeach
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-500">Acompte payé</span>
                <span class="font-bold text-green-600">{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-500">Solde restant à régler</span>
                <span class="font-semibold text-gray-900">{{ number_format($reservation->remaining_amount, 2, ',', ' ') }} €</span>
            </div>
        </div>
    </div>

    {{-- Info box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700 text-left">
        <div class="flex items-start gap-2">
            <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
            <p>Le solde restant sera à régler lors de la remise du matériel. Vous recevrez un rappel 24h avant votre location.</p>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('client.reservations.show', $reservation) }}"
           class="inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
            <i class="fas fa-eye"></i>
            Voir ma réservation
        </a>
        <a href="{{ route('client.dashboard') }}"
           class="inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-700 font-medium px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-home"></i>
            Tableau de bord
        </a>
    </div>

</div>
@endsection
