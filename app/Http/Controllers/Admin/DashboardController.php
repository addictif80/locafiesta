<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Invoice;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'reservations_today' => Reservation::whereDate('created_at', today())->count(),
            'reservations_month' => Reservation::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'active_reservations' => Reservation::whereIn('status', ['confirmed', 'in_progress'])->count(),
            'clients_count' => User::where('role', 'client')->count(),
            'revenue_month' => Invoice::where('status', 'paid')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
            'pending_returns' => Reservation::where('status', 'in_progress')->whereDate('end_date', '<=', today())->count(),
        ];

        $lateReservations = Reservation::with(['client', 'items.equipment'])
            ->where('status', 'in_progress')
            ->whereDate('end_date', '<', today())
            ->orderBy('end_date')
            ->get();

        $upcoming = Reservation::with(['client', 'items.equipment'])
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->whereDate('end_date', '>=', today())
            ->orderBy('start_date')
            ->limit(10)
            ->get();

        $recent_reservations = Reservation::with('client')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'upcoming', 'recent_reservations', 'lateReservations'));
    }
}
