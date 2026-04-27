<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('Ces identifiants ne correspondent à aucun compte.'),
            ]);
        }

        $user = Auth::user();

        if ($user->is_blacklisted) {
            Auth::logout();
            return back()->withErrors(['email' => 'Votre compte a été suspendu.']);
        }

        $request->session()->regenerate();

        if ($user->isAgent()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('client.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
