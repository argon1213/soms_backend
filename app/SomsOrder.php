<?php

namespace App;

use DB;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\SomsPaymentStatus;


class SomsOrder extends Model
{
  use SoftDeletes;

  const MOVEMENT_PREFIX_ARRAY = [
    'emptyout', 'checkin', 'checkout'
  ];

  const MOVEMENT_SUFFIX_ARRAY = [
    '_location_other', '_city_id', '_state_id', '_date_other', '_time_other'
  ];

  public static function boot()
  {
      parent::boot();

      static::created(function($model)
      {
        if($model->code == null)
        {
          // Generate Order Code By ID
          $codePrefix = 'UBR';            // UBox Order - Don't use O prevent confuse with zero
          $orderId    = $model->id;
          $randomNo   = rand(1,10000);   // Prevent Guess Other Order Number

          $generateCode = $codePrefix.'-'.$orderId.'-'.$randomNo;
          //
          //$checksum = substr(md5($generateCode), 0, 2) . substr(md5($generateCode), -2);
          //$model->code = $generateCode.'-'.$checksum;

          $model->code = $generateCode;
          $model->save();
        }
      });

      static::updated(function($model)
      {

      });

      static::saving(function($model)
      {

      });
  }

  public function client()
  {
      return $this->belongsTo('App\SomsClient', 'client_id');
  }

  public function city()
  {
      return $this->belongsTo(SomsClient::class, 'emptyout_city_id');
  }

  public function state()
  {
      return $this->belongsTo(SomsState::class, 'emptyout_state_id');
  }

  public function pickcity()
  {
      return $this->belongsTo(SomsClient::class, 'checkin_city_id');
  }

  public function pickstate()
  {
      return $this->belongsTo(SomsState::class, 'checkin_state_id');
  }

  public function checkinLocation()
  {
      return $this->belongsTo('App\SomsLocation', 'checkin_location_id');
  }

  public function checkoutLocation()
  {
      return $this->belongsTo('App\SomsLocation', 'checkout_location_id');
  }

  public function checkinDatetime()
  {
      return $this->belongsTo('App\SomsDatetime', 'checkin_datetime_id');
  }

  public function checkoutDatetime()
  {
      return $this->belongsTo('App\SomsDatetime', 'checkout_datetime_id');
  }

  public function items()
  {
      return $this->hasMany('App\SomsOrderItem', 'order_id');
  }

  public function payments()
  {
      return $this->hasMany('App\SomsOrderPayment', 'order_id');
  }

  public function paymentType()
  {
      return $this->belongsTo('App\SomsPaymentType', 'payment_type_id');
  }

  public function paymentStatus()
  {
      return $this->belongsTo('App\SomsPaymentStatus', 'payment_status_id');
  }

  public function incompletePayment()
  {
      return $this->payments->where('payment_status_id', SomsPaymentStatus::UNPAID)->first();
  }

  public function completePayments()
  {
      return $this->payments->where('payment_status_id', SomsPaymentStatus::PAID);
  }

  public function completePaymentsCount()
  {
      return $this->payments->where('payment_status_id', SomsPaymentStatus::PAID)->count();
  }

  public function status()
  {
      return $this->belongsTo('App\SomsOrderStatus', 'order_status_id');
  }

  public function getStorageMonth()
  {
      $checkinDate = Carbon::createFromFormat('Y-m-d', $this->checkin_date_other);
      $checkoutDate = Carbon::createFromFormat('Y-m-d', $this->checkout_date_other);

      $yearDiff = $checkoutDate->year - $checkinDate->year;
      $monthDiff = $checkoutDate->month - $checkinDate->month;
      $dayDiff = $checkoutDate->day - $checkinDate->day;

      return ($yearDiff * 12) + $monthDiff + (($dayDiff > 0) ? 1 : 0);
  }

  public function getMonthlyFee()
  {
      $result = 0;
      $temp = $this->items->where('item.category', "box");
      foreach ($temp as $i) {
        $result += $i->total();
      }
      return $result;
  }

  public function getOtherFee()
  {
      $result = 0;
      $temp = $this->items->where('item.category', "!=", "box");
      foreach ($temp as $i) {
        $result += $i->total();
      }
      return $result;
  }

