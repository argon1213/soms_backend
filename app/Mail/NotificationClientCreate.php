<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Log;

class NotificationClientCreate extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $locale;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client)
    {
      $this->client = $client;
      $this->locale = config('app.locale');
      $this->password = $this->password_filter($client->contact);
    }

    function password_filter($password){
      $rand = rand(0,2);
      for($i=$rand; $i<strlen($password); $i+=3)
      {
        $password = $this->replace_char($password, $i, '*');
      }
      return $password;
    }

    function replace_char($string, $position, $newchar) {
      if(strlen($string) <= $position) {
        return $string;
      }
      $string[$position] = $newchar;
      return $string;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::debug("before Mail Build ...");
        Log::debug($this->locale);
        config(['app.locale' => $this->locale]);
        return $this->from( env('MAIL_FROM_ADDRESS') )
                ->view('emails.notification-client-create-template')
                ->with([
                        'client' => $this->client,
                        'password' => $this->password
                    ]);
    }
}
