<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $announcement;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($announcement, $user)
    {
        $this->announcement = $announcement;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Novo Aviso: ' . $this->announcement->title;
        if ($this->announcement->priority) {
            $subject = '⚠️ URGENTE: ' . $this->announcement->title;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.announcement',
            with: [
                'announcement' => $this->announcement,
                'user' => $this->user,
                'url' => route('avisos.show', $this->announcement->id),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if ($this->announcement->attachment && Storage::disk('public')->exists($this->announcement->attachment)) {
            return [
                Attachment::fromPath(storage_path('app/public/' . $this->announcement->attachment))
            ];
        }
        return [];
    }
}
