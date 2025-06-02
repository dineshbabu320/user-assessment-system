<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExamCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $title, $customMessage, $user, $status, $percentage;

    /**
     * Create a new message instance.
     *
     * @param $token
     */
    public function __construct($status, $percentage, $title, $customMessage)
    {
        $this->status = $status;
        $this->percentage = $percentage;
        $this->title = $title;
        $this->customMessage = $customMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.exam_completed_notification')
                    ->with([
                        'status' => $this->status,
                        'percentage' => $this->percentage,
                        'title' => $this->title,
                        'customMessage' => $this->customMessage,
                    ]);
    }
}
