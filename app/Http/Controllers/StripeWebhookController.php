<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Invoice;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentSuccess($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentSuccess($paymentIntent): void
    {
        $reservation = Reservation::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if (!$reservation) return;

        $reservation->update([
            'status' => 'confirmed',
            'stripe_charge_id' => $paymentIntent->latest_charge,
            'deposit_paid_at' => now(),
        ]);

        Invoice::create([
            'client_id' => $reservation->client_id,
            'reservation_id' => $reservation->id,
            'type' => 'deposit',
            'amount' => $reservation->deposit_amount,
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'stripe',
        ]);

        // Send confirmation email
        try {
            \Mail::to($reservation->client)->send(new \App\Mail\ReservationConfirmed($reservation));
        } catch (\Exception $e) {
            \Log::error('Email confirmation failed: ' . $e->getMessage());
        }
    }

    private function handlePaymentFailed($paymentIntent): void
    {
        $reservation = Reservation::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if (!$reservation) return;
        $reservation->update(['status' => 'pending_payment']);
    }
}
