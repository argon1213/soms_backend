<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Mail\PaymentInvoice;
use App\Mail\ExtendedPaymentInvoice;
use App\Mail\NotificationClientCreate;

use App\SomsClient;
use App\SomsOrder;
use App\SomsOrderItem;
use App\SomsOrderPayment;

use App\SomsStoragePeriod;
use App\SomsStoragePeriodItem;

use App\SomsItem;
use App\SomsItemPrice;
use App\SomsPaymentType;

use App\SomsCity;
use App\SomsState;
use App\SomsUniversity;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

use DB;
use Log;
use Mail;
use Auth;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class TestController extends Controller
{
  public function index(Request $request)
  {
      $items = SomsItem::where('type','product')->with('prices')->get();

      $deliveryService = SomsItem::where('type','service')->where('category','delivery')->first();

      $paymentTypes = SomsPaymentType::all();

      return view('index-success', compact('items','deliveryService','paymentTypes'));
  }

  public function email(Request $request, $id)
  {
    if($id != null)
    {
      $order = SomsOrder::find($id);
    }
    else
    {
      $client = SomsClient::where('email', env('MAIL_TO_ADDRESS'))->first();
      if($client){
        $order = SomsOrder::with('client','items')->where('client_id', $client->id)->latest()->first();
      }
      else {
        $order = SomsOrder::with('client','items')->latest()->first();
        $client = $order->client;
      }
    }

    $qrCodeUrl = $order->incompletePayment()->pay_qr_code;
    $qrCode = ($qrCodeUrl != null) ?QrCode::format('png')->size(200)->generate($qrCodeUrl):"";
    // $password = $this->password_filter($client->contact);
    return view('emails/extended-invoice-email-template', compact('order', 'qrCode'));
    // return view('emails/notification-client-create-template', compact('client','password'));
  }

  public function sendEmail(Request $request, $id)
  {
    if($id != null)
    {
      $order = SomsOrder::find($id);
    }
    else
    {
      $client = SomsClient::where('email', env('MAIL_TO_ADDRESS') )->first();
      if($client){
        $order = SomsOrder::with('client','items')->where('client_id', $client->id)->latest()->first();
      }
      else {
        $order = SomsOrder::with('client','items')->latest()->first();
        $client = $order->client;
      }
    }

    if($order)
    {
        try{

            Mail::to($order->client->email)->queue(new ExtendedPaymentInvoice($order));
            Log::debug('Payment Invoice successfully send with email : '.$order->client->email.' order code : '.$order->code);

        } catch (Exception $e) {
            return 'fail';
        }

        return 'success :'.$order->client->email;
    }

    return 'done';
  }
  // Test Example:
  // https://somsuat.ubox.com.hk/test/payment/160?payment_type_id=4
  public function payment(Request $request, $id)
  {
    // Get Payment By ID
    $payment = SomsOrderPayment::find($id);
    // Clear Payment Data
    $payment->paid_fee = 0;
    $payment->trans_id = null;
    $payment->pay_qr_code = null;
    $payment->payment_status_id = 1;
    // Reset Payment Method
    $payment->payment_type_id = $request->get('payment_type_id');
    $payment->save();
     // Create Stripe Token when credit card payment
     $stripeToken = null;
     if($payment->payment_type_id == 3){
       $stripeToken = \Stripe\Token::create([
         'card' => [
           'number' => '4242424242424242',
           'exp_month' => 12,
           'exp_year' => 2024,
           'cvc' => '999',
         ],
       ]);
     }
     //
     return PaymentController::processPayment($payment, $payment->order->client, $stripeToken, route('wechatpay-return'), route('alipay-return'));
  }

  public function testOrderQuery()
  {
    $requestBody = ['appid' => '1021563', 'sn' => '11202205240549271720589956042'];
    Log::debug("Request: ".json_encode($requestBody));
    $strSign = PaymentController::signData($requestBody);
    $requestBody['sign'] = $strSign;
    Log::debug("Sign: ".$strSign);
    $httpClient = new Client();
    $response = $httpClient->post('https://api.hk.blueoceanpay.com/order/query', [
        RequestOptions::JSON => $requestBody
    ]);

    return $response->getBody();
  }

  public function testOrderList()
  {
    $requestBody = ['appid' => '1021563'];
    Log::debug("Request: ".json_encode($requestBody));
    $strSign = PaymentController::signData($requestBody);
    $requestBody['sign'] = $strSign;
    Log::debug("Sign: ".$strSign);
    $httpClient = new Client();
    $response = $httpClient->post('https://api.hk.blueoceanpay.com/order/list', [
        RequestOptions::JSON => $requestBody
    ]);

    return $response->getBody();
  }

  public function orderSubmit()
  {
      // Use Client
      $client = SomsClient::find(76);
      // Use Product
      $productQtyArray = array(3 => 1, 4 => 2, 5 => 3);
      // Use Order
      $newOrder = new SomsOrder;
      $newOrder->client_id = $client->id;
      $newOrder = SomsOrder::prepareTestData($newOrder, $productQtyArray);
      $newOrder->save();
      //
      foreach ($productQtyArray as $id => $qty) {
        // echo $id.' : '.$qty;
        if($qty > 0){
          $newOrderItem = new SomsOrderItem;
          $newOrderItem->order_id = $newOrder->id;
          $newOrderItem->item_id = $id;
          $newOrderItem->item_qty = $qty;
          $newOrderItem->save();
        }
      }
      //
      return json_encode($client);
  }
}
