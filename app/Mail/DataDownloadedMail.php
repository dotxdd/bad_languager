<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DataDownloadedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $type;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $type)
    {
        $this->user = $user;
        $this->type = $type;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject('Pobrano dane dlr'. $this->type)
            ->view('emails.data_downloaded_mail');
    }
}
