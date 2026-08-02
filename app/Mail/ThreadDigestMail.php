<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ThreadDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $recipient, public Collection $entries, public string $period) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->period === 'daily' ? 'Tägliche Themenübersicht' : 'Wöchentliche Themenübersicht');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.thread.digest');
    }
}
