<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $testData;

    public function __construct($data = [])
    {
        $this->testData = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Email de Prueba - Sistema de Turnos PUCE',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.test',
            with: [
                'data' => $this->testData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}