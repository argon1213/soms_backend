<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Log;
use Auth;
use Mail;
use Hash;
use Validator;

use App\Mail\PaymentInvoice;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

use App\SomsOrder;

class PaymentController extends Controller
{
  public function __construct()
  {

  }

  public static function getOrderMetaData($order, $client)
  {
    $orderMetaData = [
      "payment_type"  =>$order->payment_type_id,
      "email"         =>$client->email,
      "invoice"       =>$order->code,
      "phone"         =>$client->contact,
    ];

    return $orderMetaData;
  }

  public static function getOrderOwner($client)
  {
    $orderOwner = [
      "phone" => $client->contact,
      "email" => $client->email
    ];

    return $orderOwner;
  }

  public static function processPayment($order, $client, $stripeToken, $wechatpayReturnUrl, $alipayReturnUrl)
  {
    // Credit Card
    if($order->payment_type_id == 3)
    {
      return PaymentController::processCreditCardPayment($order, $client, $stripeToken);
    }
    // Wechat Pay
    else if($order->payment_type_id == 4)
    {
      return PaymentController::processWechatpayPayment($order, $client, $wechatpayReturnUrl);
    }
    // Alipay
    else if($order->payment_type_id == 5)
    {
      return PaymentController::processAlipayPayment($order, $client, $alipayReturnUrl);
    }
    else if($order->payment_type_id == 6)
    {
      return PaymentController::processCashPayment($order, $client);
    }
  }

  public static function processCreditCardPayment($order, $client, $stripeToken)
  {
    // if($request->filled('stripeToken')){
    //   $stripeToken = $request->get('stripeToken');
    // }
    // else{
    //   Log::error('Stripe Token is Missing.');
    //   return redirect()->back()->with('error', 'Stripe Token is Missing. Please contact customer service for further payment process. Order No. :'.$order->code);
    // }
    if(null === $stripeToken){
      Log::error('Stripe Token is Missing.');
      return redirect()->back()->with('error', 'Stripe Token is Missing. Please contact customer service for further payment process. Order No. :'.$order->code);
    }
    //
    Stripe::setApiKey(config('services.stripe')['secret']);
    // For Credit Card
    try{

        $charge = Charge::create([
          "amount" => $order->total_fee * 100,
          "currency" => "hkd",
          "source" => $stripeToken,
          "description" => "One-time charge from ".$client->email."(".$client->contact.") for ".$order->code,
          "metadata"=> PaymentController::getOrderMetaData($order, $client)
        ]);

        // Log::info(json_encode($charge));
        // Log::info('charge status : '.$charge['status'].'::: charge status : '.$charge->status);
        // Log::info('charge id : '.$charge['id'].'::: charge id : '.$charge->id);
        // Log::info('isset($charge) : '.isset($charge));

        if(isset($charge) && $charge->status == 'succeeded') {
          // Payment Success - Update Order Status
          $order->trans_id = $charge->id;
          $order->payment_status_id = 2;
          $order->paid_fee = $order->total_fee;
          $order->save();

          PaymentController::sendPaymentInvoice($order, $client);

        }else{
          Log::error('Stripe Payment Error : Charge Status not succeeded');
          return redirect()->back()->with('error', 'Payment is failed. Please contact customer service for further payment process. Order No. :'.$order->code);
        }

    } catch(Exception $e) {
      Log::error('Stripe Payment Error : '.$e->getMessage());
      return redirect()->back()->with('error', 'Payment is failed. Please contact customer service for further payment process. Order No. :'.$order->code);
    }

    return redirect()->back()->with('success', 'Payment is done successfully. Order No. :'.$order->code);
  }

