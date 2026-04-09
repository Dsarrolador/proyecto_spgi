<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevaNotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mensaje;
    public $remitenteNombre;
    public $remitenteEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mensaje, $remitenteNombre, $remitenteEmail)
    {
        $this->mensaje = $mensaje;
        $this->remitenteNombre = $remitenteNombre;
        $this->remitenteEmail = $remitenteEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->replyTo($this->remitenteEmail, $this->remitenteNombre)
                    ->subject('Nueva Notificación de SPGI')
                    ->view('emails.nueva_notificacion');
    }
}
