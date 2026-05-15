<?php

namespace App\Http\Controllers;

use App\Mail\ContactoRecibido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Controlador de formularios de contacto.
 *
 * Reutiliza el mismo envío SMTP para la página de contacto completa y el
 * formulario reducido de la Home, incorporando validación y honeypot antispam.
 */
class ContactoController extends Controller
{
    /**
     * Muestra la página pública de contacto.
     */
    public function index()
    {
        return view('contacto.index');
    }

    /**
     * Procesa el formulario corto de contacto disponible en la Home.
     */
    public function sendHome(Request $request)
    {
        $datos = $request->validate([
            'email' => 'required|email|max:160',
            'mensaje' => 'required|string|min:10|max:3000',
            'website' => 'nullable|string|max:255',
        ], [
            'email.required' => 'Indica tu correo electrónico.',
            'email.email' => 'Introduce un correo electrónico válido.',
            'mensaje.required' => 'Escribe tu mensaje.',
            'mensaje.min' => 'El mensaje debe tener al menos :min caracteres.',
            'mensaje.max' => 'El mensaje no puede superar los :max caracteres.',
        ]);

        if (!empty($datos['website'])) {
            return redirect(route('home.index') . '#contacto')
                ->with('status', 'Mensaje enviado correctamente.');
        }

        return $this->enviarMensajeContacto(
            request: $request,
            nombre: 'Visitante Home',
            email: $datos['email'],
            asunto: 'Contacto desde Home - LevelBeats',
            mensaje: $datos['mensaje'],
            successMessage: 'Mensaje enviado correctamente. Te responderemos lo antes posible.',
            errorContext: 'home',
            successRedirect: route('home.index') . '#contacto'
        );
    }

    /**
     * Procesa el formulario completo de contacto público.
     */
    public function send(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'asunto' => 'nullable|string|max:160',
            'mensaje' => 'required|string|min:10|max:3000',
            'privacidad' => 'accepted',
            'website' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'Indica tu nombre.',
            'email.required' => 'Indica tu correo electrónico.',
            'email.email' => 'Introduce un correo electrónico válido.',
            'mensaje.required' => 'Escribe tu mensaje.',
            'mensaje.min' => 'El mensaje debe tener al menos :min caracteres.',
            'mensaje.max' => 'El mensaje no puede superar los :max caracteres.',
            'privacidad.accepted' => 'Debes aceptar la política de privacidad.',
        ]);

        if (!empty($datos['website'])) {
            return back()->with('status', 'Mensaje enviado correctamente.');
        }

        return $this->enviarMensajeContacto(
            request: $request,
            nombre: $datos['nombre'],
            email: $datos['email'],
            asunto: $datos['asunto'] ?? null,
            mensaje: $datos['mensaje'],
            successMessage: 'Mensaje enviado correctamente. Te responderemos lo antes posible.'
        );
    }

    /**
     * Envía el correo de contacto y centraliza el tratamiento de errores SMTP.
     */
    private function enviarMensajeContacto(
        Request $request,
        string $nombre,
        string $email,
        ?string $asunto,
        string $mensaje,
        string $successMessage,
        string $errorContext = 'contacto',
        ?string $successRedirect = null
    ) {
        $destinatario = env('CONTACT_MAIL_TO', config('mail.from.address'));

        try {
            Mail::to($destinatario)->send(new ContactoRecibido(
                nombre: $nombre,
                email: $email,
                asunto: $asunto,
                mensaje: $mensaje,
                ip: $request->ip(),
                userAgent: (string) $request->userAgent()
            ));
        } catch (\Throwable $exception) {
            Log::error('Error enviando formulario de contacto.', [
                'error' => $exception->getMessage(),
                'email' => $email,
                'contexto' => $errorContext,
            ]);

            return back()
                ->withInput($request->except('website'))
                ->with('error', 'No hemos podido enviar el mensaje ahora mismo. Inténtalo de nuevo en unos minutos.');
        }

        if ($successRedirect) {
            return redirect($successRedirect)->with('status', $successMessage);
        }

        return back()->with('status', $successMessage);
    }
}
