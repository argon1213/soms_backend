<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use App\OtpMsg;

use App\SomsItem;
use App\SomsItemPrice;
use App\SomsPaymentType;
use App\SomsStoragePeriodItem;

use App\SomsCity;
use App\SomsState;
use App\SomsUniversity;

use App\SomsClient;

use App\SomsPromotion;
use App\SomsOrder;
use App\SomsOrderItem;
use App\SomsOrderPayment;

use App\Http\Controllers\MainController;
use App\Http\Controllers\PaymentApiController;
use Auth;
use Log;
use Mail;

use Yedpay\Client;
use Yedpay\Response\Success;
use Yedpay\Response\Error;

class ApiController extends Controller
{
  const STAGING = 'staging';
  const PRODUCTION = 'production';
    //
    public function getProducts(Request $request) {
        $storingItems = SomsItem::where('type','product')->where('category','box')->get();

        $materailItems = SomsItem::where('type','product')->where('category','bag')->get();

        // $deliveryService = SomsItem::where('type','service')->where('category','delivery')->first();

        $paymentTypes = SomsPaymentType::all();

        $cities = SomsCity::all();
        $states = SomsState::all();
        $universities = SomsUniversity::where('id', '<>', 0)->get();

        
        $result['store_items'] = $storingItems;
        $result['material_items'] = $materailItems;
        $result['cities'] = $cities;
        $result['states'] = $states;
        $result['universities'] = $universities;

        return response()->json($result);
    }

    public function login(Request $request) {
        $username = $request->get('email');
        $password = $request->get('password');

        $credentials = $request->only('email', 'password');
        if (Auth::guard('somsclient')->attempt($credentials)) {
            // login success
            $result['status'] = "success";
            $result['user'] = Auth::guard('somsclient')->user();
            return response()->json($result);
        }
        // Login fail
        $result['status'] = "error";
        $result['message'] = "Password or Email is not correct.";
        return response()->json($result);
    }
    
    public function register(Request $request) {

        $email = $request->get('email');
        $noUnique = SomsClient::where('email', $email)->first();

        if($noUnique) {
          $error['status'] = "error";
          $error['message'] = "The email is exist.";
          return response()->json($error);
        }

        $somsClient = new SomsClient;
        $somsClient->name = $request->get('name');
        $somsClient->email = $request->get('email');
        $somsClient->contact = $request->get('contact');
        $somsClient->password = bcrypt($request->get('password'));
        $somsClient->address1 = $request->get('address');
        $university = $request->get('university');
        if($university) {
          $somsClient->university_id = $university["id"];
        }
        $somsClient->student_id = $request->get('studentID');
        $somsClient->wechat = $request->get('wechatID');
        //
        $somsClient->save();

        Auth::guard('somsclient')->login($somsClient);     
        $result['status'] = "success";
        $result['user'] = Auth::guard('somsclient')->user();
        return response()->json($result);
    }

    public function logout(Request $request) {
        Auth::guard('somsclient')->logout();
        $result['status'] = "success";
        return response()->json($result);   
    }

    public function sendEmailOtp(Request $request){
  
      $email = $request->get('email');
      $user = SomsClient::where('email', $email)->first();
      if($user){
          $otp = rand(1111,99999);
          $otpData = new OtpMsg;
          $otpData->otp = $otp;
          $otpData->email_id = $email;
          $otpData->save();
          // $this->sendOtpEmail($otp, $user);
          return response()->json([
            'status' => 'success',
            'message' => 'OTP sent on your email, please check'],
            200
          );
      }else{
          return response()->json([
            'status' => 'success',
            'message' => 'OTP sent on your email, please check'],
            404
          );
      }
    }

    public function sendOtpEmail($otp, $user){
      $email = $user->email;
      $maildata=array(
          'name'=>$user->name,
          'email'=>$email,
          'otp'=>$otp,
      );
      Mail::send('emails.otp', $maildata, function ($message) use ($email)
      {
          $message->from( env('MAIL_FROM_ADDRESS') , 'ubox');
          $message->subject('OTP for reset password - ubox');
          $message->to($email);
      });
  }

    public function resetPassword(Request $request){
    
      $email = $request->email;
      $otp = $request->otp;
      $otpData = OtpMsg::where(['email_id'=>$email, 'otp'=>$otp, 'status'=>0])->first();
      if($otpData){
          $user = SomsClient::where('email', $email)->first();
          if($user){
              $user->password = Hash::make($request->password);
              $user->save();

              $otpData->status = 1;
              $otpData->save();

              return response()->json(['success'=>true,'msg'=>'Password changed'], 200);
          }
          return response()->json(['error'=>true,'msg'=>'Something wrong'], 404);

      }else{
          return response()->json(['error'=>true,'msg'=>'Something wrong'], 404);
      }
    }

    public function fetchUser(Request $request) {
      $userId = $request->get('id');
      $client = SomsClient::find($userId);
      return response()->json($client);
    }

