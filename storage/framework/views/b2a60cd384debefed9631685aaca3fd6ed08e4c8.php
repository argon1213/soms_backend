<div class="col-10 col-xl-10 d-none d-xl-block">
  <nav class="site-navigation text-right" role="navigation">

    <ul class="site-menu js-clone-nav mr-auto d-none d-lg-block">
      <li class="active"><a href="<?php echo e(route('index'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.book'); ?></a></li>
      <?php if(auth()->guard('somsclient')->guest()): ?>
        <li><a href="<?php echo e(route('somsclient.login'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.login'); ?></a></li>
      <?php else: ?>
        <li><a href="<?php echo e(route('somsclient.dashboard'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.dashboard'); ?></a></li>
        <li><a href="<?php echo e(route('somsclient.client.update'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.client.update'); ?></a></li>
        <li><a href="<?php echo e(route('somsclient.logout'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.logout'); ?></a></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>
<?php /**PATH /var/www/html/soms_uat/soms/resources/views/components/somsclient-navbar.blade.php ENDPATH**/ ?>