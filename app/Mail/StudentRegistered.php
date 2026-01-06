<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $shift;

    public function __construct($student, $shift)
    {
        $this->student = $student;
        $this->shift = $shift;
    }

    public function build()
    {
        // Obtener el usuario dueño del cubículo
        $user = \App\Models\User::find($this->shift->cubicle->user_id);

        return $this->subject('Confirmación de cita - Secretaria General APsU')
                    ->cc(optional($user)->email)  // ← COPIA AL USUARIO DEL CUBÍCULO
                    ->markdown('emails.student.registered')
                    ->with([
                        'student' => $this->student,
                        'shift' => $this->shift,
                    ]);
    }
}
