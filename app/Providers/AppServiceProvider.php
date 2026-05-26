<?php

namespace App\Providers;

use App\Models\MailLog;
use App\Models\Reservation;
use App\Models\Setting;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Log every outgoing mail to the mail_logs table.
        Event::listen(MessageSent::class, function (MessageSent $event) {
            try {
                $to      = $event->message->getTo();
                $toAddr  = $to ? $to[0]->getAddress() : null;
                $toName  = ($to && $to[0]->getName()) ? $to[0]->getName() : null;

                MailLog::create([
                    'to_email'       => $toAddr,
                    'to_name'        => $toName,
                    'subject'        => $event->message->getSubject() ?? '(sans objet)',
                    'mailable_class' => $event->mailable ? get_class($event->mailable) : null,
                    'html_body'      => $event->message->getHtmlBody(),
                    'sent_at'        => now(),
                ]);
            } catch (\Throwable) {
                // Never let logging break a mail send.
            }
        });

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
