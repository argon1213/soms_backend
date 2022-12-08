<?php

namespace App\Http\Controllers;

use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

use Log;
use Auth;
use Mail;
use Hash;
use Validator;

use App\Mail\PaymentInvoice;
use App\Mail\ExtendedPaymentInvoice;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

use App\SomsOrder;
use App\SomsPaymentType;
use App\SomsPaymentStatus;
use GuzzleHttp\Client;

class PaymentController extends Controller
{
    public function __construct()
    {

    }
    // Blue Ocean Pay Related Function
    public static function signData($data)
    {
        $key = "Wvo01OpUZbJyJX2HhWk5sCnE6Mk5TT18";

        $ignoreKeys = ['sign', 'key'];
        ksort($data);
        $signString = '';
        foreach ($data as $k => $v) {
            if (in_array($k, $ignoreKeys)) {
                unset($data[$k]);
                continue;
            }
            $signString .= "{$k}={$v}&";
        }
        $signString .= "key={$key}";
        return strtoupper(md5($signString));
    }
    // Payment Invoice Related Function
    private static function sendPaymentInvoice($order, $client)
    {
        // Just Send Email Notification
        try {
            Mail::to($client->email)->cc(env('MAIL_TO_ADDRESS'))->send(new ExtendedPaymentInvoice($order));

            Log::debug('Payment Invoice successfully send with email : ' . $client->email . ' order code : ' . $order->code);
        } catch (\Exception $e) {
            Log::error($e);

            Log::error('Payment Invoice cannot send with email : ' . $client->email . ' order code : ' . $order->code);
        }
    }

    private static function getOrderMetaData($payment, $client)
    {
        $orderMetaData = [
            "payment_code" => $payment->code,
            "payment_type" => $payment->payment_type_id,
            "email" => $client->email,
            "invoice" => $payment->order->code,
            "phone" => $client->contact,
        ];

        return $orderMetaData;
    }

    private static function getOrderOwner($client)
    {
        $orderOwner = [
            "phone" => $client->contact,
            "email" => $client->email
        ];

        return $orderOwner;
    }

    public static function processPayment($payment, $client, $stripeToken, $wechatpayReturnUrl, $alipayReturnUrl)
    {
        // Credit Card
        if ($payment->payment_type_id == SomsPaymentType::CREDIT_CARD) {
            return PaymentController::processCreditCardPayment($payment, $client, $stripeToken);
        } // Wechat Pay
        else if ($payment->payment_type_id == SomsPaymentType::WECHATPAY) {
            return PaymentController::processWechatpayPayment($payment, $client, $wechatpayReturnUrl);
        } // Alipay
        else if ($payment->payment_type_id == SomsPaymentType::ALIPAY) {
            return PaymentController::processAlipayPayment($payment, $client, $alipayReturnUrl);
        } // Cash
        else if ($payment->payment_type_id == SomsPaymentType::CASH) {
            return PaymentController::processCashPayment($payment, $client);
        }
    }
    // Updated
    public static function processCreditCardPayment($payment, $client, $stripeToken)
    {
        if (null === $stripeToken) {
            Log::error('Stripe Payment Error : Token is Missing.');
            return PaymentController::fail($payment, 'Token is Missing.');
        }
        // For Credit Card
        try {
            //
            Stripe::setApiKey(config('services.stripe')['secret']);

            $charge = Charge::create([
                "amount" => $payment->amount * 100,
                "currency" => "hkd",
                "source" => $stripeToken,
                "description" => "One-time charge from " . $client->email . "(" . $client->contact . ") for " . $payment->code,
                "metadata" => PaymentController::getOrderMetaData($payment, $client)
            ]);

            // Log::info(json_encode($charge));
            // Log::info('charge status : '.$charge['status'].'::: charge status : '.$charge->status);
            // Log::info('charge id : '.$charge['id'].'::: charge id : '.$charge->id);
            // Log::info('isset($charge) : '.isset($charge));

            if (isset($charge) && $charge->status == 'succeeded') {
                // Payment Success
                  // Update Payment
                  $payment->trans_id = $charge->id;
                  $payment->payment_status_id = SomsPaymentStatus::PAID;
                  $payment->paid_fee = $payment->amount;
                  $payment->save();

                  $order = $payment->order;
                  $order->paid_fee = $order->paid_fee + $payment->paid_fee;
                  $order->save();

                  PaymentController::sendPaymentInvoice($payment->order, $client);

                  return PaymentController::success($payment);
            } else {
                Log::error('Stripe Payment Error : Charge not succeeded');
                return PaymentController::fail($payment, 'Charge not succeeded');
            }

        } catch (\Exception $e) {
            Log::error('Stripe Payment Error : ' . $e->getMessage());
            return PaymentController::fail($payment, $e->getMessage());
        }
    }

