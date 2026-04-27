<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Inspection;
use App\Models\InspectionItem;
use App\Models\DamageCharge;
use App\Services\InspectionService;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function __construct(private InspectionService $inspectionService) {}

    public function index(Request $request)
    {
        $query = Inspection::with(['reservation.client', 'admin'])
            ->latest('signed_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->whereHas('reservation', fn($q) => $q->where('reference', 'like', '%'.$request->search.'%'));
        }

        $inspections = $query->paginate(20)->withQueryString();
        return view('admin.inspections.index', compact('inspections'));
    }

    public function create(Reservation $reservation, string $type)
    {
        abort_unless(in_array($type, ['departure', 'return']), 404);

        if ($type === 'return') {
            abort_unless($reservation->departureInspection, 400, 'L\'état des lieux de départ doit être fait en premier.');
        }

        $reservation->load(['items.equipment.checklistItems.damageScaleItems', 'departureInspection.items']);
        return view('admin.inspections.create', compact('reservation', 'type'));
    }

    public function store(Request $request, Reservation $reservation, string $type)
    {
        abort_unless(in_array($type, ['departure', 'return']), 404);

        $request->validate([
            'general_notes' => 'nullable|string',
            'client_signature' => 'required|string',
            'admin_signature' => 'required|string',
            'items' => 'required|array',
            'items.*.checklist_item_id' => 'required|exists:checklist_items,id',
            'items.*.condition' => 'required|in:good,worn,damaged,missing',
            'items.*.notes' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:10240',
        ]);

        $inspection = $this->inspectionService->createInspection($reservation, $type, $request->all());

        if ($type === 'departure') {
            // Store security deposit info
            $request->validate([
                'deposit_holder_name' => 'required|string|max:255',
                'deposit_bank_name' => 'required|string|max:255',
                'deposit_bank_address' => 'required|string|max:255',
                'deposit_check_number' => 'required|string|max:100',
                'deposit_amount' => 'required|numeric|min:0',
            ]);

            $reservation->securityDeposits()->create([
                'phase' => 'departure',
                'holder_name' => $request->deposit_holder_name,
                'bank_name' => $request->deposit_bank_name,
                'bank_address' => $request->deposit_bank_address,
                'check_number' => $request->deposit_check_number,
                'amount' => $request->deposit_amount,
                'status' => 'held',
                'recorded_by' => auth()->id(),
            ]);

            $reservation->update(['status' => 'in_progress']);
        }

        if ($type === 'return') {
            $this->inspectionService->processDamageCharges($reservation, $inspection);

            $request->validate([
                'deposit_action' => 'required|in:return,cash_new',
            ]);

            if ($request->deposit_action === 'return') {
                $reservation->departureDeposit?->update(['status' => 'returned', 'action_at' => now()]);
            } else {
                $request->validate([
                    'new_holder_name' => 'required|string|max:255',
                    'new_bank_name' => 'required|string|max:255',
                    'new_bank_address' => 'required|string|max:255',
                    'new_check_number' => 'required|string|max:100',
                    'new_check_amount' => 'required|numeric|min:0',
                ]);

                $reservation->departureDeposit?->update(['status' => 'cashed', 'action_at' => now()]);

                $reservation->securityDeposits()->create([
                    'phase' => 'return',
                    'holder_name' => $request->new_holder_name,
                    'bank_name' => $request->new_bank_name,
                    'bank_address' => $request->new_bank_address,
                    'check_number' => $request->new_check_number,
                    'amount' => $request->new_check_amount,
                    'status' => 'held',
                    'recorded_by' => auth()->id(),
                ]);
            }

            $reservation->update(['status' => 'completed']);
        }

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'État des lieux enregistré avec succès.');
    }

    public function show(Inspection $inspection)
    {
        $inspection->load(['reservation.client', 'items.checklistItem', 'photos', 'admin']);
        return view('admin.inspections.show', compact('inspection'));
    }
}
