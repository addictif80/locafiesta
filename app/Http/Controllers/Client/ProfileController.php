<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('client.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
        ]);

        $user->update($data + ['name' => $data['first_name'] . ' ' . $data['last_name']]);

        return back()->with('success', 'Profil mis à jour.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Mot de passe mis à jour.');
    }

    public function requestDeletion(Request $request)
    {
        auth()->user()->update(['deletion_requested_at' => now()]);
        return back()->with('success', 'Votre demande de suppression a été envoyée. Nous la traiterons sous 30 jours.');
    }
}
