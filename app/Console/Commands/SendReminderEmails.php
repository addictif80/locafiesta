<?php

namespace App\Console\Commands;

use App\Mail\DepartureReminder;
use App\Mail\ReturnReminder;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReminderEmails extends Command
{
    protected $signature = 'reservations:send-reminders';
    protected $description = 'Send departure and return reminder emails';

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

        // Return reminders
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
    }
}
