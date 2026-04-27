<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAgent()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Accès refusé.'], 403);
            }
            abort(403, 'Accès réservé à l\'administration.');
        }

        if (auth()->user()->is_blacklisted) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Votre compte a été suspendu.');
        }

        return $next($request);
    }
}
