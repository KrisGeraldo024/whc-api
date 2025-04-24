<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RecruiterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       $mail = $this->subject('New Application for '. $this->data['job_title'])
                     ->view('mail.recruiter-mail');
                     \Log::info($this->data['resume_path']);
                     if (isset($this->data['resume_path'])) {
                        $mail->attach(storage_path('app/public/' . $this->data['resume_path']));
                    }
                    
                    // Attach transcript if provided
                    if (isset($this->data['transcript_path'])) {
                        $mail->attach(storage_path('app/public/' . $this->data['transcript_path']));
                    }


        return $mail;
    }
}
