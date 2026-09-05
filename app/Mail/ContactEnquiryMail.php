<?php

namespace App\Mail;

use App\Models\ContactEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactEnquiry $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New contact enquiry from '.$this->enquiry->name,
            replyTo: [$this->enquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-enquiry',
        );
    }
}
