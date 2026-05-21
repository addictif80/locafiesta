<?php

namespace App\Console\Commands;

use App\Mail\DepartureReminder;
use App\Mail\LateReturnAlert;
use App\Mail\ReturnReminder;
use App\Models\Reservation;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReminderEmails extends Command
{
    protected $signature = 'reservations:send-reminders';
    protected $description = 'Send departure/return reminder emails and late return alerts';

    public function handle(): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Departure reminders
        $departures = Reservation::where('start_date', $tomorrow)
            ->where('status', 'confirmed')
            ->with('client')
            ->get();

        foreach ($departures as $reservation) {
            try {
                Mail::to($reservation->client)->send(new DepartureReminder($reservation));
                $this->info("Departure reminder sent for {$reservation->reference}");
            } catch (\Exception $e) {
                $this->error("Failed for {$reservation->reference}: " . $e->getMessage());
            }
        }

        // Return reminders (day before)
        $returns = Reservation::where('end_date', $tomorrow)
            ->where('status', 'in_progress')
            ->with('client')
            ->get();

        foreach ($returns as $reservation) {
            try {
                Mail::to($reservation->client)->send(new ReturnReminder($reservation));
                $this->info("Return reminder sent for {$reservation->reference}");
            } catch (\Exception $e) {
                $this->error("Failed for {$reservation->reference}: " . $e->getMessage());
            }
        }

        // Late return alerts (end_date strictly past and not yet notified)
        $lateReturns = Reservation::where('status', 'in_progress')
            ->whereDate('end_date', '<', today())
            ->whereNull('late_notified_at')
            ->with(['client', 'items.equipment'])
            ->get();

        $adminEmail = Setting::get('company_email');

        foreach ($lateReturns as $reservation) {
            try {
                Mail::to($reservation->client)->send(new LateReturnAlert($reservation, isAdmin: false));
                $this->info("Late return alert (client) sent for {$reservation->reference}");
            } catch (\Exception $e) {
                $this->error("Late alert (client) failed for {$reservation->reference}: " . $e->getMessage());
            }

            if ($adminEmail) {
                try {
                    Mail::to($adminEmail)->send(new LateReturnAlert($reservation, isAdmin: true));
                    $this->info("Late return alert (admin) sent for {$reservation->reference}");
                } catch (\Exception $e) {
                    $this->error("Late alert (admin) failed for {$reservation->reference}: " . $e->getMessage());
                }
            }

            $reservation->update(['late_notified_at' => now()]);
        }

        $this->info("Done. Late returns notified: {$lateReturns->count()}");
    }
}
