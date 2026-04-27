<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    public function index()
    {
        $team = User::whereIn('role', ['admin', 'agent'])->latest()->get();
        return view('admin.team.index', compact('team'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,agent',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'rgpd_consent' => true,
            'rgpd_consent_at' => now(),
        ]);

        return back()->with('success', 'Membre d\'équipe créé.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->isAgent(), 404);

        $data = $request->validate([
            'role' => 'required|in:admin,agent',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
        ]);

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $user->update($data + ['name' => $data['first_name'] . ' ' . $data['last_name']]);
        return back()->with('success', 'Membre mis à jour.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->isAgent(), 404);
        abort_if($user->id === auth()->id(), 403, 'Vous ne pouvez pas supprimer votre propre compte.');
        $user->delete();
        return back()->with('success', 'Membre supprimé.');
    }
}
