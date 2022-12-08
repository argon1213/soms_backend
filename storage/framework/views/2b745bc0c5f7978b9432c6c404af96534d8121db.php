<?php
  $date_title = $type.'_date_other';
  $time_title = $type.'_time_other';
?>
<div class="row form-group">
  <div class="col-md-8 mb-3 mb-md-0">
    <label class="font-weight-bold" for="<?php echo e($date_title, false); ?>">
      <?php echo e(__('somsorder.'.$date_title), false); ?>

    </label>
    <input class="form-control" type="date" id="<?php echo e($date_title, false); ?>" name="<?php echo e($date_title, false); ?>" value="<?php echo e($model->{$date_title}, false); ?>" >
  </div>
  <div class="col-md-4 mb-3 mb-md-0">
    <label class="font-weight-bold" for="<?php echo e($time_title, false); ?>">
      <?php echo e(__('somsorder.'.$time_title), false); ?>

    </label>
    <select class="form-control form-control-sm" id="<?php echo e($time_title, false); ?>" name="<?php echo e($time_title, false); ?>" >
      <option value="09:00am - 12:00noon" <?php echo e(($model->{$time_title} == '09:00am - 12:00noon')?'selected':'', false); ?>>09:00am - 12:00noon</option>
      <option value="02:00pm - 05:00pm" <?php echo e(($model->{$time_title} == '02:00pm - 05:00pm')?'selected':'', false); ?>>02:00pm - 05:00pm</option>
      <option value="07:00pm - 09:00pm" <?php echo e(($model->{$time_title} == '07:00pm - 09:00pm')?'selected':'', false); ?>>07:00pm - 09:00pm</option>
    </select>
  </div>
</div>
<?php /**PATH /var/www/html/soms/resources/views/components/somsclient-order-datetime.blade.php ENDPATH**/ ?>