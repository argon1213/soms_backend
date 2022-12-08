<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo app('translator')->get('placeorder.title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="colorlib.com">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <!-- MATERIAL DESIGN ICONIC FONT -->
    <link rel="stylesheet"
          href="<?php echo e(asset('vendor/placeorder/fonts/material-design-iconic-font/css/material-design-iconic-font.css'), false); ?>">
    <!-- STYLE CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/placeorder/css/style.css'), false); ?>">
    <!-- slick -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/slick/slick.css'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/slick/slick-theme.css'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/slick/custom.css'), false); ?>">


    <style>
        /* Estimate Monthly Cost */
        .input-txt {
            width: 162px;
            height: 36px;

            background: #FFFFFF;
            border: 1px solid #E6E6E6;
            box-sizing: border-box;

            margin-left: 5px;
        }

        .main-font {
            width: 25%;
            min-width: 250px;
            height: 36px;

            font-size: 18px;
            line-height: 21px;

            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        /* Storage Period */
        .sp-row {
            text-align: center;
            /* vertical-align: center; */
            margin-bottom: 20px;
        }

        #order_storage_period_row input[type="radio"] {
            display: none;
        }

        #order_storage_period_row span {
            width: 200px;
            max-width: 100%;
            height: 33px;

            background: white;
            border: 1px solid #C4C4C4;
            box-sizing: border-box;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
            border-radius: 10px;

            font-size: 16px;
            font-weight: bold;
            padding: 3px;
            margin-right: 40px;

            display: inline-block;
            cursor: pointer;
        }

        #order_storage_period_row input[type="radio"]:checked + span {
            background-color: #FACF83;
        }

        .steps ul:before {
            content: "<?php echo trans('placeorder.step1.title'); ?>";
        }

        .steps ul.step-2:before {
            content: "<?php echo trans('placeorder.step2.title'); ?>";
        }

        .steps ul.step-3:before {
            content: "<?php echo trans('placeorder.step3.title'); ?>";
        }

        .steps ul.step-4:before {
            content: "<?php echo trans('placeorder.step4.title'); ?>";
        }

        .steps ul.step-5:before {
            content: "<?php echo trans('placeorder.step5.title'); ?>";
        }

        .steps ul.step-6:before {
            content: "<?php echo trans('placeorder.step6.title'); ?>";
        }

        .product-qty-row {
            max-width: 33%;
        }

        .product-qty-bg {
            width: 30px;
            height: 30px;
            background: #FFC400;
            box-shadow: 0px 4px 4px rgb(0 0 0 / 25%);
            border-radius: 18px;
            color: white;
            padding: 2px;
            font-size: 18px;
            text-align: center;
            cursor: pointer;
        }

        .item .price {
            color: #b9951b;
        }

        .product-qty {
            background: #f3d4b7;
            border-color: #f3d4b7;
            color: white;
            height: 25px;
            width: 25px;
        }

        .product-qty-input {
            margin: 0;
            padding: 0;
            height: 32px;
            width: 30px;
            border: none;
            border-color: transparent;
            text-align: center;
            color: #bba04f;
            font-size: 14px;
        }

        .error {
            font-size: 10px;
            color: #8a1f11;
        }

        .steps ul li.error a {
            background-color: #8a1f11;
        }

        input[type="text"]:disabled {
            background: #dddddd;
        }

        select:disabled {
            background: #dddddd;
        }

        .preview-title {
            font-weight: bold;
        }

        .success-title {
            font-size: 22px;
            font-family: Poppins-SemiBold;
            color: rgb(51, 51, 51);
            margin-top: -62px;
            margin-bottom: 30px;
        }

        @media (min-width: 1024px) {
            .success-title {
                margin-top: -38px;
            }
        }

        .wrapper form {
            width: 100%;
            padding-left: 5%;
            padding-right: 5%;
        }

        #wizard {
            width: 100%;
        }

        .topbar {
            background-color: #fba626;
        }

        .topbar a {
            color: #fff;
            font-size: 12px;
        }

        .pull-right {
            float: right
        }

        .rightMenu li {
            padding-left: 20px
        }

        .rightMenu a {
            color: #fba524;
        }

        .bg-light {
            padding-top: 0;
            padding-bottom: 0px;
        }
        #loader{display: none}
    </style>
</head>
<body>

<div class="topbar">
    <div class="container">
        <div class="row ">
            <div class="col-sm-8 col-md-6">
                <a href="tel:85261945500"><?php echo app('translator')->get('common.company.tel'); ?></a> |
                <a href="mailto:care@ubox.com.hk"><?php echo app('translator')->get('common.company.email'); ?></a>
            </div>
            <div class="col-sm-4 col-md-6">
                <div class="pull-right">
                    <a href="<?php echo e(url('lang/en'), false); ?>">English</a> | <a href="<?php echo e(url('lang/zh-TW'), false); ?>"> 中文 (香港)</a>
                </div>
            </div>
        </div>
    </div>
</div>
<nav class="navbar navbar-expand-lg navbar-light bg-light">

    <div class="container">
        <a class="navbar-brand" href="https://www.ubox.com.hk"><img src="<?php echo e(asset('img/ubox-logo.png'), false); ?>"
                                                                    class="img-fluid" width="150" height="60"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">


            </ul>
            <ul class="d-flex rightMenu">

                <li>
                    <a href="<?php echo e(url('/'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.book'); ?></a>
                </li>
                <li>
                    <a href="<?php echo e(url('en/client/login'), false); ?>"><?php echo app('translator')->get('navbar.somsclient.login'); ?></a>
                </li>
            </ul>

        </div>
    </div>
