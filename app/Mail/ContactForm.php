<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactForm extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Contact Form Submission';

        // Add subject prefix if provided
        if (!empty($this->data['subject'])) {
            $subjectOptions = [
                'general' => 'General Inquiry',
                'enrollment' => 'Course Enrollment',
                'support' => 'Technical Support',
                'licensing' => 'Licensing Questions',
                'partnership' => 'Partnership Opportunities',
                'other' => 'Other'
            ];

            $subjectText = $subjectOptions[$this->data['subject']] ?? ucfirst($this->data['subject']);
            $subject = "Contact Form: {$subjectText}";
        }

        return $this->from($this->data['email'], $this->data['name'])
            ->subject($subject)
            ->view('emails.contact-form')
            ->with('data', $this->data);
    }
}
