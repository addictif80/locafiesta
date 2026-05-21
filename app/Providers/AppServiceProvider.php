<?php

namespace App\Providers;

use App\Models\Reservation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Inject late return count into every admin page (sidebar badge)
        View::composer('layouts.admin', function ($view) {
            $count = 0;
            if (auth()->check()) {
                $count = Reservation::where('status', 'in_progress')
                    ->whereDate('end_date', '<', today())
                    ->count();
            }
            $view->with('adminLateReturnsCount', $count);
        });

        // Inject late reservations into every client page (warning banner)
        View::composer('layouts.client', function ($view) {
            $late = collect();
            if (auth()->check() && auth()->user()->role === 'client') {
                $late = auth()->user()->reservations()
                    ->where('status', 'in_progress')
                    ->whereDate('end_date', '<', today())
                    ->with('items.equipment')
                    ->get();
            }
            $view->with('clientLateReservations', $late);
        });
    }
}
