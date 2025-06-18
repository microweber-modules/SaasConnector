<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['admin'])
    ->name('admin.saas-connector.')
    ->group(function () {
        Route::get('/setup-wizard', [
                \Modules\SaasConnector\Http\Controllers\Admin\SetupWizardController::class,
                'index'
            ]
        )->name('setup-wizard');


        Route::post('/install-template', [
            \Modules\SaasConnector\Http\Controllers\Admin\SetupWizardController::class,
            'installTemplate'])
            ->name('install-template');
    });

Route::middleware(['xss', 'web'])
    ->name('saas-connector.')
    ->namespace('Modules\SaasConnector\Http\Controllers')
    ->group(function () {
        Route::any('/login-with-token', 'LoginWithTokenController@index')->name('login-with-token');
        Route::get('/ads-bar', 'AdsBarController@index')->name('ads-bar');
    });
