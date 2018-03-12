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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Auth\Ldap')
    ->prefix('auth/ldap/')
    ->group(function() {
        Route::post('login', 'LdapController@login')->name('auth.ldap.login');
    });

Route::namespace('Indicators')
    ->prefix('indicators/seller')
    ->group(function () {

        Route::get('goal-percentage/{sellerId}', 'SellerController@goalPercentage');
    });