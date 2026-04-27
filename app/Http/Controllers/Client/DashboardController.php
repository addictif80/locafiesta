<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $reservations = $user->reservations()
            ->with('items.equipment')
            ->latest()
            ->paginate(10);

        return view('client.dashboard', compact('reservations'));
    }
}