    public static function processWechatpayPayment($payment, $client, $returnUrl)
    {
        try {
            $requestBody = ['appid' => '1021563', 'payment' => 'wechat.qrcode', 'total_fee' => $payment->amount * 100, 'notify_url' => $returnUrl, 'out_trade_no' => $payment->code];
            Log::debug("Request: ".json_encode($requestBody));

            $strSign = PaymentController::signData($requestBody);
            Log::debug("Request: ".$strSign);

            $requestBody['sign'] = $strSign;

            $httpClient = new Client();

            $response = $httpClient->post('https://api.hk.blueoceanpay.com/payment/pay', [
                RequestOptions::JSON => $requestBody // or 'json' => [...]
            ]);

            $content = json_decode($response->getBody()->getContents());

            Log::debug("Content : " . json_encode($content));

            // Save qr code to DB
            $payment->pay_qr_code = $content->data->qrcode;
            $payment->save();

            Log::debug("QRCODE: " . $content->data->qrcode);

            PaymentController::sendPaymentInvoice($payment->order, $client);

            return PaymentController::success($payment);
        } catch (\Exception $e) {
            Log::error('Wechat Payment Error : ' . $e->getMessage());
            return PaymentController::fail($payment, $e->getMessage());
        }
    }

    public static function processAlipayPayment($payment, $client, $returnUrl)
    {
        try {
            //
            Stripe::setApiKey(config('services.stripe')['secret']);

            $source = \Stripe\Source::create([
                "type" => "alipay",
                "amount" => $payment->amount * 100,
                "currency" => "hkd",
                "metadata" => PaymentController::getOrderMetaData($payment, $client),
                "owner" => PaymentController::getOrderOwner($client),
                "redirect" => [
                    // "return_url"=>route('alipay-return')
                    "return_url" => $returnUrl,
                ],
            ]);

            return PaymentController::success($payment);
        } catch (\Exception $e) {
            Log::error('Alipay Payment Error : ' . $e->getMessage());
            return PaymentController::fail($payment, $e->getMessage());
        }
    }

    public static function processCashPayment($payment, $client)
    {
        PaymentController::sendPaymentInvoice($payment->order, $client);
        return PaymentController::success($payment);
    }

    public function alipayReturn(Request $request)
    {
        Log::info('start alipayReturn - ' . $request->get('source'));
        //
        Stripe::setApiKey(config('services.stripe')['secret']);
        $source = \Stripe\Source::retrieve($request->get('source'));

        $payment = SomsOrderPayment::where('code', $source->metadata->payment_code)->first();

        try {

            if ($source->status == 'consumed' or $source->status == 'chargeable') {
                //
                $order = $payment->order;

                $payment->trans_id = $source->id;
                $payment->paid_fee = $payment->paid_fee + $source->amount;

                $order->paid_fee = $order->paid_fee + $source->amount;
                if($payment->amount == $payment->paid_fee){
                  $payment->payment_status_id = SomsPaymentStatus::PAID;
                }
                $payment->save();
                $order->save();

                PaymentController::sendPaymentInvoice($order, $order->client);

                return PaymentController::success($payment);
            } else {
                Log::error('Alipay Return Payment Invalid Status : ' . $source->status);
                return PaymentController::fail($payment, 'Invalid Status : '.$source->status);
            }
        } catch (\Exception $e) {
            Log::error('Alipay Return Payment Error : ' . $e->getMessage());
            return PaymentController::fail($payment, $e->getMessage());
        }
    }
    // No need return // Only update payment status
    public function wechatpayReturn(Request $request)
    {
      try {
        Log::info('start wechatpayReturn - ' . $request->get('out_trade_no'));

        $payment = SomsOrderPayment::where('code', $request->get('out_trade_no'))->first();
        $order = $payment->order;

        $payment->trans_id = $request->get('transaction_id');
        $payment->paid_fee = $payment->paid_fee + $request->get('pay_amount') / 100;

        $order->paid_fee = $order->paid_fee + $source->amount;
        if($payment->amount == $payment->paid_fee){
          $payment->payment_status_id = SomsPaymentStatus::PAID;
        }

        $payment->save();
        $order->save();
      } catch (\Exception $e) {
          Log::error('Wechat Return Payment Error : ' . $e->getMessage());
      }
    }
    // Auto Redirect After Payment Status is changed
    public function wechatpaySuccess(Request $request)
    {
        Log::info('start wechatpaySuccess');
        if ($request->filled('name') && strcmp($request->get('name'), 'index') == 0) {
            // Place Order Redirect
            return redirect()->route('index')->with('success', 'Payment via Wechatpay is successfully completed. Order No. :' . $request->get('code'));
        } else {
            return redirect()->route('somsclient.order.create')->with('success', 'Payment via Wechatpay is successfully completed. Order No. :' . $request->get('code'));
        }
    }

