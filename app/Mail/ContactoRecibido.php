<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoRecibido extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $email,
        public ?string $asunto,
        public string $mensaje,
        public ?string $ip,
        public string $userAgent
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->asunto
            ? 'Contacto LevelBeats: ' . $this->asunto
            : 'Nuevo mensaje de contacto - LevelBeats';

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($this->email, $this->nombre)],
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contacto-recibido',
        );
    }
}
