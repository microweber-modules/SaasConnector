<?php

namespace Modules\SaasConnector\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdsBarController extends Controller
{
    public function index(Request $request)
    {
        $saasUrl = getWebsiteManagerUrl();
        $isLiveEdit = $request->get('live_edit', false);

        $brandName = 'Microweber';
        if (isset($branding['brand_name'])) {
            $brandName = $branding['brand_name'];
        }

        return view('saas_connector::ads-bar', [
            'saasUrl' => $saasUrl,
            'brandName' => $brandName,
            'isLiveEdit' => $isLiveEdit
        ]);
    }
}
