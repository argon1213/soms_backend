<?php $__env->startSection('title'); ?>
<?php echo app('translator')->get('navbar.somsclient.login'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('banner'); ?>
  <?php $__env->startComponent('components.somsclient-banner', ['routeLink'=>['somsclient.login']]); ?>
  <?php echo $__env->renderComponent(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('validator'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <style>  #loader{display: none}</style>
  <link rel="stylesheet" href="<?php echo e(asset('vendor/slick/custom.css'), false); ?>">
  <div class="site-section bg-white">
    <div class="container">
      <div class="row">
        <div class="col-md-2 col-sm-12">

        </div>
        <div class="col-md-8 col-sm-12 mb-5 mx-0 px-0">
          <form class="form-horizontal needs-validation p-5 bg-white" method="POST" action="<?php echo e(route('somsclient.login.submit'), false); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group row">
                <label for="email" class="form-input-label col-lg-4 col-form-label text-lg-right"><?php echo app('translator')->get('common.email'); ?></label>

                <div class="col-lg-6">
                    <input id="email"
                            type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : '', false); ?>"
                            name="email" value="<?php echo e(old('email'), false); ?>" autofocus>

                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                      <span class="invalid-feedback" role="alert">
                          <strong><?php echo e($message, false); ?></strong>
                      </span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="form-input-label col-lg-4 col-form-label text-lg-right"><?php echo app('translator')->get('common.password'); ?></label>

                <div class="col-lg-6">
                    <input id="password" type="password" name="password"
                      class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : '', false); ?>">

                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                      <span class="invalid-feedback" role="alert">
                          <strong><?php echo e($message, false); ?></strong>
                      </span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-6 offset-lg-3">
                <div class="justify-content-between" style="display: flex; margin:0 50px;">
                  <span style="text-align:left;">
                    <input type="checkbox" class="form-check-input" name="remember" <?php echo e(old('remember') ? 'checked' : '', false); ?>>
                    <label class="form-detail-text" for="remember"><?php echo app('translator')->get('common.remember'); ?></label>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-6 offset-lg-3">
                <button type="submit" class="btn btn-primary w-100">
                  <?php echo app('translator')->get('common.submit'); ?>
                </button>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-6 offset-lg-3">
                <a href="#" class="clink text-center" data-toggle="modal" data-target="#ResetModl"> <?php echo app('translator')->get('common.resetPassword'); ?></a>
                <div class="changePasssuccess text-success"></div>
              </div>
            </div>
          </form>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="ResetModl" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content modal-sm">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo app('translator')->get('common.resetPassword'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form>
                  <div class="form-group">
                    <div class="col-sm-12">
                      <label for=""><?php echo app('translator')->get('common.email'); ?>*</label>
                      <input type="email" name="remail" id="remail" class="form-control">
                      <div class="remail-errormsg text-danger"></div>
                    </div>
                  </div>
                  <div class="form-group" id="loader">
                    <div class="col-sm-12 text-center">
                      <img src="<?php echo e(asset('spinner.gif'), false); ?>" style="max-height: 100px">
                    </div>
                  </div>
                  <div class="form-group reBtn">
                    <div class="col-sm-12">
                      <button type="button" class="btn btn-success" onclick="remSubmit()"><?php echo app('translator')->get('common.submit'); ?></button>
                    </div>
                  </div>
                  <div class="form-group otpNo">
                    <div class="col-sm-12">
                      <label for=""><?php echo app('translator')->get('common.OTP'); ?></label>
                      <input type="text" name="rotp" id="rotp" placeholder="Enter OTP" class="form-control">
                      <div class="rotp-errormsg text-danger"></div>
                    </div>
                  </div>
                  <div class="passForm">
                    <div class="form-group">
                      <div class="col-sm-12">
                        <label for=""><?php echo app('translator')->get('common.newPassword'); ?></label>
                        <input type="text" name="rpassword" id="rpassword" placeholder="Enter Password" class="form-control">
                        <div class="rpassword-errormsg text-danger"></div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-12">
                        <label for=""><?php echo app('translator')->get('common.confirmPassword'); ?></label>
                        <input type="text" name="rconfirmpassword" id="rconfirmpassword" placeholder="Enter Password" class="form-control">
                        <div class="rconfirmpassword-errormsg text-danger"></div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-12">
                        <button type="button" class="btn btn-success" onclick="changePassword()"><?php echo app('translator')->get('common.submit'); ?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>

            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-12">

        </div>
      </div>
    </div>
  </div>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('footerscript'); ?>
  <script>
    function remSubmit() {
      let remail = $("#remail").val();
      $("#loader").show()
      $("#reBtn").hide()
      if (validateEmail(remail)) {
        $.ajax({
          url: "<?php echo e(url('ajax/send-email-otp'), false); ?>",
          type: 'POST',
          dataType: 'json',
          data: {'email': remail},
          success: function (response) {
            $("#loader").hide()

            $(".otpNo").show()
            $("#remail").prop('readonly', true)
            $(".remail-errormsg").text(response.msg)
          },
          error: function (request, status, error) {
            $(".reBtn").show()
            var myresponse = JSON.parse(request.responseText);
            $(".remail-errormsg").text(myresponse.msg)
          }

        });
      }else{
        $(".remail-errormsg").text('Enter valid email')
      }
    }

    function validateEmail(email) {
      const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    }
    $(function(){
      $('#rotp').keyup(function() {
        let otp = $("#rotp").val()
        let remail = $("#remail").val()
        $.ajax({
          url: "<?php echo e(url('ajax/validate-otp'), false); ?>",
          type: 'POST',
          dataType: 'json',
          data: {'email': remail, 'otp': otp},
          success: function (response) {
            $(".otpNo").hide()
            $(".passForm").show()
            $(".remail-errormsg").text('')
          },
          error: function (request, status, error) {
            var myresponse = JSON.parse(request.responseText);
            $(".remail-errormsg").text(myresponse.msg)
          }

        });
      });
    });

    function changePassword() {
      let otp = $("#rotp").val()
      let email = $("#remail").val()
      let password = $("#rpassword").val()
      let confirmPassword = $("#rconfirmpassword").val()
      if (password != confirmPassword){
        $(".rconfirmpassword-errormsg").text('Password not matched')
      }
      else{
        $.ajax({
          url: "<?php echo e(url('ajax/change-password'), false); ?>",
          type: 'POST',
          dataType: 'json',
          data: {'email': email,'password': password,'otp':otp},
          success: function (response) {
            $("#ResetModl").modal('toggle')
            $(".changePasssuccess").text('Password changed successfully!')
          },
          error: function (request, status, error) {
            var myresponse = JSON.parse(request.responseText);
            $(".rconfirmpassword-errormsg").text(myresponse.msg)
          }

        });
      }
    }

  </script>
  <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.vendor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/soms/resources/views/auth-somsclient/login.blade.php ENDPATH**/ ?>