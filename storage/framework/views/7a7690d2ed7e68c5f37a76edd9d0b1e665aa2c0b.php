<div class="unit-5 overlay" style="background-image: url(<?php echo e(asset('vendor/authsomsclient/images/hero_bg_2.jpg'), false); ?>);">
  <div class="container text-center">
    <h2 class="mb-0"><?php echo app('translator')->get('navbar.'.end($routeLink)); ?></h2>
    <p class="mb-0 unit-6">
      <a href="<?php echo e(route('index'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.book'); ?></a>
      <?php $__currentLoopData = $routeLink; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="sep">></span>
        <?php if($loop->last): ?>
          <span><?php echo app('translator')->get('navbar.'.$route); ?></span>
        <?php else: ?>
          <a href="<?php echo e(route($route), false); ?>"><?php echo app('translator')->get('navbar.'.$route); ?></a>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </p>
  </div>
</div>
<?php /**PATH /var/www/html/soms/resources/views/components/somsclient-banner.blade.php ENDPATH**/ ?>