  public static function processWechatpayPayment($order, $client, $returnUrl)
  {
    try{
        //
        Stripe::setApiKey(config('services.stripe')['secret']);

        $source = \Stripe\Source::create([
          "type" => "wechat",
          "amount" => $order->total_fee * 100,
          "currency" => "hkd",
          "metadata"=> PaymentController::getOrderMetaData($order, $client),
          "owner" => PaymentController::getOrderOwner($client),
          "redirect"=> [
            // "return_url"=>route('wechatpay-return')
            "return_url"=>$returnUrl
          ],
        ]);

        // $qrParser = new chillerlan\QRCode\QRCode();
        // $data = $qrParser->render($source->wechat->qr_code_url);

        session()->flash('success', 'Please scan below qrcode for further payment process.');
        session()->flash('qr_code_url', $source->wechat->qr_code_url);
        session()->flash('order_code', $order->code);

        return redirect()->back();
      }
      catch(Exception $e)
      {
        Log::error('Wechat Payment Error : '.$e->getMessage());
        return redirect()->back()->with('error', 'Payment is failed. Please contact customer service for further payment process. Order No. :'.$order->code);
      }
  }

  public static function processAlipayPayment($order, $client, $returnUrl)
  {
    try{
        //
        Stripe::setApiKey(config('services.stripe')['secret']);

        $source = \Stripe\Source::create([
          "type" => "alipay",
          "amount" => $order->total_fee * 100,
          "currency" => "hkd",
          "metadata"=> PaymentController::getOrderMetaData($order, $client),
          "owner" => PaymentController::getOrderOwner($client),
          "redirect"=> [
            // "return_url"=>route('alipay-return')
            "return_url"=>$returnUrl,
          ],
        ]);

        return redirect()->away($source->redirect->url);
      }
      catch(Exception $e)
      {
        Log::error('Alipay Payment Error : '.$e->getMessage());
        return redirect()->back()->with('error', 'Payment is failed. Please contact customer service for further payment process. Order No. :'.$order->code);
      }
  }

  public static function processCashPayment($order, $client)
  {
    PaymentController::sendPaymentInvoice($order, $client);
    $lang = __('common.Please pay cash to our staff when boxes is checkined. Order No.');
    return redirect()->back()->with('success', ' :'.$order->code);
  }

