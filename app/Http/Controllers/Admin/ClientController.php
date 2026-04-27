<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'client')->withCount('reservations');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->filter === 'blacklisted') {
            $query->where('is_blacklisted', true);
        }

        $clients = $query->latest()->paginate(20)->withQueryString();
        return view('admin.clients.index', compact('clients'));
    }

    public function show(User $client)
    {
        abort_unless($client->isClient(), 404);
        $client->load(['reservations.items.equipment', 'invoices']);
        return view('admin.clients.show', compact('client'));
    }

    public function edit(User $client)
    {
        abort_unless($client->isClient(), 404);
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        abort_unless($client->isClient(), 404);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'email' => 'required|email|unique:users,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
        ]);

        $client->update($data + ['name' => $data['first_name'] . ' ' . $data['last_name']]);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client mis à jour.');
    }

    public function toggleBlacklist(Request $request, User $client)
    {
        abort_unless($client->isClient(), 404);

        $request->validate(['reason' => 'nullable|string|max:500']);

        $client->update([
            'is_blacklisted' => !$client->is_blacklisted,
            'blacklist_reason' => $client->is_blacklisted ? null : $request->reason,
        ]);

        $msg = $client->is_blacklisted ? 'Client mis sur liste noire.' : 'Client retiré de la liste noire.';
        return back()->with('success', $msg);
    }

    public function destroy(User $client)
    {
        abort_unless($client->isClient(), 404);
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client supprimé.');
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
        ]);

        $client = User::create($data + [
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'role' => 'client',
            'password' => Hash::make(str()->random(16)),
            'rgpd_consent' => true,
            'rgpd_consent_at' => now(),
        ]);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client créé.');
    }
}
