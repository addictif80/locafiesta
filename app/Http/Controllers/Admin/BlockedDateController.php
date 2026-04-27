<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDate;
use Illuminate\Http\Request;

class BlockedDateController extends Controller
{
    public function index()
    {
        $blockedDates = BlockedDate::latest()->paginate(20);
        return view('admin.blocked-dates.index', compact('blockedDates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|gte:start_date',
            'type' => 'required|in:holiday,maintenance,closure,other',
            'reason' => 'nullable|string|max:255',
        ]);

        BlockedDate::create($request->only('start_date', 'end_date', 'type', 'reason') + [
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Blocage de dates ajouté.');
    }

    public function destroy(BlockedDate $blockedDate)
    {
        $blockedDate->delete();
        return back()->with('success', 'Blocage supprimé.');
    }

    public function getJson()
    {
        $blocked = BlockedDate::all()->map(fn($d) => [
            'start' => $d->start_date->format('Y-m-d'),
            'end' => $d->end_date->addDay()->format('Y-m-d'),
            'display' => 'background',
            'color' => '#ef4444',
            'title' => $d->reason ?? $d->type_label,
        ]);

        return response()->json($blocked);
    }
}
