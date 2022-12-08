

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
          <p class="mb-0 font-weight-bold"><?php echo e($item->item->name_cn, false); ?></p>
          <p class="mb-4">
            <b><?php echo e($item->item_qty, false); ?></b> (個)
            <b>HKD <?php echo e($item->item_price, false); ?></b>
          </p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <div class="p-4 mb-3 bg-white">
        <p class="mb-0 font-weight-bold">總收費 Total Fee</p>
        <p class="mb-4">HKD <?php echo e($order->total_fee, false); ?></p>
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
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vendor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/soms/resources/views/somsclient/order-update-new.blade.php ENDPATH**/ ?>