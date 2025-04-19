<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExamPassedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $exam;
    public $score;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $exam
     * @param $score
     */
    public function __construct($user, $exam, $score)
    {
        $this->user = $user;
        $this->exam = $exam;
        $this->score = $score;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Exam Passed Notification')
                    ->view('emails.exam_passed_notification')
                    ->with([
                        'user' => $this->user,
                        'exam' => $this->exam,
                        'score' => $this->score,
                    ]);
    }
}
