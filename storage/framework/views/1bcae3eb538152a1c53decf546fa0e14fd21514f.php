<?php if(session('message')): ?>
  <div class="bg-light p-3">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="alert <?php echo e(session('alert-class', 'alert-info'), false); ?>">
            <?php echo e(session('message'), false); ?>

          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php /**PATH /var/www/html/soms_uat/soms/resources/views/components/session.blade.php ENDPATH**/ ?>