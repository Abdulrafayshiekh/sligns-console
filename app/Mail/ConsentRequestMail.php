<?php

namespace App\Mail;

use App\Models\Reassignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reassignment $reassignment) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Your coach at :company is changing', ['company' => $this->reassignment->company->name]));
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.consent-request',
            with: ['url' => $this->reassignment->consentUrl()],
        );
    }
}
