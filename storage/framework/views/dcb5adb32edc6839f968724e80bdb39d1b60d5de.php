<div <?php echo $attributes; ?>>
    <div class="inner">
        <h3><?php echo e($info, false); ?></h3>

        <p><?php echo e($name, false); ?></p>
    </div>
    <div class="icon">
        <i class="fa fa-<?php echo e($icon, false); ?>"></i>
    </div>
    <a href="<?php echo e($link, false); ?>" class="small-box-footer">
        <?php echo e(trans('admin.more'), false); ?>&nbsp;
        <i class="fa fa-arrow-circle-right"></i>
    </a>
</div><?php /**PATH /var/www/html/soms/vendor/encore/laravel-admin/src/../resources/views/widgets/info-box.blade.php ENDPATH**/ ?>