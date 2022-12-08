<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Log;

class ExtendedPaymentInvoice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $locale;
    public $order;
    public $payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
      $this->order = $order;
      $this->payment = $order->incompletePayment();
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

        Log::debug($this->payment->pay_qr_code);
        $qrCode = ($this->payment->pay_qr_code != null)? QrCode::format('png')->size(200)->generate($this->payment->pay_qr_code):"";

        return $this->from(config('mail.from.address'))
                ->subject('Invoice - '.$this->order->code)
                ->view('emails.extended-invoice-email-template')
                ->with([
                        'order' => $this->order,
                        'qrCode' => $qrCode
                    ]);
    }
}
