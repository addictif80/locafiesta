<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date|before:-18 years',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'rgpd_consent' => 'accepted',
        ], [
            'first_name.required'   => 'Le prénom est obligatoire.',
            'last_name.required'    => 'Le nom est obligatoire.',
            'birth_date.required'   => 'La date de naissance est obligatoire.',
            'birth_date.before'     => 'Vous devez avoir au moins 18 ans.',
            'email.required'        => 'L\'adresse e-mail est obligatoire.',
            'email.email'           => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique'          => 'Cette adresse e-mail est déjà utilisée.',
            'password.required'     => 'Le mot de passe est obligatoire.',
            'password.min'          => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'    => 'La confirmation du mot de passe ne correspond pas.',
            'phone.required'        => 'Le numéro de téléphone est obligatoire.',
            'address.required'      => 'L\'adresse est obligatoire.',
            'postal_code.required'  => 'Le code postal est obligatoire.',
            'city.required'         => 'La ville est obligatoire.',
            'rgpd_consent.accepted' => 'Vous devez accepter la politique de confidentialité.',
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_date' => $request->birth_date,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'role' => 'client',
            'rgpd_consent' => true,
            'rgpd_consent_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('client.dashboard')->with('success', 'Bienvenue sur LocaFiesta ! Votre compte a été créé.');
    }
}
