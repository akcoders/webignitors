<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminMailDiagnostic extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $administrator) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'WebIgnitors SMTP test successful');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.admin.mail-diagnostic');
    }
}
