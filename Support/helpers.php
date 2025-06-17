<?php

use Modules\SaasConnector\Support\SaasHelper;

if (!function_exists('getSaasWebsiteInfoFromServer')) {
    function getSaasWebsiteInfoFromServer()
    {
        return SaasHelper::getSaasWebsiteInfoFromServer();
    }
}

if (!function_exists('getWebsiteManagerUrl')) {
    function getWebsiteManagerUrl()
    {
        return SaasHelper::getWebsiteManagerUrl();
    }
}

if (!function_exists('validateLoginWithTokenSaas')) {
    function validateLoginWithTokenSaas($token)
    {
        return SaasHelper::validateLoginWithToken($token);
    }
}
