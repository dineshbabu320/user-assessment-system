<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExamFailedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $exam;
    public $percentage;
    public $attempt_number;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $exam
     * @param $percentage
     * @param $attempts_number
     */
    public function __construct($user, $exam, $percentage, $attempt_number)
    {
        $this->user = $user;
        $this->exam = $exam;
        $this->percentage = $percentage;
        $this->attempt_number = $attempt_number;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Exam Failed Notification')
                    ->view('emails.exam_failed_notification')
                    ->with([
                        'user' => $this->user,
                        'exam' => $this->exam,
                        'score' => $this->percentage,
                        'attempt_number' => $this->attempt_number,
                    ]);
    }
}
