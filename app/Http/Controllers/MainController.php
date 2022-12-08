<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Mail\PaymentInvoice;
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

class MainController extends Controller
{
    public function index(Request $request)
    {
        $items = SomsItem::where('type','product')->where('category','box')->get();

        $oneTimeItems = SomsItem::where('type','product')->where('category','bag')->get();

        // $deliveryService = SomsItem::where('type','service')->where('category','delivery')->first();

        $paymentTypes = SomsPaymentType::all();

        $storagePeriods = SomsStoragePeriod::all();

        $cities = SomsCity::all();
        $states = SomsState::all();
        $universities = SomsUniversity::where('id', '<>', 0)->get();

        return view('index-new', compact('items','oneTimeItems','paymentTypes','storagePeriods','cities','states','universities'));
    }

    private static function createOrUpdateClient($request)
    {
      $isCreate = false;

      if($request->somsclient_id != null)
      {
        // Check user is login
        Log::debug('is somsclient login : '.Auth::guard('somsclient')->check());
        if(Auth::guard('somsclient')->check())
        {
          // Check user id is match from input
          Log::debug('security check user id : '.Auth::guard('somsclient')->user()->id.' ::: '.$request->get('somsclient_id'));
          if(Auth::guard('somsclient')->user()->id == $request->somsclient_id)
          {
            // Update Exist User
            $client = SomsClient::find(Auth::guard('somsclient')->user()->id);
          }
        }
      }
      else
      {
          // Create New User
          $client = new SomsClient;
          $client->name = $request->name;
          $client->email = $request->email;
          $client->password = Hash::make($request->contact);

          $isCreate = true;
      }
      // 1. Remove mobile_phone_cn
      // 2. Remove mobile_phone_hk
      // 3. Add contact
      $client->contact = $request->contact;
      // 1. Remove address 2
      // 2. Add city_id
      // 3. Add state_id
      $client->address1 = $request->address;
      $client->city_id = $request->city;
      $client->state_id = $request->state;

      if($request->university_student == 1)
      {
        $client->university_id = $request->university_id;
        $client->student_id = $request->student_id;
        $client->wechat = $request->wechat_id;
      }

      $client->save();

      if($isCreate)
      {
        // Send Client Create Information
        try{
          Mail::to($client->email)->cc( env('MAIL_TO_ADDRESS') )->send(new NotificationClientCreate($client));

          Log::debug('Client Create Notification successfully send with email : '.$client->email.' client name : '.$client->name);
        }
        catch(\Exception $e){
          Log::error($e);

          Log::error('Client Create Notification cannot send with email : '.$client->email.' client name : '.$client->name);
        }        
      }

      return $client;
    }

    public static function createOrUpdateClient4JSON($data)
    {
      $isCreate = false;
      if($data['somsclient_id'] != null)
      {
        // Check user is login
        Log::debug('is somsclient login : '.Auth::guard('somsclient')->check());
        $client = SomsClient::find($data['somsclient_id']);

        // if(Auth::guard('somsclient')->check())
        // {
        //   // Check user id is match from input
        //   Log::debug('security check user id : '.Auth::guard('somsclient')->user()->id.' ::: '.$data['somsclient_id']);
        //   if(Auth::guard('somsclient')->user()->id == $data['somsclient_id'])
        //   {
        //     // Update Exist User
        //     $client = SomsClient::find(Auth::guard('somsclient')->user()->id);
        //   }
        // }
      }
      else
      {
          // Create New User
          $client = new SomsClient;
          $client->name = $data['stuff']['name'];
          $client->email = $data['stuff']['email'];
          $client->password = Hash::make($data['stuff']['contact']);

          $isCreate = true;
      }
      // 1. Remove mobile_phone_cn
      // 2. Remove mobile_phone_hk
      // 3. Add contact
      $client->contact = $data['stuff']['contact'];
      // 1. Remove address 2
      // 2. Add city_id
      // 3. Add state_id
      $client->address1 = $data['stuff']['address'];
      // $client->city_id = $data['stuff']['city'];
      // $client->state_id = $data['stuff']['state'];

      if($data['account']['isStudent'] == 1)
      {
        $client->university_id = $data['account']['university']['id'];
        $client->student_id = $data['account']['studentID'];
        $client->wechat = $data['account']['wechatID'];
      }

      $client->save();

      if($isCreate)
      {
        // Send Client Create Information
        try{
          Mail::to($client->email)->cc( env('MAIL_TO_ADDRESS') )->send(new NotificationClientCreate($client));

          Log::debug('Client Create Notification successfully send with email : '.$client->email.' client name : '.$client->name);
        }
        catch(\Exception $e){
          Log::error($e);

          Log::error('Client Create Notification cannot send with email : '.$client->email.' client name : '.$client->name);
        }        
      }

      return $client;
    }

    public function orderSubmit(Request $request)
    {
        // For Debug
        // Log::debug($request->all());
        // return json_encode($request->all());
        // DB::beginTransaction();

        try{

          $newClient = MainController::createOrUpdateClient($request);
          // Create Order
          $newOrder = new SomsOrder;
          $newOrder->client_id = $newClient->id;
          $newOrder = SomsOrder::prepareCreateDataNew($newOrder, $request);
          $newOrder->save();
          // Create Order Payment
          $newPayment = new SomsOrderPayment;
          $newPayment->order_id = $newOrder->id;
          $newPayment->amount = $request->get('total-fee');
          $newPayment->payment_type_id = $request->get('order_payment_type');
          $newPayment->save();

          $productQtyArray = $request->get('product-qty');
          foreach ($productQtyArray as $id => $qty) {
            // echo $id.' : '.$qty;
            if($qty > 0){
              $newOrderItem = new SomsOrderItem;
              $newOrderItem->order_id = $newOrder->id;
              $newOrderItem->item_id = $id;
              $newOrderItem->item_qty = $qty;
              $newOrderItem->item_price = $newOrderItem->calcBestPrice($request->get('order_storage_period'), $request->get('input_promo_id'));
              $newOrderItem->save();
            }
          }
          // DB::commit();
          return PaymentController::processPayment($newPayment, $newClient, $request->get('stripeToken'), route('wechatpay-return'), route('alipay-return'));
        }
        catch(\Exception $e){
          Log::error($e);
          // DB::rollback();
          return PaymentController::fail(null);
        }
    }

    /* Obsolete Function - Since Index Have New Function
    public function index(Request $request)
    {
        $items = SomsItem::where('type','product')->get();

        $deliveryService = SomsItem::where('type','service')->where('category','delivery')->first();

        $paymentTypes = SomsPaymentType::all();

        return view('index', compact('items','deliveryService','paymentTypes'));
    }
    */
}