    public function promotionCodeValidate(Request $request)
    {
        Log::debug($request->get('promotion_code'));
        $now = Carbon::now();

        // $somsPromotion = SomsPromotion::with('items')->where('code', $request->get('promotion_code'))->first();

        $somsPromotion = SomsPromotion::with('items')->where('code', $request->get('promotion_code'))->whereDate('effective_from', '<=', $now->toDateString())->whereDate('effective_to', '>=', $now->toDateString())->first();
        if ($somsPromotion->exists()) {
            Log::debug('exist');
            // return $somsPromotion->first();
            return response()->json([
              'status' => 'success',
              'data' => $somsPromotion,
            ], 200);
        } else {
            Log::debug('not found');
            return response()->json([
              'status' => 'error',
              'message' => 'Promo Code is invalid',
            ], 403);
        }
    }

    public function orderSubmit(Request $request) {
     
        $data = request()->all();
        try{
          $newClient = MainController::createOrUpdateClient4JSON($data);

          // Create Order
          $newOrder = new SomsOrder;
          $newOrder->client_id = $newClient->id;
          $newOrder = SomsOrder::prepareCreateDataNew4JSON($newOrder, $data);
          $newOrder->save();

          // Create Order Payment
          $newPayment = new SomsOrderPayment;
          $newPayment->order_id = $newOrder->id;
          $newPayment->amount = $data['carts']['total'];
          $newPayment->payment_type_id = $data['carts']['payment_type'];
          $newPayment->save();

          $productQtyArray = $data['carts']['stores'];
          foreach ($productQtyArray as $id => $item) {
            if($item AND $item['count'] > 0){
              $newOrderItem = new SomsOrderItem;
              $newOrderItem->order_id = $newOrder->id;
              $newOrderItem->item_id = $id;
              $newOrderItem->item_qty = $item['count'];
              // $newOrderItem->item_price = $newOrderItem->calcBestPrice(null, null);//$request->get('order_storage_period'), $request->get('input_promo_id'));
              $newOrderItem->item_price = $item['price'];
              $newOrderItem->save();
            }
          }
          $productQtyArray = $data['carts']['materials'];
          foreach ($productQtyArray as $id => $item) {
            if($item AND $item['count'] > 0){
              $newOrderItem = new SomsOrderItem;
              $newOrderItem->order_id = $newOrder->id;
              $newOrderItem->item_id = $id;
              $newOrderItem->item_qty = $item['count'];
              $newOrderItem->item_price = $newOrderItem->calcBestPrice(null, null);//$request->get('order_storage_period'), $request->get('input_promo_id'));
              $newOrderItem->save();
            }
          }
          // DB::commit();
          $result = PaymentApiController::processPayment($newPayment, $newClient, $data['stripeToken'], route('wechatpay-return'), route('alipay-return'));
          $result['order'] = $newPayment->order;
          return response()->json($result);   
        }
        catch(\Exception $e){
          Log::error($e);
          // dd($e);
          // $result['code'] = "failed";
          // $result['message'] = $e->getMessage();

          // DB::rollback();
          $result = PaymentApiController::fail($newPayment, null);
          return response()->json($result); 
        }
        $result['order'] = $newPayment->order;
        return response()->json($result);   
    }

    public function yedpayOrderSubmit(Request $request)
    {
      $data = request()->all();

      $apiKey = config('services.yedpay')['apiKey'];
      echo($apiKey);

      $storeId = '8X4LZW2XLG9V';
      $amount = 1.0;  

      $client = new Client(static::STAGING, $apiKey);
      //changing transaction currency (default: HKD)
      $client->setCurrency(Client::INDEX_CURRENCY_HKD);
      //changing alipay wallet type (default: HK)CN
      $client->setWallet(Client::INDEX_WALLET_HK);

      // with extra parameters
      // $result = $client->precreate(
      //         $storeId, 
      //         $amount, 
      // )->getData();

      $result = $client->precreate($storeId, $amount,);

      $customId = 'test-001';
      $amount = 1.0;
      $currency = 1;
      $notifyUrl = 'https://www.example.com/notify';
      $returnUrl = 'https://www.example.com/return';

      // $client = new Client(Library::STAGING, $apiKey, false);


      return response()->json($data);
    }

    public function getStoragePeriodItem(Request $request)
    {
      $month = $request->get('months');
      $storage_period_id = 1; //default value
      if ($month > 2 && $month <6)
        $storage_period_id = 1;
      // else if ($month >= 9)
      //   $storage_period_id = 3;
      else if ($month >= 6)
        $storage_period_id = 2;
      $somsStoragePeriodItems = SomsStoragePeriodItem::where('storage_period_id', $storage_period_id)->whereNotNull('price')->pluck('price', 'item_id')->all();
      return response()->json(($somsStoragePeriodItems) ? $somsStoragePeriodItems : array());
    }

