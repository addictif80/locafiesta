<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\PromoCode;
use App\Services\ReservationService;
use App\Services\StripeService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService,
        private StripeService $stripeService
    ) {}

    public function index()
    {
        $reservations = auth()->user()->reservations()
            ->with('items.equipment')
            ->latest()
            ->paginate(10);
        return view('client.reservations.index', compact('reservations'));
    }

    public function create()
    {
        if ($this->hasLateReturn()) {
            return redirect()->route('client.reservations.index')
                ->with('error', 'Vous avez une réservation en retard. Veuillez la clôturer avant d\'effectuer une nouvelle réservation.');
        }

        $equipment = Equipment::where('is_active', true)->with('photos', 'primaryPhoto')->get();
        return view('client.reservations.create', compact('equipment'));
    }

    public function getAvailability(Request $request)
    {
        $request->validate([
            'equipment_ids' => 'required|array',
            'equipment_ids.*' => 'exists:equipment,id',
        ]);

        $unavailableDates = $this->reservationService->getUnavailableDates(
            $request->equipment_ids
        );

        return response()->json($unavailableDates);
    }

    public function checkPromoCode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $promo = PromoCode::where('code', strtoupper($request->code))->first();

        if (!$promo || !$promo->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Code promo invalide ou expiré.']);
        }

        return response()->json([
            'valid' => true,
            'type' => $promo->type,
            'value' => $promo->value,
            'label' => $promo->type === 'percentage' ? "-{$promo->value}%" : "-{$promo->value}€",
        ]);
    }

    private function hasLateReturn(): bool
    {
        return auth()->user()->reservations()
            ->where('status', 'in_progress')
            ->whereDate('end_date', '<', today())
            ->exists();
    }

    public function store(Request $request)
    {
        if ($this->hasLateReturn()) {
            return redirect()->route('client.reservations.index')
                ->with('error', 'Vous avez une réservation en retard. Veuillez la clôturer avant d\'effectuer une nouvelle réservation.');
        }

        $user = auth()->user();

        $data = $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipment,id',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'promo_code' => 'nullable|string',
            'use_different_address' => 'boolean',
            'use_address' => 'nullable|required_if:use_different_address,1|string|max:255',
            'use_postal_code' => 'nullable|required_if:use_different_address,1|string|max:10',
            'use_city' => 'nullable|required_if:use_different_address,1|string|max:100',
        ]);

        $data['client_id'] = $user->id;
        $reservation = $this->reservationService->createReservation($data, byAdmin: false);

        // Create Stripe Payment Intent
        $paymentIntent = $this->stripeService->createPaymentIntent($reservation);

        return view('client.reservations.payment', [
            'reservation' => $reservation,
            'clientSecret' => $paymentIntent->client_secret,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    public function show(Reservation $reservation)
    {
        abort_unless($reservation->client_id === auth()->id(), 403);
        $reservation->load(['items.equipment', 'invoices', 'inspections', 'promoCode']);
        return view('client.reservations.show', compact('reservation'));
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->client_id === auth()->id(), 403);
        abort_unless(in_array($reservation->status, ['pending_payment', 'confirmed']), 400);

        $request->validate(['reason' => 'nullable|string|max:500']);

        $this->reservationService->cancelReservation($reservation, $request->reason, byAdmin: false);

        return redirect()->route('client.reservations.index')
            ->with('success', 'Votre réservation a été annulée.');
    }

    public function paymentSuccess(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->client_id === auth()->id(), 403);

        if ($request->payment_intent_status === 'succeeded') {
            $this->stripeService->confirmPayment($reservation, $request->payment_intent);
        }

        return view('client.reservations.payment-success', compact('reservation'));
    }
}
