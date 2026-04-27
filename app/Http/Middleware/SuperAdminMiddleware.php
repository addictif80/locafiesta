<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
        return $next($request);
    }
}
