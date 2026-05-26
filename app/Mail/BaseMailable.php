<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

/**
 * Base class for all LocaFiesta mailables.
 * Injects __mailableClass into the view data so the mail log listener
 * can identify which Mailable was used without relying on the MessageSent
 * event's (absent) mailable property.
 */
abstract class BaseMailable extends Mailable
{
    public function buildViewData(): array
    {
        return array_merge(parent::buildViewData(), [
            '__mailableClass' => static::class,
        ]);
    }
}
