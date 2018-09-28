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

Route::get('/', function () {
    $iosVersion = \App\Models\AppVersion::select(['version'])->where('platform', 'ios')->first();
    return view('home', ['iosVersion' => isset($iosVersion->version) ? $iosVersion->version : null]);
});

Route::middleware('auth.basic')
    ->prefix('/dev')
    ->group(function () {
        Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

        Route::prefix('/apps')
            ->namespace('\Apps')
            ->group(function () {
                
                Route::get('/', 'AppController@index');

                Route::post('/update', 'AppController@update');
            });

        Route::prefix('/analytics')
            ->namespace('\Apps')
            ->group(function () {

                Route::get('/', 'AppAnalyticController@index');
            });    
    });
