<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Lang;
Route::get('lang/{key}', function ($key) {

    session()->put('locale', $key);
    return redirect()->back();
});

Route::prefix('ajax')->group(function() {

  Route::post('/email/validate', 'AjaxController@emailValidate')->name('ajax-email-validate');

  Route::post('/coupon-code/validate', 'AjaxController@couponCodeValidate')->name('ajax-coupon-code-validate');

  Route::post('/university/info', 'AjaxController@universityInfo')->name('ajax-university-info');

  Route::get('/order/payment-status', 'AjaxController@getOrderPaymentStatus')->name('ajax-get-order-payment-status');

  Route::get('/order/item-price', 'AjaxController@getOrderItemPrice')->name('ajax-get-order-item-price');
  // Route::post('/university/location', 'AjaxController@universityLocation')->name('ajax-university-location');

  Route::get('/university/options', 'AjaxController@universityOptions')->name('ajax-university-options');

  Route::get('/storage-period/item', 'AjaxController@getStoragePeriodItem')->name('ajax-get-storage-period-item');

  Route::post('/promotion-code/validate', 'AjaxController@promotionCodeValidate')->name('ajax-promotion-code-validate');

  Route::post('/client/login', 'AjaxController@clientLogin')->name('ajax-client-login');

  Route::post('send-email-otp', 'AjaxController@sendemailOtp');
  Route::post('validate-otp', 'AjaxController@validateOtp');
  Route::post('change-password', 'AjaxController@changePassword');

});

Route::prefix('test')->group(function() {
  Route::get('/', 'TestController@index')->name('test');

  Route::get('/email', 'TestController@email')->name('test-email');
  Route::get('/email/{id}', 'TestController@email')->name('test-email');
  Route::get('/send/email', 'TestController@sendEmail')->name('test-send-email');
  Route::get('/send/email/{id}', 'TestController@sendEmail')->name('test-send-email');
  Route::get('/payment/{id}', 'TestController@payment')->name('test-payment');
  Route::get('/order/query', 'TestController@testOrderQuery')->name('test-order-query');
  Route::get('/order/list', 'TestController@testOrderList')->name('test-order-list');
  Route::get('/order/submit', 'TestController@orderSubmit')->name('test-order-submit');
});

Route::get('/home', 'HomeController@index')->name('home');

Route::get('testlang', 'HomeController@testLamng');

// Route::get('/', function () {
//     return view('welcome');
// });
Auth::routes();

Route::get('/', 'MainController@index')->name('index');

Route::post('/order/submit', 'MainController@orderSubmit')->name('order-submit');
// Payment Callback
Route::get('/alipay/return', 'PaymentController@alipayReturn')->name('alipay-return');
Route::post('/wechatpay/return', 'PaymentController@wechatpayReturn')->name('wechatpay-return');
// For auto redirect when order status changed to success
Route::get('/wechatpay/success', 'PaymentController@wechatpaySuccess')->name('wechatpay-success');

Route::group(
[
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
], function(){
  //...
  Route::prefix('client')->group(function() {

    Route::get('/', 'SomsClientController@index')->name('somsclient.dashboard');
    Route::get('dashboard', 'SomsClientController@index')->name('somsclient.dashboard');

    // Route::get('register', 'AuthSomsClient\RegisterController@register')->name('somsclient.register');
    // Route::post('register', 'AuthSomsClient\RegisterController@registerSubmit')->name('somsclient.register.submit');

    Route::get('login', 'AuthSomsClient\LoginController@login')->name('somsclient.login');
    Route::post('login', 'AuthSomsClient\LoginController@loginSubmit')->name('somsclient.login.submit');

    Route::get('logout', 'AuthSomsClient\LoginController@logout')->name('somsclient.logout');
    Route::post('logout', 'AuthSomsClient\LoginController@logout')->name('somsclient.logout');

    Route::get('order/create', 'SomsClientController@orderCreate')->name('somsclient.order.create');
    Route::post('order/create', 'SomsClientController@orderCreateSubmit')->name('somsclient.order.create.submit');

    // Route::get('order/update/{id}', 'SomsClientController@orderUpdate')->name('somsclient.order.update');
    // Route::post('order/update/{id}', 'SomsClientController@orderUpdateSubmit')->name('somsclient.order.update.submit');
    Route::get('order/update/{id}', 'SomsClientController@orderUpdateNew')->name('somsclient.order.update');
    Route::post('order/update/{id}', 'SomsClientController@orderUpdateSubmitNew')->name('somsclient.order.update.submit');

    // Route::get('client/update', 'SomsClientController@clientUpdate')->name('somsclient.client.update');
    // Route::post('client/update', 'SomsClientController@clientUpdateSubmit')->name('somsclient.client.update.submit');
    Route::get('client/update', 'SomsClientController@clientUpdateNew')->name('somsclient.client.update');
    Route::post('client/update', 'SomsClientController@clientUpdateSubmitNew')->name('somsclient.client.update.submit');

    Route::get('payment-invoice/send/{id}', 'SomsClientController@sendPaymentInvoice')->name('somsclient.payment-invoice.send');
    Route::get('payment-invoice/view/{id}', 'SomsClientController@viewPaymentInvoice')->name('somsclient.payment-invoice.view');
  });
  //
  // Route::prefix('new')->group(function(){
  //
  //   Route::get('/', 'MainController@indexNew')->name('index-new');
  //
  // });

});