</nav>
<div class="wrapper">
    

    <?php if(Session::has('success')): ?>
        <div id="wizard">
            <!-- Place Order Success -->
            <div class="success-title"><?php echo app('translator')->get('common.orderSuccess'); ?></div>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <label id="payment-success-description">
                            <!-- You can view your order by below url. -->
                            <?php echo e(Session::get('success'), false); ?>

                        </label>
                        <?php if(Session::has('qr_code_url')): ?>
                            <div class="visible-print text-center">
                                <?php echo QrCode::size(200)->generate(Session::get('qr_code_url'));; ?>

                            </div>
                        <?php else: ?>
                            <?php echo app('translator')->get('common.viewOrderURL'); ?>

                            <br/>
                            <a href="<?php echo e(route('somsclient.login'), false); ?>"><?php echo e(route('somsclient.login'), false); ?></a>
                            <br/>
                            <?php echo app('translator')->get("common.emailOrder"); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif(Session::has('error')): ?>
        <div id="wizard">
            <!-- Place Order Success -->
            <div class="success-title"><?php echo app('translator')->get('common.orderError'); ?></div>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <label id="payment-success-description">
                            <!-- You can view your order by below url. -->
                            <?php echo e(Session::get('error'), false); ?>

                        </label>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <form id="placeorder" action="<?php echo e(route('order-submit'), false); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div id="wizard">
                <!-- SECTION 1 - Your Order (Monthly Product) -->
                <h4></h4>
                <section>
                    <div class="sp-row p-2">
                        <p id="order_storage_period_row" name="order_storage_period_row">
                            <?php $__currentLoopData = $storagePeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $storagePeriod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="order_storage_period_label">
                                    <input type="radio" id="order_storage_period_<?php echo e($storagePeriod->id, false); ?>"
                                           name="order_storage_period" value="<?php echo e($storagePeriod->id, false); ?>"
                                           data-period="<?php echo e($storagePeriod->period_type, false); ?>"
                                           data-min="<?php echo e($storagePeriod->min, false); ?>"
                                           data-max="<?php echo e($storagePeriod->max, false); ?>" <?php echo e(($storagePeriod->default_select)? 'checked':'', false); ?>>
                                    <span><?php echo e($storagePeriod->name, false); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </p>
                    </div>
                    <div class="row">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-sm-6">
                                <div class="product">

                                    <div class="item product-item container" data-id="<?php echo e($item->id, false); ?>"
                                         data-category="<?php echo e($item->category, false); ?>">
                                        <div class="item-row row">
                                            <!-- Product Thumb -->
                                            <div class="col-4 col-md-3">
                                            <!-- <a href="#" class="thumb">
													<img src="<?php echo e(asset('storage'.$item->uri), false); ?>" alt="">
												</a> -->
                                                <img class="thumb" src="<?php echo e(asset('storage'.$item->uri), false); ?>" alt="">
                                            </div>
                                            <!-- Product Detail -->
                                            <div class="col-8 col-md-5">
                                            <!-- <a href="#"><?php echo e($item->name.' '.$item->name_cn, false); ?></a> -->
                                                <h6 id="product-<?php echo e($item->id, false); ?>-name">
                                                    <?php echo e((session('locale') == 'en')?$item->name:$item->name_cn, false); ?>

                                                </h6>
                                                <br/>
                                                <span class="pb-1"
                                                      style="font-size:14px;">
                                                     <?php echo e((session('locale') == 'en')?$item->description:$item->description_cn, false); ?>

                                                   </span>
                                                <br/>
                                                <span class="pt-1 product-detail-link" style="font-size:10px;"
                                                      data-id="<?php echo e($item->id, false); ?>" data-toggle="modal"
                                                      data-target="#productDetailModal"><?php echo app('translator')->get('common.detail'); ?> ></span>
                                            </div>
                                            <!-- Product Qty -->
                                            <div class="col-6 col-md-4">
                                                <div class="form-row">
                                                    <div class="product-qty-row product-qty-bg product-qty-plus">+</div>
                                                    <input class="product-qty-row product-qty-input product-category-<?php echo e($item->category, false); ?>"
                                                           id="product-qty-<?php echo e($item->id, false); ?>"
                                                           name="product-qty[<?php echo e($item->id, false); ?>]" data-id="<?php echo e($item->id, false); ?>"
                                                           data-old="0" value="0"/>
                                                    <div class="product-qty-row product-qty-bg product-qty-minus">-
                                                    </div>
                                                </div>
                                                <div id="product-<?php echo e($item->id, false); ?>-error"></div>
                                                <span class="price">
													<?php echo app('translator')->get('placeorder.currency'); ?><label
                                                            id="product-<?php echo e($item->id, false); ?>-price"
                                                            class="product-price-label" data-id="<?php echo e($item->id, false); ?>"
                                                            data-original-price="<?php echo e($item->default_price, false); ?>"><?php echo e($item->default_price, false); ?></label> / <?php echo app('translator')->get('placeorder.month'); ?>
												</span>
                                            </div>
                                            <!-- Product Price -->

                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>


                    <!-- Promotion Code Section -->
                    <div id="add-promo-div" class="d-flex justify-content-end">
                        <label id="add-promo-label" class="main-font"><?php echo app('translator')->get('placeorder.step1.add_promo'); ?></label>
                        <div id="add-promo-btn" class="product-qty-bg">+</div>
                    </div>
                    <div id="input-promo-div" class="d-flex justify-content-end" style="display: none !important;">
                        <label id="input-promo-label" class="main-font"><?php echo app('translator')->get('placeorder.step1.input_promo'); ?></label>
                        <input type="text" class="input-promo input-txt" id="input_promo" name="input_promo"
                               style="text-align:center;" value="">
                        <input type="hidden" id="input_promo_id" name="input_promo_id"/>
                    </div>
                    <div id="input-promo-error-div" class="d-flex justify-content-end"
                         style="display: none !important;">
                        <div id="input-promo-error" class="main-font" style="min-width: 800px; color:red;"></div>
                    </div>
                    <!-- Estimate Monthly Cost section -->
                    <div class="d-flex justify-content-end">
                        <label id="monthly-price-label"
                               class="main-font"><?php echo app('translator')->get('placeorder.step1.monthly_price'); ?></label>
                        <input type="text" class="monthly-price input-txt" id="monthly-price" style="text-align:center;"
                               value="<?php echo e(trans('placeorder.currency'), false); ?> 0" readonly>
                    </div>
                </section>
                <!-- SECTION 2 - Your Order (One-time Product) -->
                <h4></h4>
                <section>
                    <div class="sp-row p-2">
                        <p id="order_storage_period_row" name="order_storage_period_row">
                            <label class="order_storage_period_label">
                                <input type="radio" value="<?php echo e($storagePeriod->id, false); ?>" checked>
                                <span><?php echo app('translator')->get('common.oneTimeProduct'); ?></span>
                            </label>
                        </p>
                    </div>
                    <div class="product">
                        <?php $__currentLoopData = $oneTimeItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="item product-item container" data-id="<?php echo e($item->id, false); ?>"
                                 data-category="<?php echo e($item->category, false); ?>">
                                <div class="item-row row" style="width:100%;">
                                    <!-- Product Thumb -->
                                    <div class="col-4 col-md-2">
                                    <!-- <a href="#" class="thumb">
													<img src="<?php echo e(asset('storage'.$item->uri), false); ?>" alt="">
												</a> -->
                                        <img class="thumb" src="<?php echo e(asset('storage'.$item->uri), false); ?>" alt="">
                                    </div>
                                    <!-- Product Detail -->
                                    <div class="col-8 col-md-5">
                                    <!-- <a href="#"><?php echo e($item->name.' '.$item->name_cn, false); ?></a> -->
                                        <h6 id="product-<?php echo e($item->id, false); ?>-name">
                                            <?php echo e((session('locale') == 'en')?$item->name:$item->name_cn, false); ?>

                                        </h6>
                                        <br/>
                                        <span class="pb-1" style="font-size:14px;">
                                        <?php echo e((session('locale') == 'en')?$item->description:$item->description_cn, false); ?>

                                    </span>
                                        <br/>
                                        <span class="pt-1" style="font-size:10px;"><?php echo app('translator')->get('common.detail'); ?> ></span>
                                    </div>
                                    <!-- Product Qty -->
                                    <div class="col-6 col-md-2">
                                        <div class="form-row">
                                            <div class="product-qty-bg product-qty-plus">+</div>
                                            <input class="product-qty-input product-category-<?php echo e($item->category, false); ?>"
                                                   id="product-qty-<?php echo e($item->id, false); ?>" name="product-qty[<?php echo e($item->id, false); ?>]"
                                                   data-id="<?php echo e($item->id, false); ?>" data-old="0" value="0"/>
                                            <div class="product-qty-bg product-qty-minus">-</div>
                                        </div>
                                        <div id="product-<?php echo e($item->id, false); ?>-error"></div>
                                    </div>
                                    <!-- Product Price -->
                                    <div class="col-6 col-md-3 ml-auto" style="text-align:right;">
												<span class="price">
													<?php echo app('translator')->get('placeorder.currency'); ?><label
                                                            id="product-<?php echo e($item->id, false); ?>-price"
                                                            class="product-price-label" data-id="<?php echo e($item->id, false); ?>"
                                                            data-original-price="<?php echo e($item->default_price, false); ?>"><?php echo e($item->default_price, false); ?></label> / <?php echo app('translator')->get('placeorder.each'); ?>
												</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <!-- Estimate Monthly Cost section -->
                    <div class="d-flex justify-content-end">
                        <label id="monthly-price-label" class="main-font"><?php echo app('translator')->get('common.monthlyCostEstimate'); ?></label>
                        <input type="text" class="monthly-price input-txt" id="monthly-price-2"
                               style="text-align:center;"
                               value="<?php echo e(trans('placeorder.currency'), false); ?> 0" readonly>
                    </div>
                    <div class="d-flex justify-content-end">
                        <label id="other-price-label" class="main-font"><?php echo app('translator')->get('common.plus'); ?></label>
                        <input type="text" class="other-price input-txt" id="other-price" style="text-align:center;"
                               value="<?php echo e(trans('placeorder.currency'), false); ?> 0" readonly>
                    </div>
                </section>
                <!-- SECTION 3 - Account Information (User Login) -->
                <h4></h4>
                <section>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-4">

                                <div id="login_row" class="row d-flex align-items-end" style="margin-bottom:30px;">
                                    <div class="col-sm-12">
                                        <h5 class="form-head"><?php echo app('translator')->get('common.loggin'); ?></h5>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="login_email"><?php echo app('translator')->get('placeorder.step3.email'); ?></label>
                                        <input type="text" class="form-control form-control-sm" id="login_email"
                                               name="login_email">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="login_password"><?php echo app('translator')->get('placeorder.step3.password'); ?></label>
                                        <input type="password" class="form-control form-control-sm" id="login_password"
                                               name="login_password">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <input type="button" class="form-control form-control-sm" id="login_btn"
                                               name="login_btn"
                                               style="background: #FACF83;"
                                               value="<?php echo e(trans('placeorder.step3.login'), false); ?>">
                                    </div>
                                    <div id="login_error_div" class="col-12 error" style="display:none;">

                                    </div>
                                    <div class="col-md-12 form-group">
                                        <a href="#" class="clink" data-toggle="modal"
                                           data-target="#ResetModl"><?php echo app('translator')->get('placeorder.step3.resetpassword'); ?></a>
                                        <div class="changePasssuccess text-success"></div>
                                    </div>
                                    <!-- Modal -->
                                    <div class="modal fade" id="ResetModl" tabindex="-1" role="dialog"
                                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content modal-sm">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="exampleModalLabel"><?php echo app('translator')->get('placeorder.step3.resetpassword'); ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for=""><?php echo app('translator')->get('placeorder.step3.email'); ?></label>
                                                                <input type="email" name="remail" id="remail"
                                                                       class="form-control">
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
                                                                <button type="button" class="btn btn-success"
                                                                        onclick="remSubmit()"><?php echo app('translator')->get('common.submit'); ?></button>
                                                            </div>
                                                        </div>
                                                        <div class="form-group otpNo">
                                                            <div class="col-sm-12">
                                                                <label for=""><?php echo app('translator')->get('common.OTP'); ?></label>
                                                                <input type="text" name="rotp" id="rotp"
                                                                       placeorder="Enter OTP" class="form-control">
                                                                <div class="rotp-errormsg text-danger"></div>
                                                            </div>
                                                        </div>
                                                        <div class="passForm">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for=""><?php echo app('translator')->get('common.newPassword'); ?></label>
                                                                    <input type="text" name="rpassword" id="rpassword"
                                                                           placeorder="Enter Password"
                                                                           class="form-control">
                                                                    <div class="rpassword-errormsg text-danger"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for=""><?php echo app('translator')->get('common.confirmPassword'); ?></label>
                                                                    <input type="text" name="rconfirmpassword"
                                                                           id="rconfirmpassword"
                                                                           placeorder="Enter Password"
                                                                           class="form-control">
                                                                    <div class="rconfirmpassword-errormsg text-danger"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <button type="button" class="btn btn-success"
                                                                            onclick="changePassword()"><?php echo app('translator')->get('common.submit'); ?></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-8">
                                <input type="hidden" id="somsclient_id" name="somsclient_id"/>
                                <h5 class="form-head ifloggedIn">Register</h5>
                                <div class="register-box">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 p-1">
                                            <label for="name"><?php echo app('translator')->get('placeorder.step3.name'); ?></label>
                                            <input type="text" class="form-control form-control-sm" id="name"
                                                   name="name">
                                        </div>
                                        <div class="col-md-4 col-sm-4 p-1">
                                            <label for="email"><?php echo app('translator')->get('placeorder.step3.email'); ?></label>
                                            <input type="email" class="form-control form-control-sm" id="email"
                                                   name="email">
                                        </div>
                                        <div class="col-md-4 col-sm-4 p-1">
                                            <label for="contact"><?php echo app('translator')->get('placeorder.step3.contact'); ?></label>
                                            <input type="text" class="form-control form-control-sm" id="contact"
                                                   name="contact"
                                                   placeorder="8 to 13 digits" required>
                                        </div>
                                    </div>
                                    <div class="row mb-1">

                                        <div class="col-md-12 col-sm-12 p-1">
                                            <label for="address"><?php echo app('translator')->get('placeorder.step3.address'); ?></label>
                                            <input type="text" class="form-control form-control-sm" id="address"
                                                   name="address"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6 col-sm-6 p-1">
                                            <label for="city"><?php echo app('translator')->get('placeorder.step3.city'); ?></label>
                                            <select class="form-control form-control-sm" id="city" name="city">
                                                <option value=""><?php echo app('translator')->get('placeorder.input.select.default_option'); ?></option>
                                                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($city->id, false); ?>"><?php echo e((session('locale') == 'en')?$city->name:$city->name_cn, false); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-sm-6 p-1">
                                            <label for="state"><?php echo app('translator')->get('placeorder.step3.state'); ?></label>
                                            <select class="form-control form-control-sm" id="state" name="state">
                                                <option value=""><?php echo app('translator')->get('placeorder.input.select.default_option'); ?></option>
                                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($state->id, false); ?>"><?php echo e((session('locale') == 'en')?$state->name:$state->name_cn, false); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Pickup Address -->
                                    <div class="row mb-1">
                                        <div class="col-md-12 col-sm-12 p-1">
                                            <label for="pickup_address_same"><?php echo app('translator')->get('placeorder.step3.pickup_address_same'); ?></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="pickup_address_same"
                                                       id="pickup_address_same_yes" value="1" checked>
                                                <label class="form-check-label"
                                                       for="pickup_address_same_yes"><?php echo app('translator')->get('placeorder.step3.yes'); ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="pickup_address_same"
                                                       id="pickup_address_same_no" value="0">
                                                <label class="form-check-label"
                                                       for="pickup_address_same_no"><?php echo app('translator')->get('placeorder.step3.no'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="pickup_address_same_layout" style="display:none;">
                                        <div class="row mb-1">
                                            <div class="col-md-12 col-sm-12 p-1">
                                                <label for="pickup_address"><?php echo app('translator')->get('placeorder.step3.address'); ?></label>
                                                <input type="text" class="form-control form-control-sm"
                                                       id="pickup_address"
                                                       name="pickup_address" required>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6 col-sm-6 p-1">
                                                <label for="city"><?php echo app('translator')->get('placeorder.step3.city'); ?></label>
                                                <select class="form-control form-control-sm" id="pickup_city"
                                                        name="pickup_city">
                                                    <option value=""><?php echo app('translator')->get('placeorder.input.select.default_option'); ?></option>
                                                    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($city->id, false); ?>"><?php echo e((session('locale') == 'en')?$city->name:$city->name_cn, false); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-sm-6 p-1">
                                                <label for="state"><?php echo app('translator')->get('placeorder.step3.state'); ?></label>
                                                <select class="form-control form-control-sm" id="pickup_state"
                                                        name="pickup_state">
                                                    <option value=""><?php echo app('translator')->get('placeorder.input.select.default_option'); ?></option>
                                                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($state->id, false); ?>"><?php echo e((session('locale') == 'en')?$state->name:$state->name_cn, false); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Emptyout / Checkin / Checkout -->
                        <div class="row">
                            <div class="col-md-4 p-1">
                                <label id="emptyout_label" style="text-align:right;"><?php echo app('translator')->get('placeorder.step3.emptyout'); ?></label>
                            </div>
                            <div class="col-md-4 p-1">
                                <input class="form-control form-control-sm order_date" type="date"
                                       id="emptyout_date_other"
                                       name="emptyout_date_other">
                            </div>
                            <div class="col-md-4 p-1">
                                <select class="form-control form-control-sm" id="emptyout_time_other"
                                        name="emptyout_time_other">
                                    <option value="09:00am - 01:00pm">09:00am - 02:00pm</option>
                                    <option value="02:00pm - 06:00pm">02:00pm - 06:00pm</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 p-1">
                                <label id="checkin_label"><?php echo app('translator')->get('placeorder.step3.checkin'); ?></label>
                            </div>
                            <div class="col-md-4 p-1">
                                <input class="form-control form-control-sm order_date" type="date"
                                       id="checkin_date_other"
                                       name="checkin_date_other">
                            </div>
                            <div class="col-md-4 p-1">
                                <select class="form-control form-control-sm" id="checkin_time_other"
                                        name="checkin_time_other">
                                    <option value="09:00am - 01:00pm">09:00am - 02:00pm</option>
                                    <option value="02:00pm - 06:00pm">02:00pm - 06:00pm</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 p-1">
                                <label id="checkout_label"><?php echo app('translator')->get('placeorder.step3.checkout'); ?></label>
                            </div>
                            <div class="col-md-4 p-1">
                                <input class="form-control form-control-sm order_date" type="date"
                                       id="checkout_date_other"
                                       name="checkout_date_other">
                            </div>
                            <div class="col-md-4 p-1">
                                <select class="form-control form-control-sm" id="checkout_time_other"
                                        name="checkout_time_other">
                                    <option value="09:00am - 01:00pm">09:00am - 02:00pm</option>
                                    <option value="02:00pm - 06:00pm">02:00pm - 06:00pm</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- SECTION 4 - Account Information (Other Information)  -->
                <h4></h4>
                <section>
                    <!-- University Row -->
                    <div class="row mb-1">
                        <div class="col-md-12 col-sm-12 p-1">
                            <label for="university_student"><?php echo app('translator')->get('placeorder.step4.university_student'); ?></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="university_student"
                                       id="university_student_yes" value="1">
                                <label class="form-check-label"
                                       for="pickup_address_same_yes"><?php echo app('translator')->get('placeorder.step3.yes'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="university_student"
                                       id="university_student_no" value="0" checked>
                                <label class="form-check-label"
                                       for="pickup_address_same_no"><?php echo app('translator')->get('placeorder.step3.no'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1" id="university_student_layout" style="display:none;">
                        <div class="col-md-6 col-sm-12 p-1">
			    <?php echo app('translator')->get('placeorder.step4.university_id'); ?><br>
                            <select class="form-control form-control-sm" id="university_id" name="university_id"
                                    required>
                                <option value=""><?php echo app('translator')->get('placeorder.input.select.default_option'); ?></option>
                                <?php $__currentLoopData = $universities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $university): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($university->id, false); ?>"><?php echo e($university->university, false); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-12 p-1">
			    <?php echo app('translator')->get('placeorder.step4.student_id'); ?><br>
                            <input type="text" class="form-control form-control-sm" id="student_id" name="student_id"
                                   placeorder="<?php echo e(trans('placeorder.step4.student_id'), false); ?>" required>
                        </div>
                        <div class="col-md-3 col-sm-12 p-1">
			    <?php echo app('translator')->get('placeorder.step4.wechat_id'); ?><br>
                            <input type="text" class="form-control form-control-sm" id="wechat_id" name="wechat_id"
                                   placeorder="<?php echo e(trans('placeorder.step4.wechat_id'), false); ?>">
                        </div>
                    </div>
                    <!-- Special Instruction Row -->
                    <div class="row mb-1">
                        <div class="col-md-12 col-sm-12 p-1">
                            <label for="special_instruction_label"><?php echo app('translator')->get('placeorder.step4.special_instruction_label'); ?></label>
                            <textarea class="form-control form-control-sm" id="special_instruction"
                                      name="special_instruction" rows="5"
                                      placeorder="<?php echo e(trans('placeorder.step4.special_instruction_placeorder'), false); ?>"></textarea>
                        </div>
                    </div>
                    <!-- Walkup -->
                    <div class="row mb-1">
                        <div class="col-md-12 col-sm-12 p-1">
                            <label for="walkup_label"><?php echo app('translator')->get('placeorder.step4.walkup_label'); ?></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="walkup" id="walkup_yes" value="1">
                                <label class="form-check-label" for="walkup_yes"><?php echo app('translator')->get('placeorder.step3.yes'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="walkup" id="walkup_no" value="0"
                                       checked>
                                <label class="form-check-label" for="walkup_no"><?php echo app('translator')->get('placeorder.step3.no'); ?></label>
                            </div>
                        </div>
                    </div>
                    <!-- Mandatory Home Quarantine -->
                    <div class="row mb-1">
                        <div class="col-md-12 col-sm-12 p-1">
                            <label id="mandatory_home_quarantine"><?php echo app('translator')->get('placeorder.step4.mandatory_home_quarantine'); ?></label>
                        </div>
                        <div class="col-md-12 col-sm-12 p-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mandatory_home_quarantine"
                                       id="mandatory_home_quarantine_yes" value="1">
                                <label class="form-check-label"
                                       for="mandatory_home_quarantine_yes"><?php echo app('translator')->get('placeorder.step3.yes'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mandatory_home_quarantine"
                                       id="mandatory_home_quarantine_no" value="0" checked>
                                <label class="form-check-label"
                                       for="mandatory_home_quarantine_no"><?php echo app('translator')->get('placeorder.step3.no'); ?></label>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 p-1">
                            <label id="mandatory_home_quarantine_yes"><?php echo app('translator')->get('placeorder.step4.mandatory_home_quarantine_yes'); ?></label>
                        </div>
                    </div>
                </section>
                <!-- SECTION 5 - Order Confirmation -->
                <h4></h4>
                <section>
                    <div class="order-preview">
                        <div class="order-content">
                            <label class="preview-title"><?php echo app('translator')->get('placeorder.step5.account_title'); ?></label>
                            <div id="preview-account" class="container">
                            </div>
                            <hr/>
                            <label class="preview-title"><?php echo app('translator')->get('placeorder.step5.order_title'); ?></label>
                            <div id="preview-product" class="container">
                            </div>
                            <hr/>
                            <label class="preview-title"><?php echo app('translator')->get('placeorder.step5.checkinout_title'); ?></label>
                            <div id="preview-checkinout" class="container">
                            </div>
                            <hr/>
                            <label class="preview-title"><?php echo app('translator')->get('placeorder.step5.summary_title'); ?></label>
                            <div id="preview-summary" class="container">
                            </div>
                            <hr/>
                        </div>
                        <div class="order-input">
                            <input type="hidden" id="monthly-fee" name="monthly-fee">
                            <input type="hidden" id="storage-month" name="storage-month">
                            <input type="hidden" id="other-fee" name="other-fee">
                            <input type="hidden" id="total-fee" name="total-fee">
                        </div>
                    </div>
                </section>
                <!-- SECTION 6 - Payment Method -->
                <h4></h4>
                <section>
                    <div class="checkbox-circle">
                        <?php $__currentLoopData = $paymentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paymentType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="<?php echo e(($loop->first)?'active':'', false); ?>">
                                <input type="radio" name="order_payment_type"
                                       value="<?php echo e($paymentType->id, false); ?>" <?php echo e(($loop->first)?'checked':'', false); ?>><?php echo e((session('locale') == 'en')?$paymentType->description:$paymentType->description_cn, false); ?>

                                <span class="checkmark"></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div id="payment-info" class="payment-info">
                        <label>付款資訊</label>
                        <div class="form-group">
                            <div id="card-element">
                            </div>
                            <div id="card-errors" class="error" role="alert"></div>
                        </div>
                    </div>
                </section>
            </div>
        </form>
    <?php endif; ?>
</div>
<script src="<?php echo e(asset('vendor/placeorder/js/jquery-3.3.1.min.js'), false); ?>"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>

<script src="https://js.stripe.com/v3/"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
        integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<!-- JQUERY STEP -->
<script src="<?php echo e(asset('vendor/placeorder/js/jquery.steps.js'), false); ?>"></script>

<script src="<?php echo e(asset('vendor/placeorder/js/jquery.metadata.js'), false); ?>"></script>

<script src="<?php echo e(asset('vendor/placeorder/js/jquery.validate.min.js'), false); ?>"></script>
<script src="<?php echo e(asset('vendor/placeorder/js/jquery.additional-methods.min.js'), false); ?>"></script>
<!-- Slick -->
<script src="<?php echo e(asset('vendor/slick/slick.min.js'), false); ?>"></script>
<!-- <script src="<?php echo e(asset('vendor/placeorder/js/main.js'), false); ?>"></script> -->
<!-- Template created and distributed by Colorlib -->
<script>
    var form = $("#placeorder");
    form.validate({
        rules: {
            email: {
                required: true,
                email: true,
                remote: {
                    url: "<?php echo e(route('ajax-email-validate'), false); ?>",
                    type: "post",
                    data: {
                        email: function () {
                            return $("#email").val();
                        }
                    }
                }
            },
            contact: {
                required: function (element) {
                    return $("#contact").val().length == 0;
                },
                pattern: /^\+?\d{8,13}/
            },
            input_promo: {
                // required: true,
                remote: {
                    url: "<?php echo e(route('ajax-promotion-code-validate'), false); ?>",
                    type: "post",
                    dateType: "json",
                    // async: false,
                    data: {
                        promotion_code: function () {
                            // console.log('1:::'+$( "#input_promo" ).val());
                            return $("#input_promo").val();
                        }
                    },
                    complete: function (response, status) {
                        console.log('jquery validate complete');
                        var data = response.responseJSON;
                        if (data) {
                            // Update Product Price
                            console.log(data.items);
                            if (data.items.length > 0) {
                                $('#input_promo_id').val(data.id);
                                for (var i = 0; i < data.items.length; i++) {
                                    var item = data.items[i];
                                    // $('#product-'+item.item_id+'-price').html(parseFloat(item.price));
                                    $('#product-' + item.item_id + '-price').data('promotion', parseFloat(item.price));

                                    calcProductPrice(item.item_id, item.price);
                                    refreshPrice();
                                }
                                $('#input-promo-label').html('<?php echo e(trans("placeorder.step1.used_promo"), false); ?>');
                                $('#input_promo').prop('readonly', true);
                                $('#input_promo').removeClass('error');
                                $('#input_promo').css("background-color", "#E6E6E6");
                            }
                            return true;
                        }
                    },
                }
            },
            emptyout_date_other: {
                required: true,
                validEmptyout: true
            },
            checkin_date_other: {
                required: true,
                validCheckin: true
            },
            checkout_date_other: {
                required: true,
                validCheckout: true,
                validTimerange: true
            }
        },
        messages: {
            email: {
                required: "Please input Email Address",
                email: "Please input a valid Email Address",
                remote: "This email has been taken already",
            },
            contact: {
                required: "Please input Contact No.",
                pattern: "Contact No. should be 8 to 13 digits"
            },
            input_promo: {
                remote: "Sorry, the promotional code you entered is invalid. Please try again."
            }
        },
        errorPlacement: function (error, element) {
            // console.log('error :'+error.toString());
            // console.log('error json :'+JSON.stringify(error));
            // console.log('element json :'+JSON.stringify(element));
            // console.log('element id :'+element.attr('id'));
            if (element.attr('class').includes('product-category-box')) {
                error.appendTo($('#product-' + element.data('id') + '-error'));
            } else if (element.attr('id') == 'input_promo') {
                error.appendTo($('#input-promo-error'));
            } else {
                error.insertAfter(element);
            }
        }
    });

    $(function () {
        // Set All Date must be select larger than today
        $('[type="date"]').prop('min', function () {
            return new Date(new Date().getTime() + (48*60*60*1000) - new Date().getTimezoneOffset() * 60000).toJSON().split('T')[0];
        });

        $.validator.addMethod("validEmptyout", function (value, element) {
            var emptyout = new Date(value);
            var now = new Date();
	    //now.setDate(now.getDate() + 10);
            // console.log(emptyout+'::::'+now);
            if (isNaN(emptyout.getTime()))
                return true;
            return emptyout.getTime() >= now.getTime();

        }, "<?php echo e(trans('placeorder.step3.emptyout'), false); ?> must not before than current date");

        $.validator.addMethod("validCheckin", function (value, element) {
            var checkin = new Date(value);
            var emptyout = new Date($("#emptyout_date_other").val());
            // console.log(checkin+'::::'+emptyout);
            if (isNaN(checkin.getTime()))
                return true;
            return checkin.getTime() >= emptyout.getTime();

        }, "<?php echo e(trans('placeorder.step3.checkin'), false); ?> must not before than <?php echo e(trans('placeorder.step3.emptyout'), false); ?>");

        $.validator.addMethod("validCheckout", function (value, element) {
            var checkout = new Date(value);
            var checkin = new Date($("#checkin_date_other").val());
            // console.log(checkout+'::::'+checkin);
            if (isNaN(checkout.getTime()))
                return true;
            return checkout.getTime() > checkin.getTime();

        }, "<?php echo e(trans('placeorder.step3.checkout'), false); ?> must later than <?php echo e(trans('placeorder.step3.checkin'), false); ?>");

        $.validator.addMethod("validTimerange",
            function (value, element) {
                var checkout = new Date(value);
                var checkin = new Date($("#checkin_date_other").val());
                if (isNaN(checkout.getTime()) || isNaN(checkin.getTime()))
                    return true;
                var monthDiff = calcDateDiffByMonth($("#checkin_date_other").val(), value);
                var selectStoragePeriodMin = parseInt($("input[name='order_storage_period']:checked").data('min'));
                var selectStoragePeriodMax = parseInt($("input[name='order_storage_period']:checked").data('max'));
                console.log(monthDiff + '::::' + selectStoragePeriodMin + '::::' + selectStoragePeriodMax);
                if (isNaN(selectStoragePeriodMin) && isNaN(selectStoragePeriodMax)) {
                    return true;
                } else if (isNaN(selectStoragePeriodMin)) {
                    return monthDiff <= selectStoragePeriodMax;
                } else if (isNaN(selectStoragePeriodMax)) {
                    return monthDiff >= selectStoragePeriodMin;
                } else {
                    return monthDiff >= selectStoragePeriodMin && monthDiff <= selectStoragePeriodMax;
                }
            },
            // Message
            function () {
                var selectStoragePeriodMin = parseInt($("input[name='order_storage_period']:checked").data('min'));
                var selectStoragePeriodMax = parseInt($("input[name='order_storage_period']:checked").data('max'));

                if (isNaN(selectStoragePeriodMin) && isNaN(selectStoragePeriodMax)) {
                    return "";
                } else if (isNaN(selectStoragePeriodMin)) {
                    return "Storage Period must smaller than " + selectStoragePeriodMax + " month(s).";
                } else if (isNaN(selectStoragePeriodMax)) {
                    return "Storage Period must larger than " + selectStoragePeriodMin + " month(s).";
                } else {
                    return "Storage Period must between " + selectStoragePeriodMin + " month(s) and " + selectStoragePeriodMax + " month(s).";
                }
            }
        );

        $.validator.addMethod("havebox", function (value, element) {
            var total = 0;

            $(".product-category-box").each(function () {
                total += parseInt($(this).val());
            });
            // console.log(total);
            return total > 0;

        }, "Please select at least one box.");

        $.validator.addClassRules("product-category-box", {
            havebox: true
        });
        // $.validator.addClassRules("order_date", {
        // 	validtimerange:true
        // });
        // $("#wizard").steps({
        form.children("div").steps({
            headerTag: "h4",
            bodyTag: "section",
            transitionEffect: "fade",
            enableAllSteps: false,
            transitionEffectSpeed: 500,
            onStepChanging: function (event, currentIndex, newIndex) {
                var valid = false;
                console.log('onStepChanging : ' + currentIndex + ' :: ' + newIndex);
                // Only Process when go next step
                if (currentIndex < newIndex) {
                    console.log('before validate');
                    form.validate().settings.ignore = ":disabled,:hidden";

                    // Remove Previous Value after validate
                    $("#input_promo").removeData("previousValue");

                    valid = form.valid();
                    console.log('after validate : ' + valid);
                } else {
                    valid = true;
                }
                // Only Process when go prev step
                if (currentIndex > newIndex) {

                }
                // valid = true;
                if (valid) {
                    if (newIndex === 0) {

                    }
                    if (newIndex === 1) {
                        $('.steps ul').addClass('step-2');
                    } else {
                        $('.steps ul').removeClass('step-2');
                    }
                    if (newIndex === 2) {
                        $('.steps ul').addClass('step-3');
                    } else {
                        $('.steps ul').removeClass('step-3');
                    }
                    if (newIndex === 3) {
                        $('.steps ul').addClass('step-4');
                    } else {
                        $('.steps ul').removeClass('step-4');
                    }
                    if (newIndex === 4) {
                        $('.steps ul').addClass('step-5');

                        if (newIndex > currentIndex) {
                            resetOrderPreview();
                            createOrderPreview();
                        }
                    } else {
                        $('.steps ul').removeClass('step-5');
                    }

                    if (newIndex === 5) {
                        $('.steps ul').addClass('step-6');
                        $('.actions ul').addClass('step-last');
                    } else {
                        $('.steps ul').removeClass('step-6');
                        $('.actions ul').removeClass('step-last');
                    }
                }

                console.log('after validate : ' + valid);
                return valid;
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ":disabled,:hidden";
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                form.submit();
                // console.log($('input[type=radio][name=order_payment_type]:checked').val());
            },
            labels: {
                finish: "Place Order",
                next: "Next",
                previous: "Previous"
            }
        });
        // Custom Steps Jquery Steps
        $('.wizard > .steps li a').click(function () {
            $(this).parent().addClass('checked');
            $(this).parent().prevAll().addClass('checked');
            $(this).parent().nextAll().removeClass('checked');
        });
        // Custom Button Jquery Steps
        $('.forward').click(function () {
            $("#wizard").steps('next');
        })
        $('.backward').click(function () {
            $("#wizard").steps('previous');
        })
        // Checkbox
        $('.checkbox-circle label').click(function () {
            $('.checkbox-circle label').removeClass('active');
            $(this).addClass('active');
        })

        $(".product-qty-plus").click(function () {
            // console.log('target input id : '+$(this).next('input').attr('id'));
            var qtyInput = $(this).next('input');
            var currVal = (qtyInput.val() === '' || isNaN(qtyInput.val())) ? 0 : parseInt(qtyInput.val());

            if (currVal < 99) {
                $(this).next('input').val(currVal + 1);
                refreshPrice();
            }
        });

        $(".product-qty-minus").click(function () {
            // console.log('target input id : '+$(this).prev('input').attr('id'));
            var qtyInput = $(this).prev('input');
            var currVal = (qtyInput.val() === '' || isNaN(qtyInput.val())) ? 0 : parseInt(qtyInput.val());

            if (currVal > 0) {
                $(this).prev('input').val(currVal - 1);
                refreshPrice();
            }
        });

        $(".product-qty-input").on('change', function (e) {
            var input = $(this);
            var prevVal = input.data('old');
            var currVal = input.val();

            if (currVal < 0 || currVal > 99) {
                // Invalid Value! Get old value
                input.val(prevVal);
            } else {
                input.data('old', currVal);
                refreshPrice();
            }

        });
        //
        $('input[type=radio][name=order_payment_type]').change(function () {
            if ($(this).val() == '3') {
                $('#payment-info').show();
            } else {
                $('#payment-info').hide();
            }
        });
        // Fix Browser which not support HTML input date
        if ($('[type="date"]').prop('type') != 'date') {
            $('[type="date"]').datepicker({dateFormat: 'yy-mm-dd'});
        }
    })

    function togglePrevious(enable) {
        toggleButton("previous", enable);
    }

    function toggleNext(enable) {
        toggleButton("next", enable);
    }

    function toggleFinish(enable) {
        toggleButton("finish", enable);
    }

    function toggleButton(buttonId, enable) {
        if (enable) {
            // Enable disabled button
            var button = $("#wizard").find('a[href="#' + buttonId + '-disabled"]');
            button.attr("href", '#' + buttonId);
            button.parent().removeClass();
        } else {
            // Disable enabled button
            var button = $("#wizard").find('a[href="#' + buttonId + '"]');
            button.attr("href", '#' + buttonId + '-disabled');
            button.parent().addClass("disabled");
        }
    }

    function refreshPrice() {
        var monthlyPrice = 0;
        var otherPrice = 0;
        var itemQty = 0;
        var itemPrice = 0;

        $(".product-item").each(function () {
            var itemId = $(this).data('id');
            var itemCategory = $(this).data('category');

            itemQty = $('#product-qty-' + itemId).val();
            itemPrice = parseFloat($('#product-' + itemId + '-price').html());
            if ('box' === itemCategory) {
                monthlyPrice = add(monthlyPrice, multiply(itemQty, itemPrice));
            } else {
                otherPrice = add(otherPrice, multiply(itemQty, itemPrice));
            }
        });

        // console.log('Total : '+total);
        $('#monthly-price').val(priceAddCur(monthlyPrice));
        $('#monthly-price-2').val(priceAddCur(monthlyPrice));
        $('#other-price').val(priceAddCur(otherPrice));
    }

    function resetOrderPreview() {
        $('#preview-account').html('');
        $('#preview-product').html('');
        $('#preview-checkinout').html('');
        $('#preview-summary').html('');

        $('#monthly-fee').val(0);
        $('#storage-month').val(0);
        $('#total-fee').val(0);
        $('#other-fee').val(0);
    }

    function createAccountPreview() {
        var previewAccountHtml = '';
        var accountDataArray = ['name', 'email', 'contact'];
        accountDataArray.forEach(buildAccountHtml);

        function buildAccountHtml(inputKey) {
            var label = $('label[for=' + inputKey + ']').text();
            var value = $('#' + inputKey).val();
            previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12">' + label + '</div><div class="col-md-8 col-xs-12">' + value + '</div></div>';
        }

        // Address = Address + city + state
        var selectedCity = $('#city option:selected').text();
        var cityAdd = '';
        var stateAdd = '';
        if (selectedCity == "-- 請選擇 --" || selectedCity == "-- Please Select --") {
            cityAdd = "";
        } else {
            cityAdd = selectedCity + ", ";
        }

        var selectedState = $('#state option:selected').text();

        if (selectedState == "-- 請選擇 --" || selectedState == "-- Please Select --") {
            stateAdd = "";
        } else {
            stateAdd = selectedState;
        }
        //Address 1
        var pickupVal = $('input[name=pickup_address_same]:checked').val();

        if (pickupVal == 0) {
            var cityAdd1 = '';
            var stateAdd1 = '';
            var selectedCity1 = $('#pickup_city option:selected').text();
            if (selectedCity1 == "-- 請選擇 --" || selectedCity1 == "-- Please Select --") {
                cityAdd1 = "";
            } else {
                cityAdd1 = selectedCity1 + ", ";
            }

            var selectedState1 = $('#pickup_state option:selected').text();
            if (selectedState1 == "-- 請選擇 --" || selectedState1 == "-- Please Select --") {
                stateAdd1 = "";
            } else {
                stateAdd1 = selectedState1;
            }
            var address1 = $('#pickup_address').val() + "," + cityAdd1 + stateAdd1;
        }


        var address = $('#address').val() + "," + cityAdd + stateAdd;
        previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step3.address"), false); ?></div><div class="col-md-8 col-xs-12">' + address + '</div></div>';
        if (pickupVal == 0) {
            previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step3.pickup_address"), false); ?></div><div class="col-md-8 col-xs-12">' + address1 + '</div></div>';
        }
        var special_instruction = $("#special_instruction").val()
        previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step3.special_instruction"), false); ?></div><div class="col-md-8 col-xs-12">' + special_instruction + '</div></div>';

        var walkup = $('input[name=walkup]:checked').val();
        walkup = (walkup==1)?'Yes':'No';
        previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step3.walkup"), false); ?></div><div class="col-md-8 col-xs-12">' + walkup + '</div></div>';

        var close_contact = $('input[name=mandatory_home_quarantine]:checked').val();
        close_contact = (close_contact==1)?'Yes':'No';
        previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step3.close_contact_quarantine"), false); ?></div><div class="col-md-8 col-xs-12">' + close_contact + '</div></div>';
        // University
        console.log('is University Student : ' + $('input[type="radio"][name="university_student"]:checked').val());
        if ($('input[type="radio"][name="university_student"]:checked').val() > 0) {
            previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step4.university_id"), false); ?></div><div class="col-md-8 col-xs-12">' + $('#university_id option:selected').text() + '</div></div>';
            previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step4.student_id"), false); ?></div><div class="col-md-8 col-xs-12">' + $('#student_id').val() + '</div></div>';
            previewAccountHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step4.wechat_id"), false); ?></div><div class="col-md-8 col-xs-12">' + $('#wechat_id').val() + '</div></div>';
        }

        $('#preview-account').append(previewAccountHtml);
    }

    //
    function createProductPreview() {
        var previewProductHtml = '<div class="row">';
        previewProductHtml += '<div class="col-md-6 col-xs-12"><?php echo e(trans("placeorder.step5.monthly_product_name"), false); ?></div>';
        previewProductHtml += '<div class="col-md-2 col-xs-6"><?php echo e(trans("placeorder.step5.monthly_product_price"), false); ?></div>';
        previewProductHtml += '<div class="col-md-2 col-xs-6"><?php echo e(trans("placeorder.step5.monthly_product_qty"), false); ?></div>';
        previewProductHtml += '<div class="col-md-2 col-xs-6"><?php echo e(trans("placeorder.step5.monthly_product_amount"), false); ?></div>';
        previewProductHtml += '</div>';

        var boxCount = 0;
        $(".product-item").each(function () {
            var itemId = $(this).data('id');
            var itemCategory = $(this).data('category');

            var itemNameLabel = $('#product-' + itemId + '-name').text();
            var itemQtyInput = $('#product-qty-' + itemId).val();
            var itemPriceLabel = $('#product-' + itemId + '-price').text();

            var itemQty = parseInt(itemQtyInput);
            var itemAmountLabel = multiply(itemQty, parseFloat(itemPriceLabel));

            if (itemQty > 0) {
                if (itemCategory === 'box')
                    boxCount += itemQty;

                previewProductHtml += '<div class="row">';
                previewProductHtml += '<div class="col-md-6 col-xs-12"><label id="preview-product-' + itemId + '-name">' + itemNameLabel + '</label></div>';
                previewProductHtml += '<div class="col-md-2 col-xs-4"><label id="preview-product-' + itemId + '-price"><?php echo e(trans("placeorder.currency"), false); ?>' + itemPriceLabel + '</label></div>';
                previewProductHtml += '<div class="col-md-2 col-xs-4"><label id="preview-product-' + itemId + '-qty">' + itemQtyInput + '</label></div>';
                previewProductHtml += '<div class="col-md-2 col-xs-4"><label id="preview-product-' + itemId + '-amount"><?php echo e(trans("placeorder.currency"), false); ?>' + itemAmountLabel + '</label></div>';
                previewProductHtml += '</div>';
            }
        });

        $('#preview-product').append(previewProductHtml);
    }

    function createCheckinoutPreview() {
        var previewCheckinoutHtml = '';
        //
        var emptyout_datetime = $('#emptyout_date_other').val() + ' ' + $('#emptyout_time_other').val();
        previewCheckinoutHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step3.emptyout"), false); ?></div><div class="col-md-8 col-xs-12">' + emptyout_datetime + '</div></div>';
        var checkin_datetime = $('#checkin_date_other').val() + ' ' + $('#checkin_time_other').val();
        previewCheckinoutHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step3.checkin"), false); ?></div><div class="col-md-8 col-xs-12">' + checkin_datetime + '</div></div>';
        var checkout_datetime = $('#checkout_date_other').val() + ' ' + $('#checkout_time_other').val();
        previewCheckinoutHtml += '<div class="row"><div class="col-md-4 col-xs-12"><?php echo e(trans("placeorder.step3.checkout"), false); ?></div><div class="col-md-8 col-xs-12">' + checkout_datetime + '</div></div>';
        //
        $('#preview-checkinout').append(previewCheckinoutHtml);
    }

    function createSummaryPreview() {
        var previewSummaryHtml = '';

        // console.log($('#monthly-price').val()+':'+$('#other-price').val());

        var monthlyFee = parseFloat(priceRemoveCur($('#monthly-price').val()));
        var storageMonth = calcDateDiffByMonth($("#checkin_date_other").val(), $("#checkout_date_other").val());
        var otherFee = parseFloat(priceRemoveCur($('#other-price').val()));

        // console.log(storageMonth+':'+monthlyFee);
        // console.log(multiply(storageMonth, monthlyFee)+':'+otherFee);

        var totalFee = multiply(storageMonth, monthlyFee) + otherFee;

        previewSummaryHtml += '<div class="row">';
        previewSummaryHtml += '<div class="col-md-6 col-xs-12"><?php echo e(trans("placeorder.step5.monthly-fee"), false); ?></div>';
        previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>' + priceAddCur(monthlyFee) + '</label></div>';
        previewSummaryHtml += '</div>';

        previewSummaryHtml += '<div class="row">';
        previewSummaryHtml += '<div class="col-md-6 col-xs-12"><?php echo e(trans("placeorder.step5.storage-month"), false); ?></div>';
        previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>' + storageMonth + '</label></div>';
        previewSummaryHtml += '</div>';

        previewSummaryHtml += '<div class="row">';
        previewSummaryHtml += '<div class="col-md-6 col-xs-12"><?php echo e(trans("placeorder.step5.other-fee"), false); ?></div>';
        previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>' + priceAddCur(otherFee) + '</label></div>';
        previewSummaryHtml += '</div>';

        previewSummaryHtml += '<div class="row">';
        previewSummaryHtml += '<div class="col-md-6 col-xs-12"><?php echo e(trans("placeorder.step5.total-fee"), false); ?></div>';
        previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>' + priceAddCur(totalFee) + '</label></div>';
        previewSummaryHtml += '</div>';

        $('#monthly-fee').val(monthlyFee);
        $('#storage-month').val(storageMonth);
        $('#other-fee').val(otherFee);
        $('#total-fee').val(totalFee);

        $('#preview-summary').append(previewSummaryHtml);
    }

    function createOrderPreview() {
        createAccountPreview();

        createProductPreview();

        createCheckinoutPreview();

        createSummaryPreview();
    }

    function calcDateDiffByMonth(d1Str, d2Str) {
        var d1 = new Date(d1Str);
        var d2 = new Date(d2Str);

        var yearDiff = d2.getFullYear() - d1.getFullYear();
        var monthDiff = d2.getMonth() - d1.getMonth();
        var dayDiff = d2.getDate() - d1.getDate();

        var result = (yearDiff * 12) + monthDiff + ((dayDiff > 0) ? 1 : 0);
        // console.log(yearDiff+':'+monthDiff+':'+dayDiff+':'+result);
        return result;
    }

    function calcProductPrice(productId, newPrice) {
        // if have promotion code, use promoition price
        // console.log('['+productId+'] Have Promotion '+$('#product-'+productId+'-price').data('promotion'));
        //
        if ($('#product-' + productId + '-price').data('promotion')) {
            calcBestProductPrice(productId, newPrice);
        }
        // if not, update by selected storage month
        else {
            $('#product-' + productId + '-price').html(parseFloat(newPrice));
        }
    }

    function calcBestProductPrice(productId, newPrice) {
        var existPrice = parseFloat($('#product-' + productId + '-price').html());
        // console.log('Product ID ['+productId+'] : [E]'+existPrice+' [N]'+newPrice);
        if (newPrice < existPrice) {
            $('#product-' + productId + '-price').html(parseFloat(newPrice));
        }
    }

    function priceAddCur(price) {
        return '<?php echo e(trans("placeorder.currency"), false); ?> ' + price;
    }

    function priceRemoveCur(price) {
        return price.replace('<?php echo e(trans("placeorder.currency"), false); ?> ', '');
    }

    function multiply(arg1, arg2) {
        var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
        try {
            m = s1.split(".")[1].length
        } catch (e) {
        }
        try {
            m = s2.split(".")[1].length
        } catch (e) {
        }
        return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m);
    }

    function add(arg1, arg2) {
        var r1, r2, m;
        try {
            r1 = arg1.toString().split(".")[1].length
        } catch (e) {
            r1 = 0
        }
        try {
            r2 = arg2.toString().split(".")[1].length
        } catch (e) {
            r2 = 0
        }
        m = Math.pow(10, Math.max(r1, r2))
        return (arg1 * m + arg2 * m) / m;
    }
</script>
<script>
    function fillClientData(data) {
        $('#login_row').attr('style', 'display: none !important');

        $('#somsclient_id').val(data.id);
        $('#name').val(data.name);
        $('#name').prop('disabled', true);
        $('#email').val(data.email);
        $('#email').prop('disabled', true);
        $('#contact').val(data.contact);
        var address = ((data.address1) ? data.address1 : '') + ((data.address2) ? data.address2 : '');
        $('#address').val(address);
        $('#city').val(data.city_id);
        $('#state').val(data.state_id);

        $('#university_student_yes').click();

        $('#university_id').val(data.university_id);
        $('#student_id').val(data.student_id);
        $('#wechat_id').val(data.wechat_id);
        $(".ifloggedIn").text("<?php echo app('translator')->get('common.loggedIn'); ?>")
    }

    $(function () {
        // Click Login Button to AJAX login
        $('#login_btn').click(function () {
            // console.log('login_btn click');
            //
            $('#login_error_div').hide();
            $('#login_error_div').html('');
            //
            $.post("<?php echo e(route('ajax-client-login'), false); ?>", {
                email: $("#login_email").val(),
                password: $("#login_password").val()
            })
                .done(function (data) {
                    // login success
                    // console.log(data);
                    if (data) {
                        fillClientData(data);
                    }
                    // Login fail
                    else {
                        $('#login_error_div').show();
                        $(".ifloggedIn").text("<?php echo app('translator')->get('common.loggedIn'); ?>")
                        $('#login_error_div').html('Login fail. Incorrect Email / Password');
                    }
                })
                .fail(function () {
                });
        });
        // Click Promotion Button show input Promotion Code Panel
        $('#add-promo-btn').click(function () {
            // console.log('add-promo-btn-click');
            $('#add-promo-div').attr('style', 'display: none !important');
            $('#input-promo-div').css('display', 'flex');
            $('#input-promo-error-div').css('display', 'flex');
        });
        // Show or hide Pickup Address Layout when change yes or no
        $('input[type="radio"][name="pickup_address_same"]').click(function () {
            // console.log($(this).val());
            $('#pickup_address_same_layout').toggle();
        });
        // Show or hide Pickup Address Layout when change yes or no
        $('input[type="radio"][name="university_student"]').click(function () {
            // console.log($(this).val());
            $('#university_student_layout').toggle();
        });
        // Send AJAX when change storage period
        $('input[type="radio"][name="order_storage_period"]').click(function () {
            // console.log($(this).val());
            // console.log($("input[name='order_storage_period']:checked").attr('id'));
            //
            $.get("<?php echo e(route('ajax-get-storage-period-item'), false); ?>", {storage_period_id: $(this).val()})
                .done(function (data) {
                    // success data
                    // console.log(data);
                    // Check if have storage period price
                    $(".product-price-label").each(function (index) {
                        // console.log( index + ": " + $( this ).text() );
                        var item_id = $(this).data('id');
                        var item_sp_price = parseFloat(data[item_id]);
                        // console.log( item_sp_price );
                        if (isNaN(item_sp_price)) {
                            // $("#product-"+item_id+"-price").html(parseFloat($(this).data('original-price')));
                            calcProductPrice(item_id, parseFloat($(this).data('original-price')));
                        } else {
                            // $("#product-"+item_id+"-price").html(item_sp_price);
                            calcProductPrice(item_id, item_sp_price);
                        }
                    });

                    refreshPrice();
                })
                .fail(function () {
                });
        });
        // Click default Storage Period By Default
        $("input[name='order_storage_period']:checked").click();
    });
</script>
<?php
$clientData = null;
if (Auth::guard('somsclient')->check()) {
    $clientData = Auth::guard('somsclient')->user()->only(['id', 'name', 'email', 'contact', 'address1', 'address2', 'city_id', 'state_id', 'university_id', 'student_id', 'wechat_id']);
}
?>
<script>

    $(function () {
        var clientData = <?php echo json_encode($clientData); ?>;
        if (clientData) {
            // console.log(clientData);
            fillClientData(clientData);
        }
    });
</script>
<script>
    $(function () {
        // Create a Stripe client.
        console.log("hello");
        //var stripe = Stripe('<?php echo e(env("STRIPE_KEY"), false); ?>');
        var stripe = Stripe('pk_test_mGosxq40Vdq5hHd6UguX94gw');
        //var stripe = Stripe('<?php echo e(env("STRIPE_TEST_KEY"), false); ?>');
        // Create an instance of Elements.
        var elements = stripe.elements();
        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeorder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        // Create an instance of the card Element.
        var card = elements.create('card', {style: style, hidePostalCode: true});
        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');
        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        // Handle form submission.
        var submitHandler = function (event) {
            toggleFinish(false);
            console.log('submitHandler run');
            if ($('input[type=radio][name=order_payment_type]:checked').val() == '3') {
                event.preventDefault();
                stripe.createToken(card).then(function (result) {
                    if (result.error) {
                        // Inform the user if there was an error.
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;

                        toggleFinish(true);
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            }
        }

        $("#placeorder").bind("submit", submitHandler);
    })

    // Submit the form with the token ID.
    function stripeTokenHandler(token) {
        console.log('on stripeTokenHandler');
        // Insert the token ID into the form so it gets submitted to the server
        var paymentform = document.getElementById('placeorder');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        paymentform.appendChild(hiddenInput);
        // Submit the form
        paymentform.submit();
    }
</script>
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
        } else {
            $(".remail-errormsg").text('Enter valid email')
        }
    }

    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    $(function () {
        $('#rotp').keyup(function () {
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
        if (password != confirmPassword) {
            $(".rconfirmpassword-errormsg").text('Password not matched')
        } else {
            $.ajax({
                url: "<?php echo e(url('ajax/change-password'), false); ?>",
                type: 'POST',
                dataType: 'json',
                data: {'email': email, 'password': password, 'otp': otp},
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
<?php $__env->startComponent('components.wechatpay-success-auto-redirect'); ?>
<?php echo $__env->renderComponent(); ?>
<?php $__env->startComponent('modal-product-detail', ['items' => $items]); ?>
<?php echo $__env->renderComponent(); ?>
</body>
</html>
<?php /**PATH /var/www/html/soms_uat/soms/resources/views/index-new.blade.php ENDPATH**/ ?>