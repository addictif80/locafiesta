<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Equipment;
use App\Models\User;
use App\Models\PromoCode;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservationService) {}

    public function index(Request $request)
    {
        $query = Reservation::with(['client', 'items.equipment']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', "%{$request->search}%")
                  ->orWhereHas('client', fn($q2) => $q2->where('first_name', 'like', "%{$request->search}%")->orWhere('last_name', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%"));
            });
        }
        if ($request->date_from) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $reservations = $query->latest()->paginate(20)->withQueryString();
        return view('admin.reservations.index', compact('reservations'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load([
            'client', 'items.equipment', 'promoCode',
            'inspections.items.checklistItem', 'inspections.photos',
            'securityDeposits', 'damageCharges', 'invoices',
        ]);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function create()
    {
        $clients = User::where('role', 'client')->where('is_blacklisted', false)->orderBy('last_name')->get();
        $equipment = Equipment::where('is_active', true)->get();
        return view('admin.reservations.create', compact('clients', 'equipment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:users,id',
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipment,id',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'promo_code' => 'nullable|string|exists:promo_codes,code',
            'use_different_address' => 'boolean',
            'use_address' => 'nullable|required_if:use_different_address,1|string|max:255',
            'use_postal_code' => 'nullable|required_if:use_different_address,1|string|max:10',
            'use_city' => 'nullable|required_if:use_different_address,1|string|max:100',
            'admin_notes' => 'nullable|string',
        ]);

        $reservation = $this->reservationService->createReservation($data, byAdmin: true);

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Réservation créée avec succès.');
    }

    public function edit(Reservation $reservation)
    {
        $clients = User::where('role', 'client')->orderBy('last_name')->get();
        $equipment = Equipment::where('is_active', true)->get();
        $reservation->load('items.equipment');
        return view('admin.reservations.edit', compact('reservation', 'clients', 'equipment'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'status' => 'required|in:pending_payment,confirmed,in_progress,completed,cancelled,cancelled_no_refund',
            'admin_notes' => 'nullable|string',
            'contract_message' => 'nullable|string',
            'use_different_address' => 'boolean',
            'use_address' => 'nullable|string|max:255',
            'use_postal_code' => 'nullable|string|max:10',
            'use_city' => 'nullable|string|max:100',
        ]);

        $reservation->update($data);

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Réservation mise à jour.');
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $this->reservationService->cancelReservation(
            $reservation,
            $request->reason,
            byAdmin: true
        );

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Réservation annulée.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->route('admin.reservations.index')->with('success', 'Réservation supprimée.');
    }
}
