<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->get('/auth', 'AuthController@index')->name('admin.auth.home');
    // 1. Order Page - Search By University
    // 2. Client URL By Order -> To Client Edit Page
    // 3. Order Edit Page
    Route::prefix('soms')->group(function(Router $router) {

      Route::resource('orders', 'SomsOrderController');
      Route::resource('clients', 'SomsClientController');
      Route::resource('promotions', 'SomsPromotionController');
      Route::resource('storageperiods', 'SomsStoragePeriodController');
      Route::resource('items', 'SomsItemController');
      Route::resource('payments', 'SomsOrderPaymentController');

      $router->any('/orders/createByClient/{id}', 'SomsOrderController@createByClient')->name('admin.soms.orders.create-by-client');
      Route::post('orders/wechatpay/return', 'SomsOrderController@weChatPayReturn')->name('admin.soms.orders.wechatpay-return');

      Route::get('payments/mark-paid/{id}', 'SomsOrderPaymentController@markAsPaid');
      Route::get('payments/mark-cancelled/{id}', 'SomsOrderPaymentController@markAsCancelled');

      Route::get('orders/payment-invoice/send/{id}', 'SomsOrderController@processSendExtendedInvoice')->name('admin.soms.orders.payment-invoice.send');

      // $router->get('/orders', 'SomsOrderController@list')->name('admin.soms.orders');
      // $router->any('/orders/{id}/edit', 'SomsOrderController@edit')->name('admin.soms.orders.edit');
      //
      // $router->get('/clients/{uid?}', 'SomsClientController@list')->name('admin.soms.clients');
      // $router->any('/clients/{id}/edit', 'SomsClientController@edit')->name('admin.soms.clients.edit');

    });

});
