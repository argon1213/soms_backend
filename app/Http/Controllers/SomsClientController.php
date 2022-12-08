<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

use Carbon\Carbon;

use Log;
use Auth;
use Mail;
use Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Validator;

use App\Mail\PaymentInvoice;

use App\SomsClient;
use App\SomsOrder;
use App\SomsOrderItem;
use App\SomsOrderPayment;

use App\SomsEventLog;
use App\SomsEventType;

use App\SomsItem;
use App\SomsItemPrice;

use App\SomsLocation;
use App\SomsDatetime;

use App\SomsPaymentStatus;
use App\SomsPaymentType;

use App\SomsCity;
use App\SomsState;

class SomsClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:somsclient');
        // $this->middleware('auth:somsclient', ['only' => 'index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $somsClient = Auth::guard('somsclient')->user();
      $orders = SomsOrder::with('client')->where('client_id', $somsClient->id)->where('current_version', 1)->orderBy('updated_at','desc');
      $orders = $orders->paginate(10);

      return view('somsclient/dashboard', compact('orders'));
    }

    private function securityCheck($order, $request)
    {
      if($order == null || $order->client == null){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
      if($order->client->id != Auth::guard('somsclient')->user()->id){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
    }

    public function orderUpdateNew(Request $request, $id)
    {
      $order = SomsOrder::with('client','items')->find($id);

      $cities = SomsCity::all();
      $states = SomsState::all();
      //
      $this->securityCheck($order, $request);
      //
      return view('somsclient/order-update-new', compact('order','cities','states'));
    }

    private function prepareChangeLog(Request $request, $order, $change, $type)
    {
      $change_title_suffix_array = [
        '_location_other', '_city_id', '_state_id', '_date_other', '_time_other'
      ];

      foreach ($change_title_suffix_array as $change_title_suffix) {
        $change_title = $type.$change_title_suffix;

        if($request->get($change_title) != $order->{$change_title}){
          $change[$change_title] = array();
          $change[$change_title]['from'] = $order->{$change_title};
          $change[$change_title]['to'] = $request->get($change_title);
        }
      }
      return $change;
    }

    private function addChangeLog($change, $fromOrder, $toOrder, $change_title_array)
    {
        foreach ($change_title_array as $change_title) {
          $change[$change_title] = array();
          $change[$change_title]['from'] = $fromOrder->{$change_title};
          $change[$change_title]['to'] = $toOrder->{$change_title};
        }

        return $change;
    }

    public function orderUpdateSubmitNew(Request $request, $id)
    {
      // For Security Check
      $orderCode = $request->get('code');
      $orderCheck = SomsOrder::where('code', $orderCode)->where('current_version', 1)->first();
      //
      $order = SomsOrder::with('client','items')->find($id);

      if($orderCheck == null || $order == null || $order->id != $orderCheck->id){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
      if($order->client->id != Auth::guard('somsclient')->user()->id){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
      // Validation
      $validator = Validator::make($request->all(), [
          'emptyout_date_other' => ['before_or_equal:checkin_date_other',
            function ($attribute, $value, $fail) use ($order) {
                $existDate = Carbon::createFromFormat('Y-m-d', $order->emptyout_date_other);
                $newDate = Carbon::createFromFormat('Y-m-d', $value);

                if (!$existDate->eq($newDate) && $newDate->lt(Carbon::today()->addDays(2))) {
                    $fail('空箱日期最早只可更改為兩日後。');
                }
            },
          ],
          'checkin_date_other' => ['before:checkout_date_other',
            function ($attribute, $value, $fail) use ($order) {
                $existDate = Carbon::createFromFormat('Y-m-d', $order->checkin_date_other);
                $newDate = Carbon::createFromFormat('Y-m-d', $value);

                if (!$existDate->eq($newDate) && $newDate->lt(Carbon::today()->addDays(2))) {
                    $fail('存箱日期最早只可更改為兩日後。');
                }
            },
          ],
          'checkout_date_other' => [
            function ($attribute, $value, $fail) use ($order) {
                // $existDate = Carbon::createFromFormat('Y-m-d', $order->checkout_date_other);
                $newDate = Carbon::createFromFormat('Y-m-d', $value);

                if ($newDate->lt(Carbon::today()->addDays(2))) {
                    $fail('取箱日期最早只可更改為兩日後。');
                }
            },
          ],
      ],[
        'emptyout_date_other.before_or_equal' => '空箱日期必須在存箱日期之前或一樣',
        'checkin_date_other.before' => '存箱日期必須在取箱日期之前',
      ]);

      if ($validator->fails()) {
          return redirect()->back()
                      ->withErrors($validator)
                      ->withInput();
      }

      // Check Change for event log
      $change = array();

      foreach (SomsOrder::MOVEMENT_PREFIX_ARRAY as $order_movement_prefix) {
        $change = $this->prepareChangeLog($request, $order, $change, $order_movement_prefix);
      }

      if(sizeof($change) == 0){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '沒有任何更改!');
        return redirect()->back();
      }
      // Process Update
      // Create a Duplicate Row without ID
      $newOrder = $order->replicate();

      foreach (SomsOrder::MOVEMENT_PREFIX_ARRAY as $order_movement_prefix) {
        foreach (SomsOrder::MOVEMENT_SUFFIX_ARRAY as $order_movement_suffix) {
          $newOrder->{$order_movement_prefix.$order_movement_suffix} = $request->{$order_movement_prefix.$order_movement_suffix};
        }
      }
      // Calculate if storage month is changed
      if($newOrder->getStorageMonth() > $order->storage_month)
      {
        $newOrder->storage_month = $newOrder->getStorageMonth();
        $newOrder->total_fee = $newOrder->getMonthlyFee() * $newOrder->getStorageMonth() + $newOrder->getOtherFee() + $newOrder->delivery_service_fee;

        $change = $this->addChangeLog($change, $order, $newOrder, array('storage_month', 'total_fee'));
      }

      $newOrder->version = $order->version + 1;
      $newOrder->current_version = 1;
      $newOrder->save();
      // Update Current Order as a old version
      $order->current_version = 0;
      $order->save();
      // Create New Order Item From Old Order
      foreach ($order->items as $item) {
        $newItem = $item->replicate();
        $newItem->order_id = $newOrder->id;
        $newItem->save();
      }
      // Don't copy payment // Move to new version order
      foreach ($order->payments as $payment) {
        $payment->order_id = $newOrder->id;
        $payment->save();
      }
      // Check if need new payment
      $unpaid_amount = $newOrder->total_fee - $newOrder->paid_fee;
      if($unpaid_amount > 0 && $newOrder->isNeedCreateNewPayment())
      {
        // Create new Payment
        $newPayment = new SomsOrderPayment;
        $newPayment->order_id = $newOrder->id;
        $newPayment->amount = $unpaid_amount;
        $newPayment->payment_type_id = SomsPaymentType::CASH;
        $newPayment->save();

        PaymentController::processPayment($newPayment, $newOrder->client, null, route('wechatpay-return'), route('alipay-return'));
      }

      // Create Event Log
      $newEventLog = new SomsEventLog;
      $newEventLog->event_type_id = SomsEventType::where('code', 'update_order')->first()->id;
      $newEventLog->target_id = $newOrder->id;
      $newEventLog->from_target_id = $order->id;
      $newEventLog->json_remark = json_encode($change);
      $newEventLog->save();
      // return json_encode($order).':'.json_encode($order->replicate());
      // return json_encode($request->input());
      // return view('somsclient/order-update', compact('order'));
      $request->session()->flash('alert-class', 'alert-success');
      $request->session()->flash('message', '成功更新訂單!');

      return redirect()->route('somsclient.order.update', ['id' => $newOrder->id]);
    }

    public function clientUpdateNew()
    {
      $client = Auth::guard('somsclient')->user();

      $cities = SomsCity::all();
      $states = SomsState::all();

      return view('somsclient/client-update-new', compact('client','cities','states'));
    }

    public function clientUpdateSubmitNew(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'wechat' => 'required|max:255',
          'contact' => 'required|max:255|nullable|regex:/^[1-9][0-9]{7,12}$/',
          'address1' => 'required|max:255',
          'city_id' => 'required',
          'state_id' => 'required',
          'password_new' => 'nullable|string|min:8|confirmed',
          // 'email' => [
          //     'required', 'max:255',
          //       Rule::unique('soms_clients', 'email')->ignore($request->get('id'))
          // ],
          'password' => [
              'required_with:password_new', 'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value != '' && !Hash::check($value, Auth::guard('somsclient')->user()->password)) {
                        $fail($attribute.' is invalid.');
                    }
                },
          ],
      ]);

      if ($validator->fails()) {
          return redirect()->back()
                      ->withErrors($validator)
                      ->withInput();
      }
      // For Security Check
      $clientCheck = SomsClient::find($request->get('id'));
      $client = Auth::guard('somsclient')->user();

      if($clientCheck->id != $client->id){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
      // Update Process
      // $client->email = $request->get('email');
      $client->wechat = $request->get('wechat');
      $client->contact = $request->get('contact');
      $client->address1 = $request->get('address1');
      $client->city_id = $request->get('city_id');
      $client->state_id = $request->get('state_id');
      if($request->filled('password_new')){
        $client->password = Hash::make($request->get('password_new'));
      }
      //
      if($client->isDirty()){
        // Check Change for event log
        $change = array();
        $changeFields = $client->getDirty();
        foreach ($changeFields as $field => $value) {
          $change[$field]['from'] = $client->getOriginal($field);
          $change[$field]['to'] = $value;
        }
        //
        $client->save();
        // Create Event Log
        $newEventLog = new SomsEventLog;
        $newEventLog->event_type_id = SomsEventType::where('code', 'update_client')->first()->id;
        $newEventLog->target_id = $client->id;
        $newEventLog->json_remark = json_encode($change);
        $newEventLog->save();
        //
        $request->session()->flash('alert-class', 'alert-success');
        $request->session()->flash('message', '成功更改個人資料!');
        return redirect()->back();
      }
      else{
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '沒有任何更改!');
        return redirect()->back();
      }
    }

    public function sendPaymentInvoice(Request $request, $id)
    {
      $order = SomsOrder::with('client','items')->find($id);
      //
      $this->securityCheck($order, $request);
      //
      try{
        Mail::to($order->client->email)->cc( env('MAIL_TO_ADDRESS') )->send(new PaymentInvoice($order));

        Log::debug('Payment Invoice successfully send with email : '.$order->client->email.' order code : '.$order->code);
        //
        $request->session()->flash('alert-class', 'alert-success');
        $request->session()->flash('message', '成功發送訂單! 請到閣下郵箱查看!');
        //
        return redirect()->back();
      }
      catch(\Exception $e){

          Log::error($e);
        Log::error('Payment Invoice cannot send with email : '.$order->client->email.' order code : '.$order->code);
      }
      //
      $request->session()->flash('alert-class', 'alert-danger');
      $request->session()->flash('message', '發送訂單失敗! 請確認閣下郵箱可正常收發電郵! 若情況持續, 請直接從網站查閱訂單, 謝謝!');

      return redirect()->back();
    }

    public function viewPaymentInvoice(Request $request, $id)
    {
      $order = SomsOrder::with('client','items')->find($id);
      //
      $this->securityCheck($order, $request);

      if ($order->pay_qr_code != "")
        $qrCode = QrCode::format('png')->size(200)->generate($order->pay_qr_code);
      else
          $qrCode = "";
      //
      return view('emails/invoice-email-template', compact('order', 'qrCode'));
    }

    /* Obsolete Function - Since Order Create redirect to Index Page
     *
    public function orderCreate()
    {
      $client = Auth::guard('somsclient')->user();
      //
      $items = SomsItem::where('type','product')->with('prices')->get();
      $deliveryService = SomsItem::where('type','service')->where('category','delivery')->first();
      $paymentTypes = SomsPaymentType::all();
      //
      $universityItemPrices = null;
      $universityLocations = null;
      $universityDateTimes = null;

      if($client->university_id != null)
      {
        $universityItemPrices = SomsItemPrice::where('university_id', $client->university_id)->get();

        $universityLocations = SomsLocation::where('university_id', $client->university_id)->get();
        $universityDateTimes = SomsDateTime::where('university_id', $client->university_id)->get();
      }

      return view('somsclient/order-create', compact('items','deliveryService','paymentTypes', 'client', 'universityItemPrices', 'universityLocations', 'universityDateTimes'));
    }

    public function orderCreateSubmit(Request $request)
    {

      $client = Auth::guard('somsclient')->user();

      if(!$request->filled('client') || $request->get('client') != Auth::guard('somsclient')->user()->id){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
      // Create Order
      $newOrder = new SomsOrder;
      $newOrder->client_id = $client->id;
      $newOrder = SomsOrder::prepareCreateData($newOrder, $request);
      $newOrder->save();

      $productQtyArray = $request->get('product-qty');
      foreach ($productQtyArray as $id => $qty) {

        // echo $id.' : '.$qty;
        if($qty > 0){
          $newOrderItem = new SomsOrderItem;
          $newOrderItem->order_id = $newOrder->id;
          $newOrderItem->item_id = $id;
          $newOrderItem->item_qty = $qty;
          if($client->university_id != null){
            $itemPrice = SomsItemPrice::where('university_id', $client->university_id)->where('item_id', $id)->first();
            if($itemPrice){
              $newOrderItem->item_price_id = $itemPrice->id;
            }
          }
          $newOrderItem->save();
        }
      }
      //
      return PaymentController::processPayment($newOrder, $client, $request, route('wechatpay-return'), route('alipay-return'));
    }
    */

    /* Obsolete Function - Since Order Update Have New Function
     *
    public function orderUpdate(Request $request, $id)
    {
      $order = SomsOrder::with('client','items')->find($id);
      //
      $this->securityCheck($order, $request);
      //
      $universityLocations = null;
      $universityDateTimes = null;

      if($order->client->university_id != null)
      {
        $universityLocations = SomsLocation::where('university_id', $order->client->university_id)->get();
        $universityDateTimes = SomsDateTime::where('university_id', $order->client->university_id)->get();
      }

      return view('somsclient/order-update', compact('order', 'universityLocations', 'universityDateTimes'));
    }

    public function orderUpdateSubmit(Request $request, $id)
    {
      // For Security Check
      $orderCode = $request->get('code');
      $orderCheck = SomsOrder::where('code', $orderCode)->where('current_version', 1)->first();
      //
      $order = SomsOrder::with('client','items')->find($id);

      if($orderCheck == null || $order == null || $order->id != $orderCheck->id){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
      if($order->client->id != Auth::guard('somsclient')->user()->id){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
      // Check Change for event log
      $change = array();
      $change['checkin_location'] = array();
      $change['checkout_location'] = array();

      if($request->get('checkin_location_type') == 'default'){
        $newCheckinLocationId = $request->get('checkin_location');
        if($newCheckinLocationId != $order->checkin_location_id){

          if($order->checkin_location_id != null)
            $change['checkin_location']['from'] = $order->checkin_location_id;
          else
            $change['checkin_location']['from'] = $order->checkin_location_other;

          $change['checkin_location']['to'] = $newCheckinLocationId;
        }
      }else{
        $newCheckinLocationOther = $request->get('checkin_location_other');
        if($newCheckinLocationOther != $order->checkin_location_other){

          if($order->checkin_location_id != null)
            $change['checkin_location']['from'] = $order->checkin_location_id;
          else
            $change['checkin_location']['from'] = $order->checkin_location_other;

          $change['checkin_location']['to'] = $newCheckinLocationOther;
        }
      }

      if($request->get('checkout_location_type') == 'default'){
        $newCheckoutLocationId = $request->get('checkout_location');
        if($newCheckoutLocationId != $order->checkout_location_id){

          if($order->checkout_location_id != null)
            $change['checkout_location']['from'] = $order->checkout_location_id;
          else
            $change['checkout_location']['from'] = $order->checkout_location_other;

          $change['checkout_location']['to'] = $newCheckoutLocationId;
        }
      }else{
        $newCheckoutLocationOther = $request->get('checkout_location_other');
        if($newCheckoutLocationOther != $order->checkout_location_other){

          if($order->checkout_location_id != null)
            $change['checkout_location']['from'] = $order->checkout_location_id;
          else
            $change['checkout_location']['from'] = $order->checkout_location_other;

          $change['checkout_location']['to'] = $newCheckoutLocationOther;
        }
      }

      if(sizeof($change) == 0){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '沒有任何更改!');
        return redirect()->back();
      }
      // return json_encode($change);
      // Process Update
      // Create a Duplicate Row without ID
      $newOrder = $order->replicate();
      if($request->get('checkin_location_type') == 'default'){
        $newOrder->checkin_location_id = $request->get('checkin_location');
        $newOrder->checkin_location_other = null;
      }else{
        $newOrder->checkin_location_id = null;
        $newOrder->checkin_location_other = $request->get('checkin_location_other');
      }
      if($request->get('checkout_location_type') == 'default'){
        $newOrder->checkout_location_id = $request->get('checkout_location');
        $newOrder->checkout_location_other = null;
      }else{
        $newOrder->checkout_location_id = null;
        $newOrder->checkout_location_other = $request->get('checkout_location_other');
      }
      $newOrder->version = $order->version + 1;
      $newOrder->current_version = 1;
      $newOrder->save();
      // Update Current Order as a old version
      $order->current_version = 0;
      $order->save();
      // Create New Order Item From Old Order
      foreach ($order->items as $item) {
        $newItem = $item->replicate();
        $newItem->order_id = $newOrder->id;
        $newItem->save();
      }
      // Create Event Log
      $newEventLog = new SomsEventLog;
      $newEventLog->event_type_id = SomsEventType::where('code', 'update_order')->first()->id;
      $newEventLog->target_id = $newOrder->id;
      $newEventLog->from_target_id = $order->id;
      $newEventLog->json_remark = json_encode($change);
      $newEventLog->save();
      // return json_encode($order).':'.json_encode($order->replicate());
      // return json_encode($request->input());
      // return view('somsclient/order-update', compact('order'));
      $request->session()->flash('alert-class', 'alert-success');
      $request->session()->flash('message', '成功更新訂單!');
      return redirect()->route('somsclient.order.update', ['id' => $newOrder->id]);
    }

    public function clientUpdate()
    {
      $client = Auth::guard('somsclient')->user();

      return view('somsclient/client-update', compact('client'));
    }

    public function clientUpdateSubmit(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'wechat' => 'required|max:255',
          'mobile_phone_hk' => 'required_without:mobile_phone_cn|max:255|nullable|regex:/^[1-9][0-9]{7,12}$/',
          'mobile_phone_cn' => 'required_without:mobile_phone_hk|max:255|nullable|regex:/^[1-9][0-9]{7,12}$/',
          'address1' => 'required|max:255',
          'address2' => 'max:255',
          'password_new' => 'nullable|string|min:8|confirmed',
          // 'email' => [
          //     'required', 'max:255',
          //       Rule::unique('soms_clients', 'email')->ignore($request->get('id'))
          // ],
          'password' => [
              'required_with:password_new', 'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value != '' && !Hash::check($value, Auth::guard('somsclient')->user()->password)) {
                        $fail($attribute.' is invalid.');
                    }
                },
          ],
      ]);

      if ($validator->fails()) {
          return redirect()->back()
                      ->withErrors($validator)
                      ->withInput();
      }
      // For Security Check
      $clientCheck = SomsClient::find($request->get('id'));
      $client = Auth::guard('somsclient')->user();

      if($clientCheck->id != $client->id){
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '請求無效!');
        return redirect()->route('somsclient.dashboard');
      }
      // Update Process
      // $client->email = $request->get('email');
      $client->wechat = $request->get('wechat');
      $client->mobile_phone_hk = $request->get('mobile_phone_hk');
      $client->mobile_phone_cn = $request->get('mobile_phone_cn');
      $client->address1 = $request->get('address1');
      $client->address2 = $request->get('address2');
      if($request->filled('password_new')){
        $client->password = Hash::make($request->get('password_new'));
      }
      //
      if($client->isDirty()){
        // Check Change for event log
        $change = array();
        $changeFields = $client->getDirty();
        foreach ($changeFields as $field => $value) {
          $change[$field]['from'] = $client->getOriginal($field);
          $change[$field]['to'] = $value;
        }
        //
        $client->save();
        // Create Event Log
        $newEventLog = new SomsEventLog;
        $newEventLog->event_type_id = SomsEventType::where('code', 'update_client')->first()->id;
        $newEventLog->target_id = $client->id;
        $newEventLog->json_remark = json_encode($change);
        $newEventLog->save();
        //
        $request->session()->flash('alert-class', 'alert-success');
        $request->session()->flash('message', '成功更改個人資料!');
        return redirect()->back();
      }
      else{
        $request->session()->flash('alert-class', 'alert-warning');
        $request->session()->flash('message', '沒有任何更改!');
        return redirect()->back();
      }
    }

    */
}
