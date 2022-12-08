<style>
  .modal-dialog {
    max-width: 720px;
  }

  .wrap-modal-slider {
    padding: 0 30px;
    opacity: 0;
    transition: all 0.3s;
  }

  .wrap-modal-slider.open {
    opacity: 1;
  }

  .slick-prev:before, .slick-next:before {
    color: black;
  }

  .product-detail-title{
    color: #FFC400;
    font-size: 16px;
    font-weight: bold;
  }

  .product-detail-text{
    color: black;
    font-size: 16px;
  }
</style>
<!-- Modal -->
<div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog" aria-labelledby="productDetailModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h5 class="modal-title" id="productDetailModalTitle">slick slider inside modal bootstrap</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> -->
      <div class="modal-body">
        <div class="wrap-modal-slider">
          <div class="product-detail-div">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="container">
                <div class="row p-0">
                  <div class="col-5 py-4">
                    <div class="row">
                      <div class="col-12">
                        <h6 id="product-<?php echo e($item->id, false); ?>-name" style="font-weight: bold; font-size:20px;">
                          <?php echo e((session('locale') == 'en')?$item->name:$item->name_cn, false); ?>

												</h6>
                        <hr style="border-color:black;"/>
                      </div>
                      <div class="col-12">
                        <span class="pb-1" style="font-size:14px; color: #949494;"><?php echo e((session('locale') == 'en')?$item->description:$item->description_cn, false); ?></span>
                      </div>
                      <div class="col-12 p-3" style="min-height: 200px; text-align:center; display:block;">
                        <img class="thumb" style="max-height:100px;" src="<?php echo e(asset('storage'.$item->uri), false); ?>" alt="">
                      </div>
                    </div>
                  </div>
                  <div class="col-7 py-4" style="background-color: #fafafa;">
                    <div class="row mb-2">
                      <div class="col-4 product-detail-title">
                        <?php echo app('translator')->get('common.minStoragePeriod'); ?>
                      </div>
                      <div class="col-8 product-detail-text">
                        3 <?php echo app('translator')->get('common.months'); ?>
                      </div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4 product-detail-title">
                        <?php echo app('translator')->get('common.monthlyFee'); ?>
                      </div>
                      <div class="col-8 product-detail-text">
                        HKD<?php echo e($item->price, false); ?> / <?php echo app('translator')->get('common.m'); ?>
                      </div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4 product-detail-title">
                        <?php echo app('translator')->get('common.collection'); ?>
                      </div>
                      <div class="col-8 product-detail-text">
                        <?php echo app('translator')->get('common.firstTripFee'); ?><br/>
                        <?php echo app('translator')->get('common.subsequentFee'); ?> HK$29 + HK$29 <?php echo app('translator')->get('common.perEachBox'); ?>
                      </div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4 product-detail-title">
                        <?php echo app('translator')->get('common.retrieval'); ?>
                      </div>
                      <div class="col-8 product-detail-text">
                        <?php echo app('translator')->get('common.firstTripFee'); ?><br/>
                        <?php echo app('translator')->get('common.subsequentFee'); ?> HK$29 + HK$29 <?php echo app('translator')->get('common.perEachBox'); ?>
                      </div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4 product-detail-title">
                        <?php echo app('translator')->get('common.deliveryTime'); ?>
                      </div>
                      <div class="col-8 product-detail-text">
                        24 <?php echo app('translator')->get('common.hours'); ?>
                      </div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4 product-detail-title">
                        <?php echo app('translator')->get('common.protection'); ?>
                      </div>
                      <div class="col-8 product-detail-text">
                        HK$1,000 <?php echo app('translator')->get('common.perEachBox'); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>
<script type="text/javascript">
  //
  $(document).ready(function(){
    $('.product-detail-div').slick({
      arrows: true,
      // prevArrow:"<button type='button' class='slick-prev pull-left'><i class='fa fa-arrow-left' aria-hidden='true'></i></button>",
      // nextArrow:"<button type='button' class='slick-next pull-right'><i class='fa fa-arrow-right' aria-hidden='true'></i></button>"
    });
  });
  //
  $('#productDetailModal').on('shown.bs.modal', function (e) {
    $('.product-detail-div').slick('setPosition', {
      arrows: true,
      // prevArrow:"<button type='button' class='slick-prev pull-left'><i class='fa fa-angle-left' aria-hidden='true'></i></button>",
      // nextArrow:"<button type='button' class='slick-next pull-right'><i class='fa fa-angle-right' aria-hidden='true'></i></button>"
    });
    $('.wrap-modal-slider').addClass('open');
  })
</script>
<?php /**PATH /var/www/html/soms/resources/views/modal-product-detail.blade.php ENDPATH**/ ?>