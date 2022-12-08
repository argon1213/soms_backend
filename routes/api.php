<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function() {
    // Route::middleware(['cors'])->group(function() {
        Route::get('/products', 'ApiController@getProducts')->name('api-get-products-info'); // it's working well
        Route::post('/login', 'ApiController@login')->name('api-login');
        Route::post('/register', 'ApiController@register')->name('api-register');
        Route::post('/order', 'ApiController@orderSubmit')->name('api-order-submit');
        Route::post('/yedpayOrder', 'ApiController@yedpayOrderSubmit')->name('api-yedpayOrder-submit');
        Route::get('/prices', 'ApiController@getStoragePeriodItem')->name('api-prices');
        Route::post('/promo-code', 'ApiController@promotionCodeValidate')->name('promo-code');

        Route::prefix('/client')->group(function() {
            Route::post('/getUser', 'ApiController@fetchUser')->name('api-getUser');
            Route::post('/account/update', 'ApiController@clientUpdate')->name('account-update');
            Route::post('/changePassword', 'ApiController@ChangePassword')->name('change-password');
            Route::post('/updateOrder', 'ApiController@updateOrder')->name('update-order');
            Route::post('/getOrders', 'ApiController@getOrders')->name('get-orders');
            Route::post('/fetchCurrentOrder', 'ApiController@fetchCurrentOrder')->name('fetch-current-order');
            Route::post('/emailOtpForgotPassword', 'ApiController@sendEmailOtp')->name('forgot-password-email-otp');
            Route::post('/resetPassword', 'ApiController@resetPassword')->name('reset-password');
        });

        Route::prefix('/admin')->group(function() {
            Route::post('fetchUniversities', 'ApiAdminController@index')->name('admin-dashboard');
            Route::post('fetchClients', 'ApiAdminController@fetchClients')->name('clients-list');
            Route::post('fetchPeriods', 'ApiAdminController@fetchPeriods')->name('storage-period-list');
            Route::post('fetchPayments', 'ApiAdminController@fetchPayments')->name('payment-list');
        });
});