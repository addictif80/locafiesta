<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function start(User $client)
    {
        abort_unless(auth()->user()->isAgent(), 403);
        abort_unless($client->isClient(), 403);

        session(['impersonating_admin_id' => auth()->id()]);
        Auth::loginUsingId($client->id);

        return redirect()->route('client.dashboard')
            ->with('success', 'Vous naviguez en tant que ' . $client->full_name . '.');
    }

    public function stop()
    {
        $adminId = session('impersonating_admin_id');
        abort_unless($adminId !== null, 403);

        session()->forget('impersonating_admin_id');
        Auth::loginUsingId($adminId);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Retour à votre session administrateur.');
    }
}
