<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ManagerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $address;
    private $name;
    public $subject;

    public function __construct($content, $name, $subject)
    {
        $table = DB::table('Manager')->where('Id', 1)->first();
        $this->with('content', $content);
        $this->address = $table->Email;
        $this->name = $name;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Email.index')
            ->from($this->address, $this->name)
            //->cc($this->address, $this->name)
            //->bcc($this->address, $this->name)
            ->replyTo($this->address, $this->name)
            ->subject($this->subject);
    }
}
