@extends('layouts.vendor')

@section('title', __('navbar.somsclient.order.create'))

@section('banner')
  @component('components.somsclient-banner', ['routeLink'=>['somsclient.dashboard','somsclient.order.create']])
  @endcomponent
@stop

@section('content')
<div class="container">
  <div class="row">
    <div class="col-lg-4">
      <div class="p-4 mb-3 bg-white">
        <p class="h4 text-black mb-3 font-weight-bold">個人資料</p>

        <p class="mb-0 font-weight-bold">姓名 Name</p>
        <p class="mb-4">{{ $client->name }}</p>

        <p class="mb-0 font-weight-bold">電郵地址 Email Address</p>
        <p class="mb-4">{{ $client->email }}</p>

        <p class="mb-0 font-weight-bold">學生證號碼 Student ID No.</p>
        <p class="mb-4">{{ $client->id_number }}</p>

        <p class="mb-0 font-weight-bold">Wechat ID</p>
        <p class="mb-0">{{ $client->wechat }}</p>
      </div>
    </div>
    <div class="col-md-12 col-lg-8 mb-5">
      @if (Session::has('success'))
				<div id="wizard" class="p-sm-5 p-1 bg-white">
					<!-- Place Order Success -->
					<div class="success-title">Place Order Success <br/> 訂單已成功建立</div>
					<div class="container">
						<div class="row">
							<div class="col-12">
								<label id="payment-success-description">
									<!-- You can view your order by below url. -->
									{{ Session::get('success') }}
								</label>
								@if (Session::has('qr_code_url'))
									<div class="visible-print text-center">
										{!! QrCode::size(200)->generate(Session::get('qr_code_url')); !!}
									</div>
								@endif
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<label id="payment-success-description">
									<!-- 你可以從以下連結查閱你的訂單。 -->
								</label>
							</div>
						</div>
					</div>
				</div>
			@elseif (Session::has('error'))
				<div id="wizard" class="p-sm-5 p-1 bg-white">
					<!-- Place Order Success -->
					<div class="success-title">Place Order Success <br/> 訂單建立出現問題</div>
					<div class="container">
						<div class="row">
							<div class="col-12">
								<label id="payment-success-description">
									<!-- You can view your order by below url. -->
									{{ Session::get('error') }}
								</label>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<label id="payment-success-description">
									<!-- 你可以從以下連結查閱你的訂單。 -->
								</label>
							</div>
						</div>
					</div>
				</div>
			@else
        <form id="order-create-form" name="order-create-form" method="post" action="{{ route('somsclient.order.create.submit') }}" class="p-sm-5 p-1 bg-white">
          @csrf
          <input type="hidden" id="client" name="client" value="{{ $client->id }}">
          <?php
            $haveItemPrices = isset($universityItemPrices) && sizeof($universityItemPrices) > 0;
            $haveLocation = isset($universityLocations) && sizeof($universityLocations) > 0;
            $haveDatetime = isset($universityDateTimes) && sizeof($universityDateTimes) > 0;
          ?>
          <div>
            <h3></h3>
            <fieldset>
              <div class="product">
                @foreach($items as $item)
                  <div class="item product-item" data-id="{{ $item->id }}" data-category="{{ $item->category }}">
                    <div class="left">
                      <!-- <a href="#" class="thumb">
                        <img src="{{ asset('storage'.$item->uri) }}" alt="">
                      </a> -->
                      <img class="thumb" src="{{ asset('storage'.$item->uri) }}" alt="">
                      <div class="purchase">
                        <!-- <a href="#">{{ $item->name.' '.$item->name_cn }}</a> -->
                        <h6 id="product-{{ $item->id }}-name">
                          {{ $item->name.' '.$item->name_cn }}
                        </h6>
                        <div class="product-row">
                          <button type="button" class="product-qty product-qty-plus">+</button>
                          <input class="product-qty-input product-category-{{ $item->category }}" id="product-qty-{{ $item->id }}" name="product-qty[{{ $item->id }}]"
                            data-id="{{ $item->id }}" data-old="0" value="0" />
                          <button type="button" class="product-qty product-qty-minus">-</button>
                        </div>
                        <div id="product-{{ $item->id }}-error"></div>
                      </div>
                    </div>
                    <span class="price">
                      HKD<label id="product-{{ $item->id }}-price" class="product-price-label" data-original-price="{{ $item->price }}">{{ $item->price }}</label>
                    </span>
                  </div>
                @endforeach
              </div>
              <div class="checkout">
                <div class="total">
                  <span class="heading">小計 Subtotal :</span>
                  <span class="monthly-price">
                    每月 HKD <span class="monthly-price" id="monthly-price">0</span>
                  </span>
                  <span class="other-price">
                    另加 HKD <span class="other-price" id="other-price">0</span>
                  </span>
                </div>
              </div>
            </fieldset>
            <h3></h3>
            <fieldset>
              <div class="container form-radio m-0 p-0">
                <!-- Checkin Location -->
                <div class="row form-group mx-0">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <div class="form-radio-item">
                      <input type="radio" id="checkin_location_type_default" name="checkin_location_type" value="default" {{ ($haveLocation)?'checked':'disabled' }}>
                      <label id="checkin_location_label" class="font-weight-bold" for="checkin_location_type_default">指定取貨點：</label>
                      <span class="check"></span>
                    </div>
                    <select class="form-control" id="checkin_location" name="checkin_location" {{ ($haveLocation)?'':'disabled' }}>
                      <option value="">-- Please Select 請選擇 --</option>
                      @if($haveLocation)
                        @foreach($universityLocations as $universityLocation)
                          <option value="{{ $universityLocation->id }}">
                            {{ $universityLocation->location }}
                          </option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
                <div class="row form-group mx-0">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <div class="form-radio-item">
                      <input type="radio" id="checkin_location_type_other" name="checkin_location_type" value="other" {{ ($haveLocation)?'':'checked' }}>
                      <label id="checkin_location_other_label" class="font-weight-bold" for="checkin_location_type_other">其他取貨點:</label>
                      <span class="check"></span>
                    </div>
                    <input class="form-control" type="text" id="checkin_location_other" name="checkin_location_other" value="" {{ ($haveLocation)?'disabled':'' }}>
                  </div>
                </div>
                <!-- Checkin Datetime -->
                <div class="row form-group mx-0">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <div class="form-radio-item">
                      <input type="radio" id="checkin_date_type_default" name="checkin_date_type" value="default" {{ ($haveDatetime)?'checked':'disabled' }}>
                      <label id="checkin_date_label" class="font-weight-bold" for="checkin_date_type_default">指定取貨時間：</label>
                      <span class="check"></span>
                    </div>
                    <select class="form-control checkinout_date" id="checkin_date" name="checkin_date" {{ ($haveDatetime)?'':'disabled' }}>
                      <option value="">-- Please Select 請選擇 --</option>
                      @if($haveDatetime)
                        @foreach($universityDateTimes as $universityDateTime)
                          <option value="{{ $universityDateTime->id }}">
                            {{ $universityDateTime->date.' '.$universityDateTime->period }}
                          </option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
                <div class="row form-group mx-0">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <div class="form-radio-item">
                      <input type="radio" id="checkin_date_type_other" name="checkin_date_type" value="other" {{ ($haveDatetime)?'':'checked' }}>
                      <label id="checkin_date_other_label" class="font-weight-bold" for="checkin_date_type_other">其他取貨時間：</label>
                      <span class="check"></span>
                    </div>
                  </div>
                  <div class="col-md-8 mb-3 mb-md-0">
                    <input class="form-control checkinout_date" type="date" id="checkin_date_other" name="checkin_date_other" value="" {{ ($haveDatetime)?'disabled':'' }}>
                  </div>
                  <div class="col-md-4 mb-3 mb-md-0">
                    <select class="form-control" id="checkin_time_other" name="checkin_time_other" {{ ($haveDatetime)?'disabled':'' }}>
                      <option value="">AM / PM</option>
                      <option value="AM">AM</option>
                      <option value="PM">PM</option>
                    </select>
                  </div>
                </div>
                <!-- Checkout Location -->
                <div class="row form-group mx-0">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <div class="form-radio-item">
                      <input type="radio" id="checkout_location_type_default" name="checkout_location_type" value="default" {{ ($haveLocation)?'checked':'disabled' }}>
                      <label id="checkout_location_label" class="font-weight-bold" for="checkout_location_type_default">指定收貨點：</label>
                      <span class="check"></span>
                    </div>
                    <select class="form-control" id="checkout_location" name="checkout_location" {{ ($haveLocation)?'':'disabled' }}>
                      <option value="">-- Please Select 請選擇 --</option>
                      @if($haveLocation)
                        @foreach($universityLocations as $universityLocation)
                          <option value="{{ $universityLocation->id }}">
                            {{ $universityLocation->location }}
                          </option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
                <div class="row form-group mx-0">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <div class="form-radio-item">
                      <input type="radio" id="checkout_location_type_other" name="checkout_location_type" value="other" {{ ($haveLocation)?'':'checked' }}>
                      <label id="checkout_location_other_label" class="font-weight-bold" for="checkout_location_type_other">其他收貨點:</label>
                      <span class="check"></span>
                    </div>
                    <input class="form-control" type="text" id="checkout_location_other" name="checkout_location_other" value="" {{ ($haveLocation)?'disabled':'' }}>
                  </div>
                </div>
                <!-- Checkout Datetime -->
                <div class="row form-group mx-0">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <div class="form-radio-item">
                      <input type="radio" id="checkout_date_type_default" name="checkout_date_type" value="default" {{ ($haveDatetime)?'checked':'disabled' }}>
                      <label id="checkout_date_label" class="font-weight-bold" for="checkout_date_type_default">指定收貨時間：</label>
                      <span class="check"></span>
                    </div>
                    <select class="form-control checkinout_date" id="checkout_date" name="checkout_date" {{ ($haveDatetime)?'':'disabled' }}>
                      <option value="">-- Please Select 請選擇 --</option>
                      @if($haveDatetime)
                        @foreach($universityDateTimes as $universityDateTime)
                          <option value="{{ $universityDateTime->id }}">
                            {{ $universityDateTime->date.' '.$universityDateTime->period }}
                          </option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
                <div class="row form-group mx-0">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <div class="form-radio-item">
                      <input type="radio" id="checkout_date_type_other" name="checkout_date_type" value="other" {{ ($haveDatetime)?'':'checked' }}>
                      <label id="checkout_date_other_label" class="font-weight-bold" for="checkout_date_type_other">其他收貨時間：</label>
                      <span class="check"></span>
                    </div>
                  </div>
                  <div class="col-md-8 mb-3 mb-md-0">
                    <input class="form-control checkinout_date" type="date" id="checkout_date_other" name="checkout_date_other" value="" {{ ($haveDatetime)?'disabled':'' }}>
                  </div>
                  <div class="col-md-4 mb-3 mb-md-0">
                    <select class="form-control" id="checkout_time_other" name="checkout_time_other" {{ ($haveDatetime)?'disabled':'' }}>
                      <option value="">AM / PM</option>
                      <option value="AM">AM</option>
                      <option value="PM">PM</option>
                    </select>
                  </div>
                </div>
              </div>
            </fieldset>
            <h3></h3>
            <fieldset>
              <div class="order-preview">
                <div class="order-content">
                  <label class="preview-title">Your Order 你的訂單</label>
                  <div id="preview-product" class="container">
                  </div>
                  <hr/>
                  <label class="preview-title">Checkin / Checkout Detail 交收詳情</label>
                  <div id="preview-checkin" class="container">
                  </div>
                  <div id="preview-checkout" class="container">
                  </div>
                  <label id="delivery-service-fee-label" style="display:none;">{{ $deliveryService->price }}</label>
                  <div id="delivery-service-remark" class="container" style="display:none;">
                    <div class="col-12" style="font-size:10px;">{{ $deliveryService->description }}</div>
                  </div>
                  <hr/>
                  <label class="preview-title">Summary 摘要</label>
                  <div id="preview-summary" class="container">
                  </div>
                  <hr/>
                </div>
                <div class="order-input">
                  <input type="hidden" id="product-total-fee" name="product-total-fee">
                  <input type="hidden" id="storage-month" name="storage-month">
                  <input type="hidden" id="delivery-service-fee" name="delivery-service-fee">
                  <input type="hidden" id="total-fee" name="total-fee">
                </div>
              </div>
            </fieldset>
            <h3></h3>
            <fieldset>
              <div class="container form-radio m-0 p-0">
                @foreach($paymentTypes as $paymentType)
                  <div class="row form-group mx-0">
                    <div class="col-md-12 mb-3 mb-md-0">
                      <div class="form-radio-item">
                        <input type="radio" id="order_payment_type_{{ $paymentType->id }}" name="order_payment_type" value="{{ $paymentType->id }}" {{ ($loop->first)?'checked':'' }}>
                        <label id="order_payment_type_label" class="font-weight-bold" for="order_payment_type_{{ $paymentType->id }}">{{ $paymentType->description }}</label>
                        <span class="check"></span>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div id="payment-info" class="payment-info">
                <label>付款資訊</label>
                <div class="form-group">
                  <div id="card-element">
                  </div>
                  <div id="card-errors" class="error" role="alert"></div>
                </div>
              </div>
            </fieldset>
          </div>
        </form>
      @endif
    </div>
  </div>
