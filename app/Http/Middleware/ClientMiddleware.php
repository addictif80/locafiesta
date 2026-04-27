<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->is_blacklisted) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Votre compte a été suspendu. Contactez-nous pour plus d\'informations.');
        }

        if ($user->isAgent()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
