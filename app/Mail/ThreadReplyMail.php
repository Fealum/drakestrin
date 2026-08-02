<?php

namespace App\Mail;

use App\Models\Board\Post;
use App\Models\Board\ThreadSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ThreadReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ThreadSubscription $subscription, public Post $post) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Neuer Beitrag: '.$this->post->thread->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.thread.reply');
    }
}