</div>
@stop

@section('page-css')
<link rel="stylesheet" href="{{ asset('vendor/colorlib-wizard-19/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/product-item.css') }}">
<style>
  .error{
    font-size: 10px;
    color: #8a1f11;
  }

  .steps ul {
    display: flex;
    position: relative;
    margin-top: 62px; }
    @media (min-width: 1024px) {
      .steps ul {
        margin-top: 38px; }
    }
    .steps ul li {
      width: 25%;
      margin-right: 10px; }
      .steps ul li a {
        display: inline-block;
        width: 100%;
        height: 7px;
        background: #999999;
        padding: 0px;
        border-radius: 3.5px; }
      .steps ul li.first a, .steps ul li.checked a {
        background: #ffc400;
        transition: all 0.5s ease; }

    .steps ul:before {
      font-weight: bold;
      content: "Select Product 選擇貨品";
      font-size: 22px;
      color: #333;
      top: -62px;
      position: absolute; }
      @media (min-width: 1024px) {
        .steps ul:before {
          top: -38px; }
      }
    .steps ul.step-2:before {
      content: "Checkin/out Detail 交收詳情"; }
    .steps ul.step-3:before {
      content: "Preview Order 檢視訂單"; }
    .steps ul.step-4:before {
      content: "Payment Method 付款方式"; }

    .success-title {
        font-size: 22px;
        font-family: Poppins-SemiBold;
        color: rgb(51, 51, 51);
        /* margin-top: -62px; */
        margin-bottom: 30px;
    }
      @media (min-width: 1024px) {
        .success-title {
          margin-top: -38px;
        }
      }
