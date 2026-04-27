@extends('layouts.client')

@section('title', 'Paiement de l\'acompte')

@push('head')
<script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
<div class="max-w-lg mx-auto space-y-6">

    <div>
        <a href="{{ route('client.reservations.show', $reservation) }}" class="text-gray-400 hover:text-gray-600 text-sm">
            <i class="fas fa-arrow-left mr-1"></i>Retour à la réservation
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Paiement de l'acompte</h1>
    </div>

    {{-- Reservation recap --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-receipt text-orange-500"></i> Récapitulatif
        </h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Réservation</span>
                <span class="font-mono font-medium text-gray-900">{{ $reservation->reference }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Période</span>
                <span>{{ $reservation->start_date->format('d/m/Y') }} → {{ $reservation->end_date->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Matériels</span>
                <span>{{ $reservation->items->count() }} article(s)</span>
            </div>
            <div class="border-t pt-2 mt-2">
                <div class="flex justify-between text-gray-700">
                    <span>Total de la réservation</span>
                    <span class="font-semibold">{{ number_format($reservation->total_amount, 2, ',', ' ') }} €</span>
                </div>
                <div class="flex justify-between text-orange-600 font-bold text-lg mt-1">
                    <span>Acompte à payer</span>
                    <span>{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stripe payment form --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-credit-card text-orange-500"></i> Paiement sécurisé
        </h2>

        <div id="payment-message" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span id="payment-message-text"></span>
        </div>

        <form id="payment-form">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Carte bancaire</label>
                <div id="payment-element"
                     class="border border-gray-300 rounded-lg p-3 focus-within:ring-2 focus-within:ring-orange-400 focus-within:border-orange-400 transition-all">
                </div>
            </div>

            <button id="submit-btn" type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold px-6 py-3.5 rounded-lg transition-colors text-base">
                <span id="btn-icon"><i class="fas fa-lock"></i></span>
                <span id="btn-text">
                    Payer {{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €
                </span>
                <span id="btn-spinner" class="hidden">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </form>

        <div class="mt-4 flex items-center justify-center gap-3 text-xs text-gray-400">
            <i class="fas fa-shield-halved text-green-500"></i>
            Paiement 100% sécurisé par Stripe
            <img src="https://stripe.com/img/v3/home/social.png" alt="Stripe" class="h-4 opacity-50">
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(async () => {
    const stripe = Stripe('{{ $stripeKey }}');
    const elements = stripe.elements({ clientSecret: '{{ $clientSecret }}' });

    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const btnIcon = document.getElementById('btn-icon');
    const messageEl = document.getElementById('payment-message');
    const messageText = document.getElementById('payment-message-text');

    function setLoading(isLoading) {
        submitBtn.disabled = isLoading;
        btnSpinner.classList.toggle('hidden', !isLoading);
        btnIcon.classList.toggle('hidden', isLoading);
    }

    function showError(message) {
        messageEl.classList.remove('hidden');
        messageText.textContent = message;
        setLoading(false);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        setLoading(true);
        messageEl.classList.add('hidden');

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: '{{ route('client.reservations.payment-success', $reservation) }}',
            },
        });

        if (error) {
            if (error.type === 'card_error' || error.type === 'validation_error') {
                showError(error.message);
            } else {
                showError('Une erreur inattendue s\'est produite. Veuillez réessayer.');
            }
        }
        // If no error, Stripe will redirect to return_url
    });
})();
</script>
@endpush
