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

/** Cockpit API */
Route::middleware(['auth:api', 'verify_api_token'])
    ->group(function () {

        /** Indicadores  */
        Route::namespace('Indicators')
            ->prefix('indicators')
            ->group(function () {

                Route::post('/', 'IndexController@run');
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

/** Combat API */    
Route::middleware(['auth:api_combat', 'verify_api_token'])
    ->group(function () {

        /** Combat  */
        Route::namespace('Combat')
            ->prefix('combat')
            ->group(function () {

                Route::post('/', 'IndexController@run');
            });
        
    });


/** Autenticação Cockpit */
Route::namespace('Auth\Ldap')
    ->prefix('auth/ldap/')
    ->group(function() {
        Route::post('login', 'LdapController@login')->name('auth.ldap.login');
        
        Route::post('simple-login', 'LdapController@simpleLogin')->name('auth.ldap.simple-login');

        Route::post('login-combat', 'CombatController@login')->name('auth.ldap.login-combat');
    });
