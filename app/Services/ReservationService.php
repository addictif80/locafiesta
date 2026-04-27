<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\PromoCode;
use App\Models\Reservation;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function createReservation(array $data, bool $byAdmin = false): Reservation
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $daysCount = $startDate->diffInDays($endDate) + 1;

        // Check availability
        foreach ($data['equipment_ids'] as $equipmentId) {
            $equipment = Equipment::findOrFail($equipmentId);
            if (!$equipment->isAvailableBetween($startDate, $endDate)) {
                throw ValidationException::withMessages([
                    'equipment_ids' => "Le matériel \"{$equipment->name}\" n'est pas disponible pour ces dates.",
                ]);
            }
        }

        // Check blocked dates
        $blockedDates = \App\Models\BlockedDate::where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->exists();

        if ($blockedDates && !$byAdmin) {
            throw ValidationException::withMessages([
                'start_date' => 'Ces dates incluent des jours de fermeture.',
            ]);
        }

        // Calculate pricing
        $subtotal = 0;
        $items = [];
        foreach ($data['equipment_ids'] as $equipmentId) {
            $equipment = Equipment::findOrFail($equipmentId);
            $itemSubtotal = $equipment->daily_rate * $daysCount;
            $subtotal += $itemSubtotal;
            $items[] = [
                'equipment_id' => $equipmentId,
                'daily_rate' => $equipment->daily_rate,
                'days_count' => $daysCount,
                'subtotal' => $itemSubtotal,
            ];
        }

        // Apply promo code
        $discountAmount = 0;
        $promoCodeId = null;
        if (!empty($data['promo_code'])) {
            $promo = PromoCode::where('code', strtoupper($data['promo_code']))->first();
            if ($promo && $promo->isValid()) {
                $discountAmount = $promo->calculateDiscount($subtotal);
                $promoCodeId = $promo->id;
                $promo->increment('used_count');
            }
        }

        $totalAmount = $subtotal - $discountAmount;
        $depositPercentage = (int) Setting::get('deposit_percentage', 30);
        $depositAmount = round($totalAmount * $depositPercentage / 100, 2);
        $balanceAmount = $totalAmount - $depositAmount;

        $reservation = Reservation::create([
            'client_id' => $data['client_id'],
            'start_date' => $startDate,
            'start_time' => $data['start_time'],
            'end_date' => $endDate,
            'end_time' => $data['end_time'],
            'status' => $byAdmin ? 'confirmed' : 'pending_payment',
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'promo_code_id' => $promoCodeId,
            'total_amount' => $totalAmount,
            'deposit_percentage' => $depositPercentage,
            'deposit_amount' => $depositAmount,
            'balance_amount' => $balanceAmount,
            'use_different_address' => $data['use_different_address'] ?? false,
            'use_address' => $data['use_address'] ?? null,
            'use_postal_code' => $data['use_postal_code'] ?? null,
            'use_city' => $data['use_city'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        foreach ($items as $item) {
            $reservation->items()->create($item);
        }

        return $reservation;
    }

    public function cancelReservation(Reservation $reservation, ?string $reason, bool $byAdmin): void
    {
        $canRefund = $reservation->canBeCancelledWithRefund();
        $status = ($canRefund || $byAdmin) ? 'cancelled' : 'cancelled_no_refund';

        $reservation->update([
            'status' => $status,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        if ($canRefund && $reservation->stripe_charge_id) {
            app(StripeService::class)->refundDeposit($reservation);
        }

        try {
            \Mail::to($reservation->client)->send(new \App\Mail\ReservationCancelled($reservation));
        } catch (\Exception $e) {
            \Log::error('Cancellation email failed: ' . $e->getMessage());
        }
    }

    public function getUnavailableDates(array $equipmentIds): array
    {
        $unavailable = [];

        // From reservations
        $reservations = \App\Models\ReservationItem::whereIn('equipment_id', $equipmentIds)
            ->whereHas('reservation', fn($q) => $q->whereNotIn('status', ['cancelled', 'cancelled_no_refund']))
            ->with('reservation:id,start_date,end_date')
            ->get();

        foreach ($reservations as $item) {
            $start = Carbon::parse($item->reservation->start_date);
            $end = Carbon::parse($item->reservation->end_date);
            $current = $start->copy();
            while ($current <= $end) {
                $unavailable[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        // From blocked dates
        $blocked = \App\Models\BlockedDate::all();
        foreach ($blocked as $block) {
            $current = $block->start_date->copy();
            while ($current <= $block->end_date) {
                $unavailable[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        return array_unique(array_values($unavailable));
    }
}