  public function isNeedCreateNewPayment()
  {
    $unpaid_amount = $this->total_fee - $this->paid_fee;
    // Found if have unpaid payment already
    $incompletePayment = $this->incompletePayment();
    if($incompletePayment == null)
      return true;
    // If Amount is changed
    if($incompletePayment->amount != $unpaid_amount)
    {
      // Cancel & Create New Payment
      $incompletePayment->payment_status_id = SomsPaymentStatus::CANCELLED;
      $incompletePayment->save();

      return true;
    }

    return false;
  }

  protected $appends = ['balance','paperBoxes','standardBoxes','oversizeItems','vacuumBags','wardrobe',
    'emptyout_cutoff','checkin_cutoff','checkout_cutoff'];

  public function getEmptyOutCutOffAttribute()
  {
      if($this->emptyout_date_other == null)
        return false;

      $cutOffDate = Carbon::createFromFormat('Y-m-d', $this->emptyout_date_other)->subDays(2);

      $cutOffStatus = SomsOrderStatus::where('code', 'empty_delivery')->first();
      return $this->status->id >= $cutOffStatus->id || Carbon::today()->gt($cutOffDate);
  }

  public function getCheckInCutOffAttribute()
  {
      if($this->checkin_date_other == null)
        return false;

      $cutOffDate = Carbon::createFromFormat('Y-m-d', $this->checkin_date_other)->subDays(2);

      $cutOffStatus = SomsOrderStatus::where('code', 'check_in')->first();
      return $this->status->id >= $cutOffStatus->id || Carbon::today()->gt($cutOffDate);
  }

  public function getCheckOutCutOffAttribute()
  {
      if($this->checkout_date_other == null)
        return false;

      $cutOffDate = Carbon::createFromFormat('Y-m-d', $this->checkout_date_other)->subDays(2);

      $cutOffStatus = SomsOrderStatus::where('code', 'check_out')->first();
      return $this->status->id >= $cutOffStatus->id || Carbon::today()->gt($cutOffDate);
  }

  public function getBalanceAttribute()
  {
      return bcsub($this->total_fee, $this->paid_fee, 2);
  }

  public function getPaperBoxesAttribute()
  {
      $item = $this->items()->where('item_id', 2)->first();
      return ($item)?$item->item_qty:0;
  }

  public function getStandardBoxesAttribute()
  {
      $item = $this->items()->where('item_id', 3)->first();
      return ($item)?$item->item_qty:0;
  }

  public function getOversizeItemsAttribute()
  {
      $item = $this->items()->where('item_id', 4)->first();
      return ($item)?$item->item_qty:0;
  }

  public function getWardrobeAttribute()
  {
      $item = $this->items()->where('item_id', 9)->first();
      return ($item)?$item->item_qty:0;
  }

  public function getVacuumBagsAttribute()
  {
      $item = $this->items()->where('item_id', 5)->first();
      return ($item)?$item->item_qty:0;
  }

  public static function prepareTestData($newOrder, $productQtyArray)
  {
      $newOrder->checkin_location_other = 'Checkin Location Other';
      $newOrder->checkin_date_other = Carbon::createFromDate(2020, 1, 24);
      $newOrder->checkin_time_other = 'AM';

      $newOrder->checkout_location_other = 'Checkout Location Other';
      $newOrder->checkout_date_other = Carbon::createFromDate(2020, 1, 31);
      $newOrder->checkout_time_other = 'PM';

      $product_total_fee = 0;
      $delivery_service_fee = 29;
      foreach ($productQtyArray as $id => $qty) {
        if($qty > 0){
          $product = SomsProduct::find($id);
          $product_total_fee += $product->price * $qty;
          if($product->category == 'box'){
            $delivery_service_fee += 29 * $qty;
          }
        }
      }

      $newOrder->product_total_fee = $product_total_fee;
      $newOrder->storage_month = 1;
      $newOrder->delivery_service_fee = $delivery_service_fee;
      $newOrder->total_fee = $product_total_fee + $delivery_service_fee;
      $newOrder->payment_type_id = 6;

      $newOrder->version = 1;
      $newOrder->current_version = 1;

      return $newOrder;
  }

