<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['xss', 'web'])
    ->name('saas-connector.')
    ->namespace('Modules\SaasConnector\Http\Controllers')
    ->group(function () {
        Route::any('/login-with-token', 'LoginWithTokenController@index')->name('login-with-token');
        Route::get('/ads-bar', 'AdsBarController@index')->name('ads-bar');
    });
