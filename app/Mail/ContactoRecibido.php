<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable del formulario de contacto público.
 *
 * Construye el asunto, remitente configurado y reply-to del visitante para
 * enviar consultas a la cuenta corporativa de LevelBeats.
 */
class ContactoRecibido extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Recibe los datos validados del formulario de contacto.
     */
    public function __construct(
        public string $nombre,
        public string $email,
        public ?string $asunto,
        public string $mensaje,
        public ?string $ip,
        public string $userAgent
    ) {
    }

    /**
     * Define remitente, reply-to y asunto del correo.
     */
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

    /**
     * Indica la vista Blade usada como cuerpo del email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contacto-recibido',
        );
    }
}
