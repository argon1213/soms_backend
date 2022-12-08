<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\SomsItem;
use App\SomsItemPrice;
use App\SomsPaymentType;
use App\SomsStoragePeriodItem;

use App\SomsCity;
use App\SomsState;
use App\SomsUniversity;

use App\SomsClient;

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

    public function orderSubmit(Request $request) {
        // For Debug
        // Log::debug($request->all());
        // return json_encode($request->all());
        // DB::beginTransaction();
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
              $newOrderItem->item_price = $newOrderItem->calcBestPrice(null, null);//$request->get('order_storage_period'), $request->get('input_promo_id'));
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
          return response()->json($result);   
        }
        catch(\Exception $e){
          Log::error($e);
          dd($e);
          $result['code'] = "failed";
          $result['message'] = $e->getMessage();

          // DB::rollback();
        //   return PaymentApiController::fail(null);
        }

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
      dd($result);

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
      // dd($storage_period_id);
      return response()->json(($somsStoragePeriodItems) ? $somsStoragePeriodItems : array());
    }
}
