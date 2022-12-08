

<?php $__env->startSection('title'); ?>
<?php echo app('translator')->get('navbar.somsclient.order.update'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('banner'); ?>
  <?php $__env->startComponent('components.somsclient-banner', ['routeLink'=>['somsclient.dashboard','somsclient.order.update']]); ?>
  <?php echo $__env->renderComponent(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="row">
    <div class="col-lg-4">
      <div class="p-4 mb-3 bg-white">
        <p class="mb-0 font-weight-bold">訂單號碼:</p>
        <p class="mb-4"><?php echo e($order->code, false); ?></p>

        <p class="mb-0 font-weight-bold">姓名 Name</p>
        <p class="mb-4"><?php echo e($order->client->name, false); ?></p>

        <p class="mb-0 font-weight-bold">電郵地址 Email Address</p>
        <p class="mb-4"><?php echo e($order->client->email, false); ?></p>

        <?php if($order->client->university_id != null && $order->client->university_id > 0): ?>
          <p class="mb-0 font-weight-bold">學生證號碼 Student ID No.</p>
          <p class="mb-4"><?php echo e($order->client->student_id, false); ?></p>

          <p class="mb-0 font-weight-bold">Wechat ID</p>
          <p class="mb-0"><?php echo e($order->client->wechat, false); ?></p>
        <?php endif; ?>

      </div>

      <div class="p-4 mb-3 bg-white">
        <p class="h4 text-black mb-3 font-weight-bold">產品明細</p>

        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if($item->item->category == "box"): ?>
            <p class="mb-0 font-weight-bold"><?php echo e($item->item->name_cn, false); ?></p>
            <p class="mb-4">
              <b><?php echo e($item->item_qty, false); ?></b> (個)
              <b>HKD <?php echo e($item->item_price, false); ?></b>
            </p>
          <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <p class="mb-0 font-weight-bold">月費</p>
        <p class="mb-4">
          <b>HKD <?php echo e($order->getMonthlyFee(), false); ?></b>
        </p>
        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if($item->item->category != "box"): ?>
            <p class="mb-0 font-weight-bold"><?php echo e($item->item->name_cn, false); ?></p>
            <p class="mb-4">
              <b><?php echo e($item->item_qty, false); ?></b> (個)
              <b>HKD <?php echo e($item->item_price, false); ?></b>
            </p>
          <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <p class="mb-0 font-weight-bold">其他費用</p>
        <p class="mb-4">
          <b>HKD <?php echo e($order->getOtherFee(), false); ?></b>
        </p>
      </div>

      <div class="p-4 mb-3 bg-white">
        <p class="mb-0 font-weight-bold">總收費 Total Fee</p>
        <p class="mb-4">HKD <?php echo e($order->total_fee, false); ?></p>

        <?php if($order->incompletePayment()): ?>
          <p class="mb-0 font-weight-bold">已付 Paid Fee</p>
          <p class="mb-4">HKD <?php echo e($order->paid_fee, false); ?></p>

          <p class="mb-0 font-weight-bold">未付 Unpaid Fee</p>
          <p class="mb-4">HKD <?php echo e($order->incompletePayment()->amount, false); ?></p>
        <?php else: ?>
          <p class="mb-0 font-weight-bold">沒有未付款項</p>
        <?php endif; ?>

      </div>
    </div>

    <div class="col-md-12 col-lg-8 mb-5">
      <form method="post" action="<?php echo e(route('somsclient.order.update.submit', ['id'=>$order->id]), false); ?>" class="p-5 bg-white">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="code" name="code" value="<?php echo e($order->code, false); ?>">
        <!-- Emptyout -->
        <?php $__env->startComponent('components.somsclient-order-location', ['type'=>'emptyout','model'=>$order,'cities'=>$cities,'states'=>$states]); ?>
        <?php echo $__env->renderComponent(); ?>
        <?php $__env->startComponent('components.somsclient-order-datetime', ['type'=>'emptyout','model'=>$order]); ?>
        <?php echo $__env->renderComponent(); ?>
        <!-- Checkin -->
        <?php $__env->startComponent('components.somsclient-order-location', ['type'=>'checkin','model'=>$order,'cities'=>$cities,'states'=>$states]); ?>
        <?php echo $__env->renderComponent(); ?>
        <?php $__env->startComponent('components.somsclient-order-datetime', ['type'=>'checkin','model'=>$order]); ?>
        <?php echo $__env->renderComponent(); ?>
        <!-- Checkout -->
        <?php $__env->startComponent('components.somsclient-order-location', ['type'=>'checkout','model'=>$order,'cities'=>$cities,'states'=>$states]); ?>
        <?php echo $__env->renderComponent(); ?>
        <?php $__env->startComponent('components.somsclient-order-datetime', ['type'=>'checkout','model'=>$order]); ?>
        <?php echo $__env->renderComponent(); ?>

        <!--
          Show Storage Period
          Show New Storage Period
          Show Price Changed
        -->
        <div class="row form-group">
          <div class="col-md-6 mb-3 mb-md-0">
            <label class="font-weight-bold" for="storage_month">
              <?php echo e(__('somsorder.storage_month'), false); ?>

            </label>
            <input class="form-control" type="text" id="storage_month" name="storage_month" value="<?php echo e($order->storage_month, false); ?>" readonly>
          </div>
          <div class="col-md-6 mb-3 mb-md-0">
            <label class="font-weight-bold" for="storage_month">
              <?php echo e(__('somsorder.storage_month'), false); ?> (更改日期後)
            </label>
            <input class="form-control" type="text" id="storage_month_preview" name="storage_month_preview" value="<?php echo e($order->storage_month, false); ?>" readonly>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
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

    var fee = (newStorageMonth - currStorageMonth) * <?php echo e($order->getMonthlyFee(), false); ?>;
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vendor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/soms_uat/soms/resources/views/somsclient/order-update-new.blade.php ENDPATH**/ ?>