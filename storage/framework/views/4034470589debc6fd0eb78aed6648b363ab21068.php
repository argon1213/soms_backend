<?php if(Session::has('qr_code_url')): ?>
  <script>
    var interval = 1000;
    function doAjax(){
      $.get("<?php echo e(route('ajax-get-order-payment-status'), false); ?>", { code: "<?php echo e(Session::get('payment_code'), false); ?>" })
        .done(function (data){
          // success data
          console.log("<?php echo e(Session::get('payment_code'), false); ?> : "+data);
          if(data == 2){
            window.location.replace("<?php echo e(route('wechatpay-success'), false); ?>?name=<?php echo e(Route::currentRouteName(), false); ?>&code=<?php echo e(Session::get('payment_code'), false); ?>");
          }
        })
        .fail(function() {})
        .always(function() {
          setTimeout(doAjax, interval);
        });
    }
    setTimeout(doAjax, interval);
  </script>
<?php endif; ?>
<?php /**PATH /var/www/html/soms_uat/soms/resources/views/components/wechatpay-success-auto-redirect.blade.php ENDPATH**/ ?>