  public function alipayReturn(Request $request)
  {
    try {
        //
        Stripe::setApiKey(config('services.stripe')['secret']);

        $source = \Stripe\Source::retrieve($request->get('source'));

        if($source->status == 'consumed' or $source->status == 'chargeable')
        {
          //
          $currOrder = SomsOrder::where('code', $source->metadata->invoice)->first();
          $currOrder->payment_status_id = 2;
          $currOrder->trans_id = $source->id;
          $currOrder->paid_fee = $currOrder->total_fee;
          $currOrder->save();

          PaymentController::sendPaymentInvoice($currOrder, $currOrder->client);
          $langMsg = __msg('common.Payment via Alipay is successfully completed. Order No.');
          return redirect()->back()->with('success', "$langMsg :".$currOrder->code);
          // return 'Payment via Alipay is successfully completed. Check stripe dashboard. <br/><br/>'.json_encode($source);
        }
        else
        {
          return redirect()->back()->with('error', 'Payment via Alipay is failed. Please contact customer service for further payment process. Order No. :');
          // return 'Payment via Alipay is not successfully completed.';
        }

      } catch(\Stripe\Error\Card $e) {
        // Since it's a decline, \Stripe\Error\Card will be caught
        $body = $e->getJsonBody();
        $err  = $body['error'];

        print('Block Type 1:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
        print('Type is:' . $err['type'] . "<br>\n");
        print('Code is:' . $err['code'] . "<br>\n");
        // param is '' in this case
        print('Param is:' . $err['param'] . "<br>\n");
        print('Message is:' . $err['message'] . "<br>\n");

      } catch (\Stripe\Error\RateLimit $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];

        print('Block Type 2:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
        print('Type is:' . $err['type'] . "<br>\n");
        print('Code is:' . $err['code'] . "<br>\n");
        print('Param is:' . $err['param'] . "<br>\n");
        print('Message is:' . $err['message'] . "<br>\n");
      } catch (\Stripe\Error\InvalidRequest $e) {
        // Invalid parameters were supplied to Stripe's API
        $body = $e->getJsonBody();
        $err  = $body['error'];
        echo'<pre>';print_r($e);echo'</pre>';
        print('Block Type 3:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
        print('Type is:' . $err['type'] . "<br>\n");
        print('Code is:' . $err['code'] . "<br>\n");
        // param is '' in this case
        print('Param is:' . $err['param'] . "<br>\n");
        print('Message is:' . $err['message'] . "<br>\n");

      } catch (\Stripe\Error\Authentication $e) {
        // Authentication with Stripe's API failed
        // (maybe you changed API keys recently)

        $body = $e->getJsonBody();
        $err  = $body['error'];

        print('Block Type 4:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
        print('Type is:' . $err['type'] . "<br>\n");
        print('Code is:' . $err['code'] . "<br>\n");
        // param is '' in this case
        print('Param is:' . $err['param'] . "<br>\n");
        print('Message is:' . $err['message'] . "<br>\n");

      } catch (\Stripe\Error\ApiConnection $e) {
        // Network communication with Stripe failed

        $body = $e->getJsonBody();
        $err  = $body['error'];

        print('Block Type 5:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
        print('Type is:' . $err['type'] . "<br>\n");
        print('Code is:' . $err['code'] . "<br>\n");
        // param is '' in this case
        print('Param is:' . $err['param'] . "<br>\n");
        print('Message is:' . $err['message'] . "<br>\n");

      } catch (\Stripe\Error\Base $e) {
        // Display a very generic error to the user, and maybe send
        // yourself an email
        $body = $e->getJsonBody();
        $err  = $body['error'];

        print('Block Type 6:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
        print('Type is:' . $err['type'] . "<br>\n");
        print('Code is:' . $err['code'] . "<br>\n");
        // param is '' in this case
        print('Param is:' . $err['param'] . "<br>\n");
        print('Message is:' . $err['message'] . "<br>\n");

      } catch (Exception $e) {
        // Something else happened, completely unrelated to Stripe

        $body = $e->getJsonBody();
        $err  = $body['error'];

        print('Block Type 7:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
        print('Type is:' . $err['type'] . "<br>\n");
        print('Code is:' . $err['code'] . "<br>\n");
        // param is '' in this case
        print('Param is:' . $err['param'] . "<br>\n");
        print('Message is:' . $err['message'] . "<br>\n");
    }
  }

  public function wechatpayReturn(Request $request)
  {
    Log::info('start wechatpayReturn - '.$request->get('type'));
    // $source = $request->get('data')['object'];
    // return $source['metadata']['invoice'];
    // return json_encode($request->get('data')['object']);
    // $input = $request->all();
    // return json_encode($input);
    try {
      //
      Stripe::setApiKey(config('services.stripe')['secret']);
      $eventId = $request->get('id');
      $eventType = $request->get('type');
      $source = $request->get('data')['object'];
      $sourceType = $source['type'];
      if('wechat' == $sourceType){
        $source = \Stripe\Source::retrieve($source['id']);
        // return $source['metadata']['invoice'];
        // return $source->metadata->invoice;

        if($eventType == 'source.chargeable')
        {
          //
          $charge = \Stripe\Charge::create([
            'amount' => $source->amount,
            'currency' => 'hkd',
            "source" => $source->id,
          ]);

          if(isset($charge) and $charge->status == 'succeeded') {
            // Payment Success - Update Order Status
            $currOrder = SomsOrder::where('code', $source->metadata->invoice)->first();
            $currOrder->payment_status_id = 2;
            $currOrder->trans_id = $charge->id;
            $currOrder->paid_fee = $currOrder->total_fee;
            $currOrder->save();

            PaymentController::sendPaymentInvoice($currOrder, $currOrder->client);
          }
        }
        else if($eventType == 'source.failed')
        {

        }
        else if($eventType == 'source.canceled')
        {
          // Payment Success - Update Order Status
          $currOrder = SomsOrder::where('code', $source->metadata->invoice)->first();
          $currOrder->payment_status_id = 3;
          $currOrder->trans_id = $eventId;
          $currOrder->save();
        }
      }
      else{
        Log::info('ignore non-wechat payment - now is '.$sourceType);
      }

    } catch(\Stripe\Error\Card $e) {
      // Since it's a decline, \Stripe\Error\Card will be caught
      $body = $e->getJsonBody();
      $err  = $body['error'];

      print('Block Type 1:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
      print('Type is:' . $err['type'] . "<br>\n");
      print('Code is:' . $err['code'] . "<br>\n");
      // param is '' in this case
      print('Param is:' . $err['param'] . "<br>\n");
      print('Message is:' . $err['message'] . "<br>\n");

    } catch (\Stripe\Error\RateLimit $e) {
      $body = $e->getJsonBody();
      $err  = $body['error'];

      print('Block Type 2:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
      print('Type is:' . $err['type'] . "<br>\n");
      print('Code is:' . $err['code'] . "<br>\n");
      print('Param is:' . $err['param'] . "<br>\n");
      print('Message is:' . $err['message'] . "<br>\n");
    } catch (\Stripe\Error\InvalidRequest $e) {
      // Invalid parameters were supplied to Stripe's API
      $body = $e->getJsonBody();
      $err  = $body['error'];
      echo'<pre>';print_r($e);echo'</pre>';
      print('Block Type 3:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
      print('Type is:' . $err['type'] . "<br>\n");
      print('Code is:' . $err['code'] . "<br>\n");
      // param is '' in this case
      print('Param is:' . $err['param'] . "<br>\n");
      print('Message is:' . $err['message'] . "<br>\n");

    } catch (\Stripe\Error\Authentication $e) {
      // Authentication with Stripe's API failed
      // (maybe you changed API keys recently)

      $body = $e->getJsonBody();
      $err  = $body['error'];

      print('Block Type 4:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
      print('Type is:' . $err['type'] . "<br>\n");
      print('Code is:' . $err['code'] . "<br>\n");
      // param is '' in this case
      print('Param is:' . $err['param'] . "<br>\n");
      print('Message is:' . $err['message'] . "<br>\n");

    } catch (\Stripe\Error\ApiConnection $e) {
      // Network communication with Stripe failed

      $body = $e->getJsonBody();
      $err  = $body['error'];

      print('Block Type 5:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
      print('Type is:' . $err['type'] . "<br>\n");
      print('Code is:' . $err['code'] . "<br>\n");
      // param is '' in this case
      print('Param is:' . $err['param'] . "<br>\n");
      print('Message is:' . $err['message'] . "<br>\n");

    } catch (\Stripe\Error\Base $e) {
      // Display a very generic error to the user, and maybe send
      // yourself an email
      $body = $e->getJsonBody();
      $err  = $body['error'];

      print('Block Type 6:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
      print('Type is:' . $err['type'] . "<br>\n");
      print('Code is:' . $err['code'] . "<br>\n");
      // param is '' in this case
      print('Param is:' . $err['param'] . "<br>\n");
      print('Message is:' . $err['message'] . "<br>\n");

    } catch (Exception $e) {
      // Something else happened, completely unrelated to Stripe

      $body = $e->getJsonBody();
      $err  = $body['error'];

      print('Block Type 7:<br> Status is:' . $e->getHttpStatus() . "<br>\n");
      print('Type is:' . $err['type'] . "<br>\n");
      print('Code is:' . $err['code'] . "<br>\n");
      // param is '' in this case
      print('Param is:' . $err['param'] . "<br>\n");
      print('Message is:' . $err['message'] . "<br>\n");
    }
  }

  public function wechatpaySuccess(Request $request)
  {
    Log::info('start wechatpaySuccess');
    if($request->filled('name') && strcmp($request->get('name'), 'index') == 0){
      // Place Order Redirect
      return redirect()->route('index')->with('success', 'Payment via Wechatpay is successfully completed. Order No. :'.$request->get('code'));
    }
    else{
      return redirect()->route('somsclient.order.create')->with('success', 'Payment via Wechatpay is successfully completed. Order No. :'.$request->get('code'));
    }
  }

  private static function sendPaymentInvoice($order, $client)
  {
    // Just Send Email Notification
    try{
      Mail::to($client->email)->cc( env('MAIL_TO_ADDRESS') )->send(new PaymentInvoice($order));

      Log::debug('Payment Invoice successfully send with email : '.$client->email.' order code : '.$order->code);
    }
    catch(\Exception $e){
      Log::error($e);

      Log::error('Payment Invoice cannot send with email : '.$client->email.' order code : '.$order->code);
    }
  }

  public static function fail(){

  }

  public static function success(){

  }
}
