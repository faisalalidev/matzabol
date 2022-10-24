<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $templateBody;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($templateBody, $subject)
    {
        $this->templateBody = $templateBody;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view('admin.email_template.template')->with([
            'body' => $this->templateBody['html'],
            //'body' => $this->templateBody['text']
        ]);
    }
}
