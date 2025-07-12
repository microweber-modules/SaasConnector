<?php

use Illuminate\Support\Facades\Route;
use Modules\SaasConnector\Http\Controllers\LoginWithTokenController;
use Modules\SaasConnector\Http\Controllers\AdsBarController;

Route::middleware('web')
    ->name('saas-connector.')
    ->group(function () {
        Route::any('/login-with-token', [LoginWithTokenController::class, 'index'])->name('login-with-token');
        Route::get('/ads-bar', [AdsBarController::class, 'index'])->name('ads-bar');

        // Clear cache route
        Route::get('/saas-clearcache', function (){
            $token = request()->get('token', false);

            if (validateLoginWithTokenSaas($token)) {
                clearcache();
                return 'Cache cleared';
            }
            return redirect(admin_url());
        })->name('saas-clearcache');

        // Validate password preview route
        Route::get('/validate-password-preview', function () {
            $checkDomain = site_url();
            $parseUrl = parse_url($checkDomain);
            $checkDomain = $parseUrl['host'];

            $password = request()->get('password_preview', false);
            $password = trim($password);

            $websiteManagerUrl = getWebsiteManagerUrl();
            if (!$websiteManagerUrl) {
                return redirect(site_url());
            }

            $verifyUrl = $websiteManagerUrl . '/api/websites/validate-password-preview';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $verifyUrl,
                CURLOPT_USERAGENT => 'Microweber',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => array(
                    'password' => $password,
                    'domain' => $checkDomain
                )
            ));
            $verifyCheck = curl_exec($curl);
            $verifyCheck = @json_decode($verifyCheck, true);
            curl_close($curl);

            if (isset($verifyCheck['success']) && $verifyCheck['success']) {
                app()->user_manager->session_set('hidden_preview', true);
                return redirect(site_url());
            } else {
                return app()->user_manager->redirect(site_url() . '?hidden_preview=1&error=wrong_password');
            }
        })->name('validate-password-preview');
    });
