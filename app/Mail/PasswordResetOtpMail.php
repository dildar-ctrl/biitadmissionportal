<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PasswordResetOtpMail extends Mailable
{
    public function __construct(
        public string $otp,
        public string $name,
        public int $expiryMinutes = 10
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'BIIT Admission Portal - Password Reset Code');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.password-reset-otp');
    }
}