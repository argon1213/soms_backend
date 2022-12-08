<div class="input-group input-group-sm">
    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php echo $inline ? '<span class="icheck">' : '<div class="checkbox icheck">'; ?>


        <label <?php if($inline): ?>class="checkbox-inline"<?php endif; ?>>
            <input type="checkbox" class="<?php echo e($id, false); ?>" name="<?php echo e($name, false); ?>[]" value="<?php echo e($option, false); ?>" class="minimal" <?php echo e(in_array((string)$option, request($name, is_null($value) ? [] : $value)) ? 'checked' : '', false); ?> />&nbsp;<?php echo e($label, false); ?>&nbsp;&nbsp;
        </label>

        <?php echo $inline ? '</span>' :  '</div>'; ?>


    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div><?php /**PATH /var/www/html/soms/vendor/encore/laravel-admin/src/../resources/views/filter/checkbox.blade.php ENDPATH**/ ?>