</style>
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

    $('input[type=radio][name=checkin_date_type]').change(function() {
        if ($(this).val() == 'default') {
            $('#checkin_date').prop('disabled', false);
            $('#checkin_date').prop('required', true);
            $('#checkin_date_other').prop('disabled', true);
            $('#checkin_date_other').prop('required', false);
            $('#checkin_time_other').prop('disabled', true);
            $('#checkin_time_other').prop('required', false);
        }
        else if ($(this).val() == 'other') {
            $('#checkin_date').prop('disabled', true);
            $('#checkin_date').prop('required', false);
            $('#checkin_date_other').prop('disabled', false);
            $('#checkin_date_other').prop('required', true);
            $('#checkin_time_other').prop('disabled', false);
            $('#checkin_time_other').prop('required', true);
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

    $('input[type=radio][name=checkout_date_type]').change(function() {
        if ($(this).val() == 'default') {
            $('#checkout_date').prop('disabled', false);
            $('#checkout_date').prop('required', true);
            $('#checkout_date_other').prop('disabled', true);
            $('#checkout_date_other').prop('required', false);
            $('#checkout_time_other').prop('disabled', true);
            $('#checkout_time_other').prop('required', false);
        }
        else if ($(this).val() == 'other') {
            $('#checkout_date').prop('disabled', true);
            $('#checkout_date').prop('required', false);
            $('#checkout_date_other').prop('disabled', false);
            $('#checkout_date_other').prop('required', true);
            $('#checkout_time_other').prop('disabled', false);
            $('#checkout_time_other').prop('required', true);
        }
    });
  });
</script>
<script src="https://js.stripe.com/v3/"></script>
<script>
  var form = $("#order-create-form");

  $(function(){
    $('input[type=radio][name=order_payment_type]').change(function() {
      if ($(this).val() == '3') {
        $('#payment-info').show();
      }else{
        $('#payment-info').hide();
      }
    });
    // Create a Stripe client.
    // var stripe = Stripe('{{ env("STRIPE_TEST_KEY") }}');
    var stripe = Stripe('{{ env("STRIPE_KEY") }}');
    // Create an instance of Elements.
    var elements = stripe.elements();
    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
      base: {
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    };
    // Create an instance of the card Element.
    var card = elements.create('card', {style: style, hidePostalCode: true});
    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');
    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
      var displayError = document.getElementById('card-errors');
      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = '';
      }
    });
    // Handle form submission.
    var submitHandler = function( event ) {
      if($('input[type=radio][name=order_payment_type]:checked').val() == '3')
      {
        event.preventDefault();
        stripe.createToken(card).then(function(result) {
          if (result.error) {
            // Inform the user if there was an error.
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
          } else {
            // Send the token to your server.
            stripeTokenHandler(result.token);
          }
        });
      }
    }

    form.bind("submit", submitHandler);

  });
  // Submit the form with the token ID.
  function stripeTokenHandler(token) {
    console.log('on stripeTokenHandler');
    // Insert the token ID into the form so it gets submitted to the server
    var paymentform = document.getElementById('order-create-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    paymentform.appendChild(hiddenInput);
    // Submit the form
    paymentform.submit();
  }
</script>
@component('components.wechatpay-success-auto-redirect')
@endcomponent
@stop

@section('step-js')
<!-- <script src="{{ asset('vendor/colorlib-wizard-19/js/main.js') }}"></script> -->
<script src="{{ asset('js/orderPreview.js') }}"></script>
<script>
  (function($) {

  })(jQuery);

  var form = $("#order-create-form");
  form.children("div").steps({
      headerTag: "h3",
      bodyTag: "fieldset",
      transitionEffect: "fade",
      labels: {
          previous : 'Prev',
          next : 'Next',
          finish : 'Finish',
          current : ''
      },
      titleTemplate : '<h3 class="title">#title#</h3>',
      onStepChanging: function (event, currentIndex, newIndex) {
          console.log('onStepChanging form '+currentIndex+' to '+newIndex);

          if(currentIndex > newIndex){
            valid = true;
          }
          else{
            form.validate().settings.ignore = ":disabled,:hidden";
            valid = form.valid();
          }

          if(valid)
          {
            if ( currentIndex < newIndex && newIndex === 1 ) {
              initLocation('{{ ($haveLocation ?? false)? true:false }}');
              initDate('{{ ($haveDatetime ?? false)? true:false }}');
            }
            else if ( currentIndex < newIndex && newIndex === 2 ) {
              resetOrderPreview();
              createOrderPreview();
            }

            $('.steps ul').removeClass('step-'+(currentIndex+1));
            $('.steps ul').addClass('step-'+(newIndex+1));
          }

          return valid;
      },
      onFinishing: function (event, currentIndex)
      {
          form.validate().settings.ignore = ":disabled";
          return form.valid();
      },
      onFinished: function (event, currentIndex)
      {
          // alert('Sumited');
          console.log('submitHandler : '+$('input[type=radio][name=order_payment_type]:checked').val());
          form.submit();
      }
  });
  // Custom Steps Jquery Steps
  $('.wizard > .steps li a').click(function(){
    $(this).parent().addClass('checked');
    $(this).parent().prevAll().addClass('checked');
    $(this).parent().nextAll().removeClass('checked');
  });

  $(function(){
    refreshPrice();

    $(".product-qty-plus").click(function(){
        // console.log('target input id : '+$(this).next('input').attr('id'));
        var qtyInput = $(this).next('input');
        var currVal = (qtyInput.val() === '' || isNaN(qtyInput.val()))? 0:parseFloat(qtyInput.val());

        if(currVal < 99){
          $(this).next('input').val(currVal+1);
          refreshPrice();
        }
    });

    $(".product-qty-minus").click(function(){
        // console.log('target input id : '+$(this).prev('input').attr('id'));
        var qtyInput = $(this).prev('input');
        var currVal = (qtyInput.val() === '' || isNaN(qtyInput.val()))? 0:parseFloat(qtyInput.val());

        if(currVal > 0){
          $(this).prev('input').val(currVal-1);
          refreshPrice();
        }
    });

    $(".product-qty-input").on('change',function(e){
      var input = $(this);
      var prevVal = input.data('old');
      var currVal = input.val();

      if(currVal < 0 || currVal > 99){
        // Invalid Value! Get old value
        input.val(prevVal);
      }else{
        input.data('old', currVal);
        refreshPrice();
      }

    });
  });

  function resetPrice(){
    // Select Product Qty = 0
    $(".product-qty-input").each(function() {
      $(this).val(0);
    });
    //
    $(".product-price-label").each(function() {
      $(this).html($(this).data('original-price'));
    });
    //
    $('#monthly-price').html(0);
    $('#other-price').html(0);
  }

  function refreshPrice(){
    var monthlyPrice = 0;
    var otherPrice = 0;
    var itemQty = 0;
    var itemPrice = 0;

    $(".product-item").each(function() {
        var itemId = $(this).data('id');
        var itemCategory = $(this).data('category');

        itemQty = $('#product-qty-'+itemId).val();
        itemPrice = $('#product-'+itemId+'-price').html();
        // console.log('select product sub-total : '+(itemQty * itemPrice));
        if('box' === itemCategory){
          monthlyPrice += itemQty * itemPrice;
        }else{
          otherPrice += itemQty * itemPrice;
        }
    });
    // console.log('Total : '+total);
    $('#monthly-price').html(monthlyPrice);
    $('#other-price').html(otherPrice);
  }

</script>
@stop

@section('validate-js')
<script>
  var form = $("#order-create-form");
  form.validate({
    // rules: {
    //
    // },
    // messages: {
    //
    // },
    errorPlacement: function(error, element) {
      console.log('error :'+error.toString());
      if (element.attr('class').includes('product-category-box')) {
        error.appendTo($('#product-'+element.data('id')+'-error'));
      }
      else {
        error.insertAfter(element);
      }
    }
  });

  $.validator.addMethod("havebox", function(value, element) {
    var total = 0;

    $(".product-category-box").each(function() {
      total += parseInt($(this).val());
    });
    // console.log(total);
    return total > 0;

  }, "Please select at least one box.");

  $.validator.addClassRules("product-category-box", {
    havebox:true
  });

  $.validator.addMethod("validtimerange", function(value, element) {
    var checkindateselect = $('#checkin_date option:selected');
    var checkinotherdate = $('#checkin_date_other').val();
    var checkoutdateselect = $('#checkout_date option:selected');
    var checkoutotherdate = $('#checkout_date_other').val();

    var checkin;
    var checkout;
    console.log('checkindateselectval : '+checkindateselect.val());
    console.log('checkoutdateselect : '+checkindateselect.val());

    if(!$('#checkin_date').is(':disabled') && checkindateselect.val() != ''){
      checkin = new Date(checkindateselect.text().trim().split(" ")[0]);
      console.log('checkindate : '+checkindateselect.text().trim());
      console.log('checkindate : '+checkindateselect.text().trim().split(" ")[0]);
    }else{
      checkin = new Date(checkinotherdate);
      // console.log('checkindate : '+checkinotherdate);
    }
    if(!$('#checkout_date').is(':disabled') && checkoutdateselect.val() != ''){
      checkout = new Date(checkoutdateselect.text().trim().split(" ")[0]);
      console.log('checkoutdate : '+checkoutdateselect.text().trim());
      console.log('checkoutdate : '+checkoutdateselect.text().trim().split(" ")[0]);
    }else{
      checkout = new Date(checkoutotherdate);
      // console.log('checkoutdate : '+checkoutotherdate);
    }
    console.log('checkin Time : '+checkin.getTime());
    console.log('checkout Time : '+checkout.getTime());

    if(isNaN(checkout.getTime()) || isNaN(checkin.getTime()))
      return true;
    return checkout.getTime() > checkin.getTime();

  }, "Checkout date must later then checkin date.");

  $.validator.addClassRules("checkinout_date", {
    validtimerange:true
  });

  function initLocation( haveDefaultLoc ){
    console.log('initLocation haveDefaultLoc'+haveDefaultLoc);

    if( haveDefaultLoc ) {
      $('#checkin_location_type_default').prop('checked', true).change();
      $('#checkout_location_type_default').prop('checked', true).change();
    }else{
      $('#checkin_location_type_default').prop('disabled', true);
      $('#checkin_location_type_other').prop('checked', true).change();

      $('#checkout_location_type_default').prop('disabled', true);
      $('#checkout_location_type_other').prop('checked', true).change();
    }
  }

  function initDate( haveDefaultDate ){
    console.log('initDate haveDefaultDate'+haveDefaultDate);

    if( haveDefaultDate ) {
      $('#checkin_date_type_default').prop('checked', true).change();
      $('#checkout_date_type_default').prop('checked', true).change();
    }else{
      $('#checkin_date_type_default').prop('disabled', true);
      $('#checkin_date_type_other').prop('checked', true).change();

      $('#checkout_date_type_default').prop('disabled', true);
      $('#checkout_date_type_other').prop('checked', true).change();
    }
  }
</script>
@stop
