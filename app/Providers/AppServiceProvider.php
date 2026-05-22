<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Single global composer with static cache — runs the DB query at most once per request.
        View::composer('*', function ($view) {
            static $shared = null;

            if ($shared !== null) {
                $view->with($shared);
                return;
            }

            $latePenaltyPerDay = (float) Setting::get('late_penalty_per_day', 0);

            $shared = [
                'adminLateReturnsCount'  => 0,
                'clientLateReservations' => collect(),
                'clientHasLateReturns'   => false,
                'latePenaltyPerDay'      => $latePenaltyPerDay,
            ];

            if (!auth()->check()) {
                $view->with($shared);
                return;
            }

            $user = auth()->user();

            if ($user->isAgent()) {
                $shared['adminLateReturnsCount'] = Reservation::where('status', 'in_progress')
                    ->whereDate('end_date', '<', today())
                    ->count();
            } elseif ($user->isClient()) {
                $late = $user->reservations()
                    ->where('status', 'in_progress')
                    ->whereDate('end_date', '<', today())
                    ->with('items.equipment')
                    ->get();
                $shared['clientLateReservations'] = $late;
                $shared['clientHasLateReturns']   = $late->isNotEmpty();
            }

            $view->with($shared);
        });
    }
}
