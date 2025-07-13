<?php

namespace Modules\SaasConnector\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MicroweberPackages\User\Models\User;

class LoginWithTokenController extends Controller
{
    public function index(Request $request)
    {
        $redirect = $request->get('redirect', false);
        $token = $request->get('token', false);

        if (empty($token)) {
            return redirect(admin_url());
        }

        $syncAdminDetails = $request->get('sync_admin_details', false);
        $validateLoginWithToken = validateLoginWithTokenSaas($token);

        if ($validateLoginWithToken) {

            $user = User::where('is_admin', '=', '1')->first();
            if ($user !== null) {
                if ($syncAdminDetails) {
                    if (isset($validateLoginWithToken['user']['email'])) {
                        if ($user->email != $validateLoginWithToken['user']['email']) {
                            $user->email = $validateLoginWithToken['user']['email'];
                            $user->save();
                        }
                    }
                }
                auth()->login($user);
            }
        }

        if ($redirect) {
            return redirect($redirect);
        }

        return redirect(admin_url());
    }
}
