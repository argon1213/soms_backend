<?php
  if($model instanceof App\SomsOrder)
  {
    $lang_prefix    = 'somsorder';
    $location_title = $type.'_location_other';
    $city_title     = $type.'_city_id';
    $state_title    = $type.'_state_id';

    $valid = true;
  }
  else if($model instanceof App\SomsClient)
  {
    $lang_prefix    = 'somsclient';
    $location_title = 'address1';
    $city_title     = 'city_id';
    $state_title    = 'state_id';

    $valid = true;
  }
  else
  {
    $valid = false;
  }
?>
<?php if($valid): ?>
<div class="row form-group">
  <div class="col-md-12 mb-3 mb-md-0">
    <label class="font-weight-bold" for="<?php echo e($location_title, false); ?>">
      <?php echo e(__($lang_prefix.'.'.$location_title), false); ?>

    </label>
    <input class="form-control" type="text" id="<?php echo e($location_title, false); ?>" name="<?php echo e($location_title, false); ?>" value="<?php echo e(old($location_title)? old($location_title):$model->{$location_title}, false); ?>">
  </div>
  <div class="col-md-6 mb-3 mb-md-0">
    <label class="font-weight-bold" for="<?php echo e($city_title, false); ?>">
      <?php echo e(__($lang_prefix.'.'.$city_title), false); ?>

    </label>
    <select class="form-control" id="<?php echo e($city_title, false); ?>" name="<?php echo e($city_title, false); ?>">
      <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($city->id, false); ?>" <?php echo e(($model->{$city_title} == $city->id)?'selected':'', false); ?>>
          <?php echo e($city->name_cn, false); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
  <div class="col-md-6 mb-3 mb-md-0">
    <label class="font-weight-bold" for="<?php echo e($state_title, false); ?>">
      <?php echo e(__($lang_prefix.'.'.$state_title), false); ?>

    </label>
    <select class="form-control" id="<?php echo e($state_title, false); ?>" name="<?php echo e($state_title, false); ?>">
      <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($state->id, false); ?>" <?php echo e(($model->{$state_title} == $state->id)?'selected':'', false); ?>>
          <?php echo e($state->name_cn, false); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
</div>
<?php endif; ?>
<?php /**PATH /var/www/html/soms/resources/views/components/somsclient-order-location.blade.php ENDPATH**/ ?>