<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityDeposit;
use Illuminate\Http\Request;

class SecurityDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = SecurityDeposit::with(['reservation.client', 'recordedBy'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('phase')) {
            $query->where('phase', $request->phase);
        }
        if ($request->filled('search')) {
            $query->whereHas('reservation', fn($q) => $q->where('reference', 'like', '%'.$request->search.'%'))
                  ->orWhere('holder_name', 'like', '%'.$request->search.'%')
                  ->orWhere('check_number', 'like', '%'.$request->search.'%');
        }

        $deposits = $query->paginate(20)->withQueryString();
        return view('admin.security-deposits.index', compact('deposits'));
    }

    public function updateStatus(Request $request, SecurityDeposit $deposit)
    {
        $request->validate([
            'status' => 'required|in:held,returned,cashed',
            'notes'  => 'nullable|string|max:500',
        ]);

        $deposit->update([
            'status'    => $request->status,
            'notes'     => $request->notes,
            'action_at' => in_array($request->status, ['returned', 'cashed']) ? now() : $deposit->action_at,
        ]);

        return back()->with('success', 'Statut de la caution mis à jour.');
    }
}
