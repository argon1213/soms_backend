<div class="form-group">
    <label><?php echo e($label, false); ?></label>
    <input <?php echo $attributes; ?>>
    <?php echo $__env->make('admin::actions.form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div><?php /**PATH /var/www/html/soms/vendor/encore/laravel-admin/src/../resources/views/actions/form/text.blade.php ENDPATH**/ ?>