  public static function prepareCreateDataNew($newOrder, $request)
  {
    foreach (self::MOVEMENT_PREFIX_ARRAY as $order_movement_prefix) {
      //
      $newOrder->{$order_movement_prefix.'_date_other'}  = $request->{$order_movement_prefix.'_date_other'};
      $newOrder->{$order_movement_prefix.'_time_other'}  = $request->{$order_movement_prefix.'_time_other'};
      //
      $address_prefix = '';
      if($request->pickup_address_same != 1)
        $address_prefix = 'pickup_';

      $newOrder->{$order_movement_prefix.'_location_other'} = $request->{$address_prefix.'address'};
      $newOrder->{$order_movement_prefix.'_city_id'}        = $request->{$address_prefix.'city'};
      $newOrder->{$order_movement_prefix.'_state_id'}       = $request->{$address_prefix.'state'};
    }

    $newOrder->promotion_id               = $request->get('input_promo_id');
    $newOrder->storage_period_id          = $request->get('order_storage_period');
    $newOrder->storage_month              = $request->get('storage-month');

    $newOrder->special_instruction        = $request->special_instruction;
    $newOrder->walkup                     = $request->walkup;
    $newOrder->mandatory_home_quarantine  = $request->mandatory_home_quarantine;

    $newOrder->total_fee                  = $request->get('total-fee');
    $newOrder->payment_type_id            = $request->get('order_payment_type');

    $newOrder->version = 1;
    $newOrder->current_version = 1;

    return $newOrder;
  }

  public static function prepareCreateDataNew4JSON($newOrder, $data) {
    // foreach (self::MOVEMENT_PREFIX_ARRAY as $order_movement_prefix) {
    //   //
    //   $newOrder->{$order_movement_prefix.'_date_other'}  = $data['date'][{$order_movement_prefix.'_date_other'}];
    //   $newOrder->{$order_movement_prefix.'_time_other'}  = $data['date'][{$order_movement_prefix.'_time_other'}];
    //   //
    //   $address_prefix = '';
    //   if($data['']['pickup_address_same != 1'])
    //     $address_prefix = 'pickup_';

    //   $newOrder->{$order_movement_prefix.'_location_other'} = $data['']['{$address_prefix.'address'}'];
    //   $newOrder->{$order_movement_prefix.'_city_id'}        = $data['']['{$address_prefix.'city'}'];
    //   $newOrder->{$order_movement_prefix.'_state_id'}       = $data['']['{$address_prefix.'state'}'];
    // }

    // $newOrder->promotion_id               = $data['']['get('input_promo_id')'];
    // $newOrder->storage_period_id          = $data['']['get('order_storage_period')'];
    // $newOrder->storage_month              = $data['']['get('storage-month')'];

    $newOrder->emptyout_location_other = $data['stuff']['address'];
    $deliveryDate = date($data['stuff']['deliveryDate']);
    $newOrder->emptyout_date_other = date($deliveryDate);
    $newOrder->emptyout_time_other = $data['stuff']['deliveryTime'];

    $newOrder->checkin_location_other = $data['stuff']['address'];
    $newOrder->checkin_date_other = date($data['stuff']['ladenReturnDate']);
    $newOrder->checkin_time_other = $data['stuff']['ladenReturnTime'];

    $newOrder->checkout_location_other = $data['stuff']['address'];
    $newOrder->checkout_date_other = date($data['stuff']['tentativeDate']);
    $newOrder->checkout_time_other = $data['stuff']['tentativeTime'];
    
    $newOrder->storage_expired_date = date($data['stuff']['expirationDate']);

    $newOrder->special_instruction        = $data['account']['instructions'];
    $newOrder->walkup                     = $data['account']['question1'];
    $newOrder->mandatory_home_quarantine  = $data['account']['question2'];

    $newOrder->total_fee                  = $data['carts']['total'];
    $newOrder->payment_type_id            = $data['carts']['payment_type'];
    $newOrder->storage_month              = $data['carts']['storage_month'];

    $newOrder->product_total_fee          = $data['carts']['stores_total'];
    // $newOrder->delivery_service_fee       = $data['carts']['materials_total'];

    $newOrder->version = 1;
    $newOrder->current_version = 1;

    return $newOrder;
  }
}
