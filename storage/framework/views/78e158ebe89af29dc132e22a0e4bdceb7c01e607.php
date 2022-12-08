<div class="modal" tabindex="-1" role="dialog" id="<?php echo e($modal_id, false); ?>">
    <div class="modal-dialog <?php echo e($modal_size, false); ?>" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo e($title, false); ?></h4>
            </div>
            <form>
            <div class="modal-body">
                <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $field->render(); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(__('admin.close'), false); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo e(__('admin.submit'), false); ?></button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --><?php /**PATH /var/www/html/soms/vendor/encore/laravel-admin/src/../resources/views/actions/form/modal.blade.php ENDPATH**/ ?>