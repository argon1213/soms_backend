

<?php $__env->startSection('title'); ?>
<?php echo app('translator')->get('navbar.somsclient.dashboard'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('banner'); ?>
  <?php $__env->startComponent('components.somsclient-banner', ['routeLink'=>['somsclient.dashboard']]); ?>
  <?php echo $__env->renderComponent(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('banner2'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="row justify-content-start text-left mb-5">
    <div class="col-md-12" data-aos="fade">
      <h2 class="font-weight-bold text-black">尊敬的 <?php echo e(Auth::guard('somsclient')->user()->name, false); ?> 先生/女士，您好！歡迎登錄 <?php echo app('translator')->get('common.company.nickname'); ?> 官方網站。</h2>
    </div>
  </div>
  <div class="row justify-content-start text-left mb-5">
    <div class="col-md-9" data-aos="fade">
      <h2 class="font-weight-bold text-black">Recent Orders 交易記錄</h2>
    </div>
    <div class="col-md-3" data-aos="fade" data-aos-delay="200">
      <a href="<?php echo e(route('index'), false); ?>" class="btn btn-primary py-3 btn-block"><span class="h5">+</span><?php echo app('translator')->get('somsclient.create.order'); ?></a>
    </div>
  </div>
  <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row" data-aos="fade">
     <div class="col-md-12">
       <div class="job-post-item bg-white p-4 d-block d-md-flex align-items-center">

         <div class="mb-4 mb-md-0 mr-5">
          <div class="job-post-item-header d-flex align-items-center">
            <h2 class="mr-3 text-black h4"><?php echo e($order->code, false); ?></h2>
          </div>
          <div class="job-post-item-header d-flex align-items-center">
            <div class="badge-wrap">
             <span class="bg-primary text-white badge py-2 px-4">Fee HKD$<?php echo e($order->total_fee, false); ?></span> &nbsp;
            </div>
            <div class="badge-wrap">
             <span class="bg-primary text-white badge py-2 px-4">Paid HKD$<?php echo e($order->paid_fee, false); ?></span> &nbsp;
            </div>
            <div class="badge-wrap">
             <span class="bg-primary text-white badge py-2 px-4">Balance HKD$<?php echo e($order->total_fee - $order->paid_fee, false); ?></span>
            </div>
          </div>
          <div class="job-post-item-body d-block d-md-flex">
            <div class="mr-3">
              <span class="fl-bigmug-line-portfolio23"></span>
              <span>Pay By <?php echo e($order->paymentType->description, false); ?></span>
            </div>
            <div>
              <span class="fl-bigmug-line-note35"></span>
              <span><?php echo e($order->status->description, false); ?></span>
            </div>
          </div>
          <div class="job-post-item-body d-block d-md-flex">
            <div class="mr-3">
              <span class="fl-bigmug-line-shopping202"></span>
              <span><?php echo e($order->created_at, false); ?></span>
            </div>
          </div>
         </div>
         <div class="ml-auto">
          <a href="<?php echo e(route('somsclient.order.update',['id'=>$order->id]), false); ?>" class="btn btn-primary py-2"><?php echo app('translator')->get('somsclient.update.order'); ?></a>
          <a href="<?php echo e(route('somsclient.payment-invoice.send',['id'=>$order->id]), false); ?>" class="btn btn-secondary py-2" style="background-color:#e67176; color:white;"><?php echo app('translator')->get('somsclient.payment-invoice.send'); ?></a>
          <a href="<?php echo e(route('somsclient.payment-invoice.view',['id'=>$order->id]), false); ?>" class="btn btn-success py-2" style="background-color:#5ec4b6; color:white;"><?php echo app('translator')->get('somsclient.payment-invoice.view'); ?></a>
         </div>
       </div>
       <div class="job-post-item bg-white p-4 d-block d-md-flex align-items-center">                                                         
          <span><?php echo e('QR Code : '.$order->remark_qrcode, false); ?></span>
       </div>
     </div>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <div class="justify-content-center mt-3" style="display:flex;">
    <?php echo e($orders->links(), false); ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content2'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-css'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vendor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/soms_uat/soms/resources/views/somsclient/dashboard.blade.php ENDPATH**/ ?>