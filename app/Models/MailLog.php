<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $fillable = [
        'to_email',
        'to_name',
        'subject',
        'mailable_class',
        'html_body',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Short label from the mailable class name (e.g. "ReservationContractMail").
     */
    public function getMailableLabel(): string
    {
        if (!$this->mailable_class) {
            return 'Mail';
        }
        return class_basename($this->mailable_class);
    }
}
