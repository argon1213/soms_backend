<!-- Default Banner -->
<?php $__env->startSection('banner'); ?>
  <div class="unit-5 overlay" style="background-image: url(<?php echo e(asset('images/hero_bg_2.jpg'), false); ?>);">
    <div class="container text-center">
      <h2 class="mb-0"><?php echo app('translator')->get('navbar.somsclient-dashboard'); ?></h2>
      <p class="mb-0 unit-6"><a href="<?php echo e(route('index'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.book'); ?></a><span class="sep">></span><span><?php echo app('translator')->get('navbar.somsclient-dashboard'); ?></span></p>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('validator'); ?>
  <?php $__env->startComponent('components.validator'); ?>
  <?php echo $__env->renderComponent(); ?>
<?php $__env->stopSection(); ?>
<!-- Main Layout -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo app('translator')->get('common.company.nickname'); ?> - <?php echo $__env->yieldContent('title'); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,700,900|Roboto+Mono:300,400,500">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/fonts/icomoon/style.css'), false); ?>">
    <!-- Bootstrap 4.4.1 Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css'), false); ?>">
    <!-- <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/bootstrap.min.css'), false); ?>"> -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/magnific-popup.css'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/jquery-ui.css'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/owl.carousel.min.css'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/owl.theme.default.min.css'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/bootstrap-datepicker.css'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/animate.css'), false); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/fonts/flaticon/font/flaticon.css'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/fl-bigmug-line.css'), false); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/aos.css'), false); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('vendor/authsomsclient/css/style.css'), false); ?>">

    <?php echo $__env->yieldContent('page-css'); ?>
    <!-- Material Design icons by Google -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- End -->
    <?php if (! empty(trim($__env->yieldContent('step-js')))): ?>

    <?php endif; ?>

  </head>
  <body>

  <div class="site-wrap">

    <div class="site-mobile-menu">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close mt-3">
          <span class="icon-close2 js-menu-toggle"></span>
        </div>
      </div>
      <div class="site-mobile-menu-body"></div>
    </div> <!-- .site-mobile-menu -->

    <header class="site-navbar py-1" role="banner" style="background-color: #ffc400;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-xs-12 col-xl-8" style="text-align:left; font-size:12px;">
            <span>
              <i class="material-icons text-white" style="font-size:12px; margin-right:5px;">&#xE0CD;</i><span class="text-white"><?php echo app('translator')->get('common.company.tel'); ?></span>
              <i class="material-icons text-white" style="font-size:12px; margin-left:20px; margin-right:5px;">&#xE0BE;</i><span class="text-white"><?php echo app('translator')->get('common.company.email'); ?></span>
            </span>
          </div>
          <div class="col-xs-12 col-xl-4" style="text-align:right; font-size:12px;">

            <span class="text-white">






             <a class="text-white" href="<?php echo e(url('lang/en'), false); ?>">English</a> |
             <a class="text-white" href="<?php echo e(url('lang/zh-TW'), false); ?>">中文 (香港)</a>

            </span>
          </div>
        </div>
      </div>
    </header>

    <header class="site-navbar py-1" role="banner">

      <div class="container">

        <div class="row align-items-center">

          <div class="col-6 col-xl-2">
            <!-- Company Logo -->
            <!-- <h1 class="mb-0"><a href="index.html" class="text-black h2 mb-0">Job<strong>start</strong></a></h1> -->
            <a href="https://www.ubox.com.hk">
              <img src="<?php echo e(asset('img/ubox-logo.png'), false); ?>" class="img-fluid" width="150" height="60">
            </a>
          </div>

          <?php $__env->startComponent('components.somsclient-navbar'); ?>
          <?php echo $__env->renderComponent(); ?>

          <div class="col-6 col-xl-2 text-right d-block">
            <div class="d-inline-block d-xl-none ml-md-0 mr-auto py-3" style="position: relative; top: 3px;">
              <a href="#" class="site-menu-toggle js-menu-toggle text-black"><span class="icon-menu h3"></span></a>
            </div>
          </div>
        </div>
      </div>

    </header>

    <!-- Banner -->
    <?php echo $__env->yieldContent('banner'); ?>
    <!-- Page Content -->
    <div class="site-section bg-light">
      <!-- Session -->
      <?php $__env->startComponent('components.session'); ?>
      <?php echo $__env->renderComponent(); ?>
      <!-- Validator -->
      <?php echo $__env->yieldContent('validator'); ?>

      <?php echo $__env->yieldContent('content'); ?>
    </div>
    <!-- Extend Content -->
    <?php echo $__env->yieldContent('extend-content'); ?>

    <style>
      /* ================================================================
       * FOOTER
       * ================================================================ */
      .footer-section {
      	background: #f2f2f2;
      }
      .footer-copyright-section {
      	background: #f7f7f7;
      	border-top: 1px solid #e1e1e1;
      	padding:25px 0;
      }
      .footer-widgets-section {
      	padding: 40px 0;
      }
      .footer-hidden .wrapper-inner {
          position: relative;
          z-index: 10;
      }
      .footer-section.footer-style-hidden {
          bottom: 0;
          left: 0;
          position: fixed;
          right: 0;
      	z-index: -1;
      	overflow-y: auto;
      }
      .footer-sticky .footer-section.footer-style-sticky .footer-copyright-section {
          bottom: 0;
          left: 0;
          position: fixed;
          right: 0;
          z-index: 10;
      }
      .footer-copyright-section p {
          margin-top: 8px;
      }
      /*Copyright and back to top*/
      .footer-copyright-section p,
      .zozo-footer-nav.navbar-nav > li > a{
      	color: #7f7f7f;
      	font-size: 11px;
      	margin: 0;
      	line-height: 18px;
      }
      .zozo-footer-nav.navbar-nav > li{
      	margin-right:8px;
      	position: relative;
      }
      .zozo-footer-nav.navbar-nav > li:after {
      	color:#7f7f7f;
          content: "/";
          position: absolute;
          right: -6px;
          top: -4px;
      }
      .zozo-footer-nav.navbar-nav > li:last-child:after{
      	content:none;
      }
      .zozo-footer-nav.navbar-nav > li > a{
      	padding:0;
      }
      .zozo-footer-nav.navbar-nav > li > a:hover{
      	color:#333;
      	background: transparent;
      }
      .footer-backtotop a{
      	background-color: #c6c6c6;
      	font-size:11px;
      	color:#fff;
      	height:30px;
      	width:30px;
      	line-height: 30px;
      	text-align:center;
      	display: inline-block;
      	margin-top:3px;
      	border-radius: 50%;
      	-moz-border-radius: 50%;
      	-webkit-border-radius: 50%;
      	-o-border-radius: 50%;
      	-ms-border-radius: 50%;
      	padding-right: 1px;
      }
      /* ================================================================
       * FOOTER WIDGETS
       * ================================================================ */
      .footer-widgets .widget h3 {
      	background:none;
      	padding-left:15px;
      	margin: 0 0 8px;
      	padding: 0;
      	text-transform:uppercase;
      	border-bottom: 1px solid #e1e1e1;
      }
      .footer-widgets .widget h3.widget-title {
      	color:#333;
      }
      .footer-widgets .widget {
      	margin-bottom: 10px;
      	border: none;
      }
      .footer-widgets .widget,
      .footer-widgets .widget p
      .footer-widgets .widget .zozo-count-number h3 {
      	color:#7f7f7f;
      }
      .footer-widgets .widget .zozo-mailchimp-form .form-control{
      	background:transparent;
          border: 1px solid #e1e1e1;
          box-shadow: 0 1px #ffffff, 0 1px 4px rgba(0, 0, 0, 0.05) inset;
      	-ms-box-shadow: 0 1px #ffffff, 0 1px 4px rgba(0, 0, 0, 0.05) inset;
      	-moz-box-shadow: 0 1px #ffffff, 0 1px 4px rgba(0, 0, 0, 0.05) inset;
      	-o-box-shadow: 0 1px #ffffff, 0 1px 4px rgba(0, 0, 0, 0.05) inset;
      	-webkit-box-shadow: 0 1px #ffffff, 0 1px 4px rgba(0, 0, 0, 0.05) inset;
          font-size: 12px;
      	height: 40px;
          line-height: 40px;
          min-height: 40px;
          padding: 8px 12px;
          width: 100%;
      }
      .footer-skin-dark .footer-widgets .widget .zozo-mailchimp-form .form-control{
      	box-shadow: none; -webkit-box-shadow: none; -moz-box-shadow: none; -ms-box-shadow: none; -o-box-shadow: none;
      }
      .footer-widgets .widget .zozo-count-number h3{
      	margin-bottom:0;
      	border-bottom:none;
      }
      .footer-widgets .widget .zozo-mailchimp-form{
      	margin-bottom:10px;
      }
      .footer-widgets .widget .tagcloud a {
      	color:#333;
      }
      .footer-widgets .widget .tagcloud a:hover{
      	color: #fff;
      }
      .footer-widgets .widget .zozo-call-action a.btn-call-action:hover,
      .footer-widgets .widget a.btn:hover {
      	color: #fff;
      }
      /* Popular Post */
      .footer-widgets .widget.zozo_popular_posts_widget li.posts-item{
      	padding: 5px 0;
      	margin-bottom: 5px;
      }
      .footer-widgets .widget.zozo_popular_posts_widget li.posts-item .posts-title{
      	margin-bottom: 0;
      }

      .footer-widgets .widget h3 { font-family: Oswald;font-size: 16px;font-style: normal;font-weight: 400;line-height: 42px; }
      .footer-widgets .widget div, .footer-widgets .widget p { font-family: Arimo;font-size: 13px;font-style: normal;font-weight: 400;line-height: 25px; }

    </style>

    <div id="footer" class="footer-section footer-style-default footer-skin-light" style="background:#f2f2f2;">
    	<div id="footer-widgets-container" class="footer-widgets-section">
    		<div class="container">
    			<div class="zozo-row row">
    				<div id="footer-widgets-1" class="footer-widgets col-sm-4 col-xs-12">
    					<div id="text_icl-2" class="widget widget_text_icl"><h3 class="widget-title">關於我們</h3>
                <div class="textwidget">方便利迷你倉本著「方便利，方便你」的精神，提供安全可靠而方便的儲物空間，加上優質先進的設備，以解決客戶家中或辦公室內堆存物件的存放問題，租用小小儲存空間，就可以增加大大居住空間。<br />
                  <br />
                  辦公室: 方便利有限公司 ubox Limited<br />
                  香港新界屯門天后路10號友德工業大廈11樓A2室<br />
                  <br />
                  西環長發店 : 香港西環山道7-9號長發工業大廈13樓A室<br />
                  西環香工店 : 香港西環德輔道西444-452號香港工業大廈19樓H室<br />
                  黃竹坑建德店 : 香港黃竹坑道26號建德工業大廈3樓A室<br />
                  屯門友德店 : 香港屯門天后路10號友德工業大廈11樓A2室<br />
                  <br />
                  聯絡我們: +852 6194-5500<br />
                  WhatsApp: 6194-5500<br />
                  電郵 : care@ubox.com.hk<br />
                </div>
    		      </div>
            </div>
    				<div id="footer-widgets-2" class="footer-widgets col-sm-4 col-xs-12">
    					<div id="text_icl-3" class="widget widget_text_icl"><h3 class="widget-title">辦工時間</h3>
                <div class="textwidget"><strong>在場客戶服務 : </strong><br />
                  星期一至五 10:00am to 4:00pm (HKT)<br />
                  <br />
                  <strong>運輸團隊 : </strong><br />
                  星期一至六 10:00am to 10:00pm (HKT)<br />
                  星期日 10:00am to 5:00pm (HKT)<br />
                  <br />
                  <strong>*法定假期休息</strong><br />
                </div>
    		      </div>
            </div>
    				<div id="footer-widgets-3" class="footer-widgets col-sm-4 col-xs-12">
    					<div id="text_icl-4" class="widget widget_text_icl"><h3 class="widget-title">公司資訊</h3>
                <div class="textwidget"><a href="https://www.ubox.com.hk/tnc/">條款及細則</a><br />
                  <a href="https://www.ubox.com.hk/privacy-policy/">私隱政策</a>
                </div>
    		      </div>
            </div>
    			</div><!-- .row -->
    		</div>
    	</div><!-- #footer-widgets-container -->

  		<div id="footer-copyright-container" class="footer-copyright-section">
  			<div class="container">
  				<div class="zozo-row row">
  					<div id="copyright-text" class="col-sm-7">
  						<p>&copy; Copyright 2019. ubox Limited. All Rights Reserved.</p>
  					</div><!-- #copyright-text -->
  					<div id="zozo-back-to-top" class="footer-backtotop col-sm-5 text-right">
  						<a href="#zozo-back-to-top">
                <i class="material-icons text-white" style="font-size:18px; padding-top:5px;">arrow_upward</i>
              </a>
  					</div><!-- #zozo-back-to-top -->
  				</div>
  			</div>
  		</div><!-- #footer-copyright-container -->
    </div><!-- #footer -->
  </div>

  <script src="<?php echo e(asset('vendor/authsomsclient/js/jquery-3.3.1.min.js'), false); ?>"></script>
  <script src="<?php echo e(asset('vendor/authsomsclient/js/jquery-migrate-3.0.1.min.js'), false); ?>"></script>
  <script src="<?php echo e(asset('vendor/authsomsclient/js/jquery-ui.js'), false); ?>"></script>
  <script src="<?php echo e(asset('vendor/authsomsclient/js/popper.min.js'), false); ?>"></script>
  <!-- Bootstrap 4.4.1 Styles -->
  <script src="<?php echo e(asset('js/app.js'), false); ?>" defer></script>
  <!-- <script src="<?php echo e(asset('vendor/authsomsclient/js/bootstrap.min.js'), false); ?>"></script> -->
  <script src="<?php echo e(asset('vendor/authsomsclient/js/owl.carousel.min.js'), false); ?>"></script>
  <script src="<?php echo e(asset('vendor/authsomsclient/js/jquery.stellar.min.js'), false); ?>"></script>
  <script src="<?php echo e(asset('vendor/authsomsclient/js/jquery.countdown.min.js'), false); ?>"></script>
  <script src="<?php echo e(asset('vendor/authsomsclient/js/jquery.magnific-popup.min.js'), false); ?>"></script>
  <script src="<?php echo e(asset('vendor/authsomsclient/js/bootstrap-datepicker.min.js'), false); ?>"></script>
  <script src="<?php echo e(asset('vendor/authsomsclient/js/aos.js'), false); ?>"></script>
  <?php if (! empty(trim($__env->yieldContent('step-js')))): ?>
    <script src="<?php echo e(asset('vendor/jquery-steps/jquery.steps.min.js'), false); ?>"></script>
  <?php endif; ?>
  <?php if (! empty(trim($__env->yieldContent('validate-js')))): ?>
    <script src="<?php echo e(asset('vendor/placeorder/js/jquery.validate.min.js'), false); ?>"></script>
    <script src="<?php echo e(asset('vendor/placeorder/js/jquery.additional-methods.min.js'), false); ?>"></script>
  <?php endif; ?>

  <script>
      // This example displays an address form, using the autocomplete feature
      // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var placeSearch, autocomplete;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };

      function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type  {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        for (var component in componentForm) {
          document.getElementById(component).value = '';
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
          if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            document.getElementById(addressType).value = val;
          }
        }
      }

      // Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
      function geolocate() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
      }
    </script>

    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&libraries=places&callback=initAutocomplete" async defer></script> -->

    <script src="<?php echo e(asset('vendor/authsomsclient/js/main.js'), false); ?>"></script>

    <script>
      $("a[href='#zozo-back-to-top']").click(function() {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
      });
    </script>

    <?php echo $__env->yieldContent('page-js'); ?>

    <?php echo $__env->yieldContent('step-js'); ?>

    <?php echo $__env->yieldContent('validate-js'); ?>
  <?php echo $__env->yieldPushContent('footerscript'); ?>

  </body>
</html>
<?php /**PATH /var/www/html/soms_uat/soms/resources/views/layouts/vendor.blade.php ENDPATH**/ ?>