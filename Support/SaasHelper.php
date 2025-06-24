<?php

namespace Modules\SaasConnector\Support;

use GuzzleHttp\Client;

class SaasHelper
{
    public static function getSaasWebsiteInfoFromServer()
    {
        static $checkWebsiteCache;

        if (is_array($checkWebsiteCache) && !empty($checkWebsiteCache)) {
            return $checkWebsiteCache;
        }

        $websiteManagerUrl = self::getWebsiteManagerUrl();
        if (!$websiteManagerUrl) {
            return false;
        }

        $checkDomain = site_url();
        $parseUrl = parse_url($checkDomain);

        if (!empty($parseUrl['host'])) {
            try {
                $checkDomain = $parseUrl['host'];
                $client = new Client();
                $response = $client->request('GET', $websiteManagerUrl . '/api/websites/website-info?domain=' . $checkDomain);

                if ($response->getStatusCode() == 200) {
                    $checkWebsiteCache = json_decode($response->getBody(), true);
                    return $checkWebsiteCache;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    public static function getWebsiteManagerUrl()
    {

        $websiteManagerUrl = config('modules.saas-connector.manager_url', env('SAAS_MANAGER_URL'));
        if(!$websiteManagerUrl){
            $websiteManagerUrl = get_option('package_manager_url', 'panel');
        }

        //remove end slash

        if($websiteManagerUrl){
            $websiteManagerUrl = rtrim($websiteManagerUrl, '/');
        }

        return $websiteManagerUrl;
    }

    public static function validateLoginWithToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $websiteManagerUrl = self::getWebsiteManagerUrl();
        if (!$websiteManagerUrl) {
            return false;
        }
        $domain = site_url();
        //remove end slash

        $websiteManagerUrl = rtrim($websiteManagerUrl, '/');

        $client = new Client();
        $response = $client->request('GET', $websiteManagerUrl . '/api/websites/verify-login-token', [
            'form_params' => [
                'token' => $token,
                'domain' => $domain
            ]
        ]);


        try {

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    public static function getBranding()
    {
        $brandingFile = storage_path('branding_saas.json');
        if (is_file($brandingFile)) {
            $branding = json_decode(file_get_contents($brandingFile), true);
            if (!empty($branding)) {
                return $branding;
            }
        }

        $brandingFileUser = storage_path('branding.json');
        if (is_file($brandingFileUser)) {
            $branding = @json_decode(file_get_contents($brandingFileUser), true);
            if (!empty($branding)) {
                return $branding;
            }
        }

        return false;
    }
}
