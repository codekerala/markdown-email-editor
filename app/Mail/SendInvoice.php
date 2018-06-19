<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Facades\Illuminate\Mail\Markdown;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = $data['subject'];
        $this->html = Markdown::parse($data['message']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->html((new CssToInlineStyles)->convert(
            view('email', ['html' => $this->html]),
            view('email-theme')
        ));
    }
}
