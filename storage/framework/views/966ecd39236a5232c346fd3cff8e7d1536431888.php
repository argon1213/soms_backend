<?php if($errors->any()): ?>
  <div class="bg-light p-3">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-danger">
            <ul>
              <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error, false); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php /**PATH /var/www/html/soms_uat/soms/resources/views/components/validator.blade.php ENDPATH**/ ?>