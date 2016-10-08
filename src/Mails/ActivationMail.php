<?php

namespace Rorikurn\Activator\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = Config::get('activator::model');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('activator::activation')
                    ->with(['user' => $this->user]);
    }
}