    public static function success($payment)
    {
      // From Admin Page
      if(Route::currentRouteName() == "orders.update")
        return PaymentController::successFromAdminUpdate($payment);
      // From Order Update
      else if(Route::currentRouteName() == "somsclient.order.update.submit")
        return PaymentController::successFromClientCreateOrUpdate($payment);
      // From Order Create
      else
        return PaymentController::successFromClientCreateOrUpdate($payment);
    }

    public static function successFromAdminUpdate($payment)
    {
        admin_toastr(__('成功更改訂單。'));
        return redirect('/admin/soms/order');
    }

    public static function successFromClientCreateOrUpdate($payment)
    {
      // Credit Card
      if ($payment->payment_type_id == SomsPaymentType::CREDIT_CARD) {
        $message = 'Payment is done successfully. Order No. :' . $payment->order->code;

        session()->flash('alert-class', 'alert-success');
        session()->flash('message', $message);
        session()->flash('success', $message);

        return redirect()->back();
      } // Wechat Pay
      else if ($payment->payment_type_id == SomsPaymentType::WECHATPAY) {
        $message = 'Please scan below qrcode for further payment process.';

        session()->flash('alert-class', 'alert-success');
        session()->flash('message', $message);
        session()->flash('success', $message);

        session()->flash('qr_code_url', $payment->qrcode);
        session()->flash('order_code', $payment->order->code);
        session()->flash('payment_code', $payment->code);

        return redirect()->back();
      } // Alipay
      else if ($payment->payment_type_id == SomsPaymentType::ALIPAY) {
        if($payment->payment_status_id == SomsPaymentStatus::PAID)
        {
          session()->flash('alert-class', 'alert-success');
          session()->flash('message', 'Payment via Alipay is successfully completed. Order No. :' . $payment->order->code);

          return redirect()->route('somsclient.dashboard');
        }
        else
        {
          return redirect()->away($source->redirect->url);
        }
      } // Cash
      else if ($payment->payment_type_id == SomsPaymentType::CASH) {
        $message = 'Please pay cash to our staff when boxes is checkined. Order No. :' . $payment->order->code;

        session()->flash('alert-class', 'alert-success');
        session()->flash('message', $message);
        session()->flash('success', $message);
        return redirect()->back();
      }
    }

    public static function fail($payment, $errorMessage = null)
    {
      $message = 'Payment is failed. Please contact customer service for further payment process. Order No. :' . (($payment != null)? $payment->order->code:"NULL") . '.';
      if($errorMessage != null)
          $message .= " Reason : ".$errorMessage;
      // From Admin Page
      if(Route::currentRouteName() == "orders.update")
      {
        admin_toastr(__('發生錯誤。'));
        return redirect('/admin/soms/order');
      }
      // From Order Update
      else if(Route::currentRouteName() == "somsclient.order.update.submit")
      {
        session()->flash('alert-class', 'alert-danger');
        session()->flash('message', $message);

        return redirect()->back();
      }
      else
      {
        return redirect()->back()->with('error', $message);
      }
    }
}
