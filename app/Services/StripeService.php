<?php

namespace App\Services;

use App\Models\Reservation;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Reservation $reservation): \Stripe\PaymentIntent
    {
        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => (int) round($reservation->deposit_amount * 100),
            'currency' => 'eur',
            'metadata' => [
                'reservation_id' => $reservation->id,
                'reservation_reference' => $reservation->reference,
            ],
            'description' => "Acompte réservation {$reservation->reference}",
            'receipt_email' => $reservation->client->email,
        ]);

        $reservation->update(['stripe_payment_intent_id' => $paymentIntent->id]);

        return $paymentIntent;
    }

    public function confirmPayment(Reservation $reservation, string $paymentIntentId): void
    {
        $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

        if ($paymentIntent->status === 'succeeded') {
            $reservation->update([
                'status' => 'confirmed',
                'stripe_charge_id' => $paymentIntent->latest_charge,
                'deposit_paid_at' => now(),
            ]);
        }
    }

    public function refundDeposit(Reservation $reservation): void
    {
        if (!$reservation->stripe_charge_id) return;

        try {
            $this->stripe->refunds->create([
                'charge' => $reservation->stripe_charge_id,
                'reason' => 'requested_by_customer',
            ]);

            $reservation->update([
                'deposit_refunded' => true,
                'deposit_refunded_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error("Stripe refund failed for reservation {$reservation->id}: " . $e->getMessage());
        }
    }
}
