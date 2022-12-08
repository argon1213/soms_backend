<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Log;

class PaymentInvoice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $locale;
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
      $this->order = $order;
      $this->locale = config('app.locale');
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
        Log::debug(config('mail.from.address'));
        Log::debug("before Mail Build 2...");

        return $this->from(config('mail.from.address'))
                ->view('emails.invoice-email-template')
                ->with([
                        'order' => $this->order
                    ]);
    }
}
