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
        return $this->subject('Confirmación de cita - Psicología Aplicada APsU')
                    ->markdown('emails.student.registered')
                    ->with([
                        'student' => $this->student,
                        'shift' => $this->shift,
                    ]);
    }
}
