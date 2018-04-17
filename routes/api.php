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

Route::middleware(['auth:api', 'verify_api_token'])
    ->group(function () {

        /** Indicadores  */
        Route::namespace('Indicators')
            ->prefix('indicators')
            ->group(function () {

                Route::prefix('seller')
                    ->group(function () {
                        Route::get('goal-percentage/{sellerId}', 'SellerController@goalPercentage');

                        /** Meta x Vendas Diário*/
                        Route::get('goal-percentage-daily/{sellerId}', 'SellerController@goalPercentageDaily');

                        Route::get('services/{accessLevel}/{sellerId}', 'SellerController@services');
                    });

                Route::prefix('sale')
                    ->group(function () {

                        /** Meta x Vendas Geral */
                        Route::get('goal-percentage/{accessLevel}/{userId}', 'SaleController@goalPercentage')
                            ->where(['accessLevel' => '[0-9]+', 'userId' => '[0-9]+']);

                        Route::get('get-departments','SaleController@getDepartments');

                        /** Meta x Vendas Geral com Filtro */
                        Route::get('get-filters/{userId}', 'SaleController@goalPercentageWithFilter');

                        Route::get('general-sales/{accessLevel}/{userId}', 'SaleController@generalSales')
                            ->where(['accessLevel' => '[0-9]+', 'userId' => '[0-9]+']);

                        Route::get('general-sales-services/{accessLevel}/{userId}', 'SaleController@generalSalesServices')
                            ->where(['accessLevel' => '[0-9]+', 'userId' => '[0-9]+']);

                    });
            });

        /** Users */
        Route::namespace('Users')
            ->prefix('users')
            ->group(function () {

                Route::namespace('Profiles')
                    ->prefix('profiles')
                    ->group(function () {
                        /** Menu Android do usuário */
                        Route::get('android-menu/{sellerId}', 'ProfileController@androidMenu');
                    });
            });
    });

/** Autenticação */
Route::namespace('Auth\Ldap')
    ->prefix('auth/ldap/')
    ->group(function() {
        Route::post('login', 'LdapController@login')->name('auth.ldap.login');
    });
