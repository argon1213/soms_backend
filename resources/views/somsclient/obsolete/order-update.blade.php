@extends('layouts.vendor')

@section('title')
@lang('navbar.somsclient.order.update')
@stop

@section('banner')
  @component('components.somsclient-banner', ['routeLink'=>['somsclient.dashboard','somsclient.order.update']])
  @endcomponent
@stop

@section('content')
<div class="container">
  <div class="row">
    <div class="col-lg-4">
      <div class="p-4 mb-3 bg-white">
        <p class="mb-0 font-weight-bold">訂單號碼:</p>
        <p class="mb-4">{{ $order->code }}</p>

        <p class="mb-0 font-weight-bold">姓名 Name</p>
        <p class="mb-4">{{ $order->client->name }}</p>

        <p class="mb-0 font-weight-bold">電郵地址 Email Address</p>
        <p class="mb-4">{{ $order->client->email }}</p>

        <p class="mb-0 font-weight-bold">學生證號碼 Student ID No.</p>
        <p class="mb-4">{{ $order->client->id_number }}</p>

        <p class="mb-0 font-weight-bold">Wechat ID</p>
        <p class="mb-0">{{ $order->client->wechat }}</p>
      </div>

      <div class="p-4 mb-3 bg-white">
        <p class="h4 text-black mb-3 font-weight-bold">產品明細</p>
        @foreach($order->items as $item)
          <p class="mb-0 font-weight-bold">{{ $item->item->name_cn }}</p>
          <p class="mb-4">
            <b>{{ $item->item_qty }}</b> (個)
            <b>HKD {{ (empty($item->price))? $item->item->price:$item->price->price }}</b>
          </p>
        @endforeach
      </div>

      <div class="p-4 mb-3 bg-white">
        <p class="mb-0 font-weight-bold">產品收費 Product Fee</p>
        <p class="mb-4">HKD {{ $order->product_total_fee }}</p>

        @if(isset($order->delivery_service_fee) && $order->delivery_service_fee > 0)
          <p class="mb-0 font-weight-bold">外送服務 額外收費 Delivery Service Extra Fee</p>
          <p class="mb-4">HKD {{ $order->delivery_service_fee }}</p>
        @endif

        <p class="mb-0 font-weight-bold">總收費 Total Fee</p>
        <p class="mb-4">HKD {{ $order->total_fee }}</p>
      </div>
    </div>

    <div class="col-md-12 col-lg-8 mb-5">
      <form method="post" action="{{ route('somsclient.order.update.submit', ['id'=>$order->id]) }}" class="p-5 bg-white">
        @csrf
        <input type="hidden" id="code" name="code" value="{{ $order->code }}">
        <!-- <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="fullname">訂單號碼:</label>
            <input type="text" class="form-control" value="{{ $order->code }}" readonly>
            <input type="hidden" id="code" name="code" value="{{ $order->code }}">
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="fullname">姓名 Name:</label>
            <input type="text" class="form-control" value="{{ $order->client->name }}" readonly>
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="fullname">電郵地址 Email Address:</label>
            <input type="text" class="form-control" value="{{ $order->client->email }}" readonly>
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="fullname">學生證號碼 Student ID No.:</label>
            <input type="text" class="form-control" value="{{ $order->client->id_number }}" readonly>
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="fullname">Wechat ID:</label>
            <input type="text" class="form-control" value="{{ $order->client->wechat }}" readonly>
          </div>
        </div>
        <div class="p-4 mb-3 bg-white">
          <div class="row">
            <div class="col-md-12">
              <p class="font-weight-bold">產品明細:</p>
            </div>
          </div>
          @foreach($order->items as $item)
            <div class="row">
              <div class="col-md-6">
                <p class="mb-0 font-weight-bold">{{ $item->item->name_cn }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-4">
                  <b>{{ $item->item_qty }}</b> (個)
                  <b>HKD {{ (empty($item->price))? $item->item->price:$item->price->price }}</b>
                </p>
              </div>
            </div>
          @endforeach
        </div> -->
        <!-- Checkin Location -->
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="checkin_location">
              <input type="radio" id="checkin_location_type_default" name="checkin_location_type" value="default" {{ ($order->checkinLocation)?'checked':'' }}>指定取貨點：
            </label>
            @if(isset($universityLocations) && sizeof($universityLocations) > 0)
              <select class="form-control" id="checkin_location" name="checkin_location">
                <option value="">-- Please Select 請選擇 --</option>
                @foreach($universityLocations as $universityLocation)
                  <option value="{{ $universityLocation->id }}" {{ ($order->checkinLocation && $order->checkinLocation->id == $universityLocation->id)?'selected':'' }}>
                    {{ $universityLocation->location }}
                  </option>
                @endforeach
              </select>
            @else
              <select class="form-control" id="checkin_location" name="checkin_location" disabled>
                <option value="">-- Please Select 請選擇 --</option>
              </select>
            @endif
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="checkin_location">
              <input type="radio" id="checkin_location_type_other" name="checkin_location_type" value="other" {{ ($order->checkinLocation)?'':'checked' }}>其他取貨點:
            </label>
            @if($order->checkinLocation)
              <input class="form-control" type="text" id="checkin_location_other" name="checkin_location_other" value="" disabled>
            @else
              <input class="form-control" type="text" id="checkin_location_other" name="checkin_location_other" value="{{ $order->checkin_location_other }}">
            @endif
          </div>
        </div>
        <!-- Checkin Datetime -->
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="checkin_date">
              <input type="radio" id="checkin_date_type_default" name="checkin_date_type" value="default" {{ ($order->checkinDatetime)?'checked':'' }} disabled>指定取貨時間：
            </label>
            @if(isset($universityDateTimes) && sizeof($universityDateTimes) > 0)
              <select class="form-control" id="checkin_date" name="checkin_date" disabled>
                <option value="">-- Please Select 請選擇 --</option>
                @foreach($universityDateTimes as $universityDateTime)
                  <option value="{{ $universityDateTime->id }}" {{ ($order->checkinDatetime && $order->checkinDatetime->id == $universityDateTime->id)?'selected':'' }}>
                    {{ $universityDateTime->date.' '.$universityDateTime->period }}
                  </option>
                @endforeach
              </select>
            @else
              <select class="form-control" id="checkin_date" name="checkin_date" disabled>
                <option value="">-- Please Select 請選擇 --</option>
              </select>
            @endif
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="checkin_date">
              <input type="radio" id="checkin_date_type_other" name="checkin_date_type" value="other" {{ ($order->checkinDatetime)?'':'checked' }} disabled>其他取貨時間：
            </label>
          </div>
          @if($order->checkinDatetime)
            <div class="col-md-8 mb-3 mb-md-0">
              <input class="form-control" type="date" id="checkin_date_other" name="checkin_date_other" value="" disabled>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <select class="form-control" id="checkin_time_other" name="checkin_time_other" disabled>
                <option value="">AM / PM</option>
                <option value="AM">AM</option>
                <option value="PM">PM</option>
              </select>
            </div>
          @else
            <div class="col-md-8 mb-3 mb-md-0">
              <input class="form-control" type="date" id="checkin_date_other" name="checkin_date_other" value="{{ $order->checkin_date_other }}" disabled>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <select class="form-control" id="checkin_time_other" name="checkin_time_other" disabled>
                <option value="">AM / PM</option>
                <option value="AM" {{ ($order->checkout_time_other == 'AM')?'selected':'' }}>AM</option>
                <option value="PM" {{ ($order->checkout_time_other == 'PM')?'selected':'' }}>PM</option>
              </select>
            </div>
          @endif
        </div>
        <!-- Checkout Location -->
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="checkout_location">
              <input type="radio" id="checkout_location_type_default" name="checkout_location_type" value="default" {{ ($order->checkoutLocation)?'checked':'' }}>指定收貨點：
            </label>
            @if(isset($universityLocations) && sizeof($universityLocations) > 0)
              <select class="form-control" id="checkout_location" name="checkout_location">
                <option value="">-- Please Select 請選擇 --</option>
                @foreach($universityLocations as $universityLocation)
                  <option value="{{ $universityLocation->id }}" {{ ($order->checkoutLocation && $order->checkoutLocation->id == $universityLocation->id)?'selected':'' }}>
                    {{ $universityLocation->location }}
                  </option>
                @endforeach
              </select>
            @else
              <select class="form-control" id="checkout_location" name="checkout_location" disabled>
                <option value="">-- Please Select 請選擇 --</option>
              </select>
            @endif
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="checkout_location">
              <input type="radio" id="checkout_location_type_other" name="checkout_location_type" value="other" {{ ($order->checkoutLocation)?'':'checked' }}>其他收貨點:
            </label>
            @if($order->checkoutLocation)
              <input class="form-control" type="text" id="checkout_location_other" name="checkout_location_other" value="" disabled>
            @else
              <input class="form-control" type="text" id="checkout_location_other" name="checkout_location_other" value="{{ $order->checkout_location_other }}">
            @endif
          </div>
        </div>
        <!-- Checkout Datetime -->
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="checkout_date">
              <input type="radio" id="checkout_date_type_default" name="checkout_date_type" value="default" {{ ($order->checkoutDatetime)?'checked':'' }} disabled>指定收貨時間：
            </label>
            @if(isset($universityDateTimes) && sizeof($universityDateTimes) > 0)
              <select class="form-control" id="checkout_date" name="checkout_date" disabled>
                <option value="">-- Please Select 請選擇 --</option>
                @foreach($universityDateTimes as $universityDateTime)
                  <option value="{{ $universityDateTime->id }}" {{ ($order->checkoutDatetime && $order->checkoutDatetime->id == $universityDateTime->id)?'selected':'' }}>
                    {{ $universityDateTime->date.' '.$universityDateTime->period }}
                  </option>
                @endforeach
              </select>
            @else
              <select class="form-control" id="checkout_date" name="checkout_date" disabled>
                <option value="">-- Please Select 請選擇 --</option>
              </select>
            @endif
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="checkout_date">
              <input type="radio" id="checkout_date_type_other" name="checkout_date_type" value="other" {{ ($order->checkoutDatetime)?'':'checked' }} disabled>其他收貨時間：
            </label>
          </div>
          @if($order->checkoutDatetime)
            <div class="col-md-8 mb-3 mb-md-0">
              <input class="form-control" type="date" id="checkout_date_other" name="checkout_date_other" value="" disabled>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <select class="form-control" id="checkout_time_other" name="checkout_time_other" disabled>
                <option value="">AM / PM</option>
                <option value="AM">AM</option>
                <option value="PM">PM</option>
              </select>
            </div>
          @else
            <div class="col-md-8 mb-3 mb-md-0">
              <input class="form-control" type="date" id="checkout_date_other" name="checkout_date_other" value="{{ $order->checkout_date_other }}" disabled>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <select class="form-control" id="checkout_time_other" name="checkout_time_other" disabled>
                <option value="">AM / PM</option>
                <option value="AM" {{ ($order->checkout_time_other == 'AM')?'selected':'' }}>AM</option>
                <option value="PM" {{ ($order->checkout_time_other == 'PM')?'selected':'' }}>PM</option>
              </select>
            </div>
          @endif
        </div>

        <div class="row form-group">
          <div class="col-md-12">
            <input type="submit" value="確認更改" class="btn btn-primary py-2 px-5">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('page-js')
<script>
  $(function(){
    $('input[type=radio][name=checkin_location_type]').change(function() {
        if ($(this).val() == 'default') {
            $('#checkin_location').prop('disabled', false);
            $('#checkin_location').prop('required', true);
            $('#checkin_location_other').prop('disabled', true);
            $('#checkin_location_other').prop('required', false);
        }
        else if ($(this).val() == 'other') {
            $('#checkin_location').prop('disabled', true);
            $('#checkin_location').prop('required', false);
            $('#checkin_location_other').prop('disabled', false);
            $('#checkin_location_other').prop('required', true);
        }
    });

    $('input[type=radio][name=checkout_location_type]').change(function() {
        // console.log($(this).val());
        if ($(this).val() == 'default') {
            $('#checkout_location').prop('disabled', false);
            $('#checkout_location').prop('required', true);
            $('#checkout_location_other').prop('disabled', true);
            $('#checkout_location_other').prop('required', false);
        }
        else if ($(this).val() == 'other') {
            $('#checkout_location').prop('disabled', true);
            $('#checkout_location').prop('required', false);
            $('#checkout_location_other').prop('disabled', false);
            $('#checkout_location_other').prop('required', true);
        }
    });
  });
</script>
@stop
