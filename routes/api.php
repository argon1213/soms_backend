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
    // });
});