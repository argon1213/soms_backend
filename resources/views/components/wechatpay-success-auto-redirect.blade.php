@if (Session::has('qr_code_url'))
  <script>
    var interval = 1000;
    function doAjax(){
      $.get("{{ route('ajax-get-order-payment-status') }}", { code: "{{ Session::get('payment_code') }}" })
        .done(function (data){
          // success data
          console.log("{{ Session::get('payment_code') }} : "+data);
          if(data == 2){
            window.location.replace("{{ route('wechatpay-success') }}?name={{ Route::currentRouteName() }}&code={{ Session::get('payment_code') }}");
          }
        })
        .fail(function() {})
        .always(function() {
          setTimeout(doAjax, interval);
        });
    }
    setTimeout(doAjax, interval);
  </script>
@endif
