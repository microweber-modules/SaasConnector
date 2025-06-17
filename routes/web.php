<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['admin'])
    ->prefix('admin')
    ->name('admin.saas-connector.')
    ->namespace('Modules\SaasConnector\Http\Controllers\Admin')
    ->group(function () {
        Route::get('/setup-wizard', 'SetupWizardController@index')->name('setup-wizard');
        Route::post('/install-template', 'SetupWizardController@installTemplate')->name('install-template');
    });

Route::middleware(['xss', 'web'])
    ->name('saas-connector.')
    ->namespace('Modules\SaasConnector\Http\Controllers')
    ->group(function () {
        Route::any('/login-with-token', 'LoginWithTokenController@index')->name('login-with-token');
        Route::get('/ads-bar', 'AdsBarController@index')->name('ads-bar');
    });
