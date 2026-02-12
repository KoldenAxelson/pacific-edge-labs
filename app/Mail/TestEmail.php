<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Test email for verifying the email abstraction layer.
 *
 * Used to validate that the mailing system is properly configured
 * and capable of sending messages with the MAIL_* environment settings.
 */
class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;

    /**
     * Create a new message instance.
     *
     * @param string $userName The name of the recipient
     */
    public function __construct(string $userName)
    {
        $this->userName = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Email from Pacific Edge Labs',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.test',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
