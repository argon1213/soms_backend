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

        @if($order->client->university_id != null && $order->client->university_id > 0)
          <p class="mb-0 font-weight-bold">學生證號碼 Student ID No.</p>
          <p class="mb-4">{{ $order->client->student_id }}</p>

          <p class="mb-0 font-weight-bold">Wechat ID</p>
          <p class="mb-0">{{ $order->client->wechat }}</p>
        @endif

      </div>

      <div class="p-4 mb-3 bg-white">
        <p class="h4 text-black mb-3 font-weight-bold">產品明細</p>

        @foreach($order->items as $item)
          @if($item->item->category == "box")
            <p class="mb-0 font-weight-bold">{{ $item->item->name_cn }}</p>
            <p class="mb-4">
              <b>{{ $item->item_qty }}</b> (個)
              <b>HKD {{ $item->item_price }}</b>
            </p>
          @endif
        @endforeach
        <p class="mb-0 font-weight-bold">月費</p>
        <p class="mb-4">
          <b>HKD {{ $order->getMonthlyFee() }}</b>
        </p>
        @foreach($order->items as $item)
          @if($item->item->category != "box")
            <p class="mb-0 font-weight-bold">{{ $item->item->name_cn }}</p>
            <p class="mb-4">
              <b>{{ $item->item_qty }}</b> (個)
              <b>HKD {{ $item->item_price }}</b>
            </p>
          @endif
        @endforeach
        <p class="mb-0 font-weight-bold">其他費用</p>
        <p class="mb-4">
          <b>HKD {{ $order->getOtherFee() }}</b>
        </p>
      </div>

      <div class="p-4 mb-3 bg-white">
        <p class="mb-0 font-weight-bold">總收費 Total Fee</p>
        <p class="mb-4">HKD {{ $order->total_fee }}</p>

        @if($order->incompletePayment())
          <p class="mb-0 font-weight-bold">已付 Paid Fee</p>
          <p class="mb-4">HKD {{ $order->paid_fee }}</p>

          <p class="mb-0 font-weight-bold">未付 Unpaid Fee</p>
          <p class="mb-4">HKD {{ $order->incompletePayment()->amount }}</p>
        @else
          <p class="mb-0 font-weight-bold">沒有未付款項</p>
        @endif

      </div>
    </div>

    <div class="col-md-12 col-lg-8 mb-5">
      <form method="post" action="{{ route('somsclient.order.update.submit', ['id'=>$order->id]) }}" class="p-5 bg-white">
        @csrf
        <input type="hidden" id="code" name="code" value="{{ $order->code }}">
        <!-- Emptyout -->
        @component('components.somsclient-order-location', ['type'=>'emptyout','model'=>$order,'cities'=>$cities,'states'=>$states])
        @endcomponent
        @component('components.somsclient-order-datetime', ['type'=>'emptyout','model'=>$order])
        @endcomponent
        <!-- Checkin -->
        @component('components.somsclient-order-location', ['type'=>'checkin','model'=>$order,'cities'=>$cities,'states'=>$states])
        @endcomponent
        @component('components.somsclient-order-datetime', ['type'=>'checkin','model'=>$order])
        @endcomponent
        <!-- Checkout -->
        @component('components.somsclient-order-location', ['type'=>'checkout','model'=>$order,'cities'=>$cities,'states'=>$states])
        @endcomponent
        @component('components.somsclient-order-datetime', ['type'=>'checkout','model'=>$order])
        @endcomponent

        <!--
          Show Storage Period
          Show New Storage Period
          Show Price Changed
        -->
        <div class="row form-group">
          <div class="col-md-6 mb-3 mb-md-0">
            <label class="font-weight-bold" for="storage_month">
              {{ __('somsorder.storage_month')  }}
            </label>
            <input class="form-control" type="text" id="storage_month" name="storage_month" value="{{ $order->storage_month }}" readonly>
          </div>
          <div class="col-md-6 mb-3 mb-md-0">
            <label class="font-weight-bold" for="storage_month">
              {{ __('somsorder.storage_month')  }} (更改日期後)
            </label>
            <input class="form-control" type="text" id="storage_month_preview" name="storage_month_preview" value="{{ $order->storage_month }}" readonly>
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-6 mb-3 mb-md-0">

          </div>
          <div class="col-md-6 mb-3 mb-md-0">
            <label class="font-weight-bold" for="storage_month">
              需付費用 (更改日期後)
            </label>
            <input class="form-control" type="text" id="fee_perview" name="fee_perview" value="0" readonly>
          </div>
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
    // If Checkin & Checkout Date is Changed
    $('input[type=date][name=checkin_date_other]').change(function() {
          console.log('checkin_date_other change');
          var storageMonth = calcDateDiffByMonth($("#checkin_date_other").val(), $("#checkout_date_other").val());
          $("#storage_month_preview").val(storageMonth);
          calcFee();
    });
    $('input[type=date][name=checkout_date_other]').change(function() {
          console.log('checkout_date_other change');
          var storageMonth = calcDateDiffByMonth($("#checkin_date_other").val(), $("#checkout_date_other").val());
          $("#storage_month_preview").val(storageMonth);
          calcFee();
    });
  });

  function calcFee() {
    var currStorageMonth = $("#storage_month").val();
    var newStorageMonth = $("#storage_month_preview").val();

    var fee = (newStorageMonth - currStorageMonth) * {{ $order->getMonthlyFee() }};
    fee = (fee < 0)? 0:fee;
    console.log(fee);
    $("#fee_perview").val(fee);
  }

  function calcDateDiffByMonth(d1Str, d2Str) {
      var d1 = new Date(d1Str);
      var d2 = new Date(d2Str);

      var yearDiff = d2.getFullYear() - d1.getFullYear();
      var monthDiff = d2.getMonth() - d1.getMonth();
      var dayDiff = d2.getDate() - d1.getDate();

      var result = (yearDiff * 12) + monthDiff + ((dayDiff > 0) ? 1 : 0);
      // console.log(yearDiff+':'+monthDiff+':'+dayDiff+':'+result);
      return result;
  }
</script>
@stop