    public function clientUpdate(Request $request)
    {
      // For Security Check
      $client = SomsClient::find($request->get('id'));
      // Update Process
      $client->name = $request->get('name');
      // $client->email = $request->get('email');
      $client->wechat = $request->get('wechat');
      $client->contact = $request->get('contact');
      $client->student_id = $request->get('student_id');
      // $client->address1 = $request->get('address1');
      // $client->city_id = $request->get('city_id');
      // $client->state_id = $request->get('state_id');
      if($request->filled('password_new')){
        $client->password = Hash::make($request->get('password_new'));
      }
      //
      $client->save();
      $client = SomsClient::find($request->get('id'));
      return response()->json($client);
    }

    public function ChangePassword(Request $request) {
      $currentPassword = $request->get('password');
      $newPassword = $request->get('password_new');
      $client = SomsClient::find($request->get('id'));

      $credentials = $request->only('email', 'password');
        if (Auth::guard('somsclient')->attempt($credentials)) {
          // success
          $client->password =bcrypt($newPassword);
          $client->save();
          $result['status'] = "success";
          $result['message'] = "Password was changed sucessfully.";
          return response()->json($result);
        } else {
          $result['status'] = "error";
          $result['message'] = "Current password is incorrect.";
          return response()->json($result);
        }
    }

    public function getOrders(Request $request)
    {
      // $somsClient = Auth::guard('somsclient')->user();
      // $orders = SomsOrder::where('client_id', $request->get('id'))->where('current_version', 1)->orderBy('updated_at','desc');
      $label = $request->get('label');
      $offset = $request->get('offset');
      $limit = $request->get('limit');
      $page = $request->get('page');
      if($label == "init") {
        $orders = SomsOrder::with('items')->where('client_id', $request->get('client_id'))->orderBy('updated_at','desc')->offset($offset)->limit($limit)->get();
      } else {
        $orders = SomsOrder::with('items')->where('client_id', $request->get('client_id'))->orderBy('code', $label)->offset($offset)->limit($limit)->get();
      }
      $currentOrders['orders'] = $orders;
      $currentOrders['page'] = $page;
      return response()->json($currentOrders);
    }

    public function fetchCurrentOrder(Request $request)
    {
      $id = $request->get('id');
      $order = SomsOrder::with('items', 'payments')->find($id);
      return response()->json($order);
    }

    public function updateOrder(Request $request)
    {
      // For Security Check
      $orderCode = $request->get('code');
      $id = $request->get('id');
      $orderCheck = SomsOrder::where('code', $orderCode)->where('current_version', 1)->first();
      $order = SomsOrder::with('client','items')->find($id);

      if($orderCheck == null || $order == null || $order->id != $orderCheck->id){
        
      }

      $order->emptyout_date_other = $request->get('emptyout_date_other');
      $order->emptyout_time_other = $request->get('emptyout_time_other');
      $order->checkin_date_other = $request->get('checkin_date_other');
      $order->checkin_time_other = $request->get('checkin_time_other');
      $order->checkout_date_other = $request->get('checkout_date_other');
      $order->checkout_time_other = $request->get('checkout_time_other');


      if($order->getStorageMonth() > $order->storage_month)
      {
        $order->storage_month = $order->getStorageMonth();
        $order->total_fee = $order->getMonthlyFee() * $order->getStorageMonth() + $order->getOtherFee() + $order->delivery_service_fee;
      }

      $order->version = $order->version + 1;
      $order->current_version = 1;
      // $order->save();
      // Update Current Order as a old version
      // $order->current_version = 0;
      // $order->save();

      // Create New Order Item From Old Order

      // foreach ($order->items as $item) {
      //   $Item->order_id = $order->id;
      // }
      // Don't copy payment // Move to new version order
      foreach ($order->payments as $payment) {
        $payment->order_id = $order->id;
      }


      // Check if need new payment
      $unpaid_amount = $order->total_fee - $order->paid_fee;

      if($unpaid_amount > 0 && $order->isNeedCreateNewPayment())
      {
        // Create new Payment
        $newPayment = new SomsOrderPayment;
        $newPayment->order_id = $order->id;
        $newPayment->amount = $unpaid_amount;
        $newPayment->payment_type_id = SomsPaymentType::CASH;
        $newPayment->save();

        PaymentController::processPayment($newPayment, $order->client, null, route('wechatpay-return'), route('alipay-return'));
      }

      $order->save();

      $client = SomsClient::find($request->get('client_id'));
      $order = SomsOrder::with('items', 'payments')->find($id);
      $orders = SomsOrder::with('items')->where('client_id', $request->get('client_id'))->orderBy('updated_at','desc')->offset(0)->limit(10)->get();
      $result['currentOrder'] = $order;
      $result['client'] = $client;
      $result['orders'] = $orders;
      return response()->json($result);

    }
}
