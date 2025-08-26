<?php

namespace Modules\SaasConnector\Providers;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use MicroweberPackages\LaravelModules\Providers\BaseModuleServiceProvider;

class SaasConnectorServiceProvider extends BaseModuleServiceProvider
{
    protected string $moduleName = 'SaasConnector';
    protected string $moduleNameLower = 'saas_connector';

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        $this->loadRoutesFrom(module_path($this->moduleName, 'routes/web.php'));
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {

        $this->bindEvents();
    }

    /**
     * Bind SaaS Connector events
     */
    private function bindEvents(): void
    {

        Filament::serving(function () {
            if (is_ajax()) {
                return;
            }
            if (!user_id()) {
                return;
            }

            $script = '';

            $panel = Filament::getCurrentPanel();
            $active_template = app()->option_manager->get('current_template', 'template');
            $urlPath = url_path();
            $setupWizardUrl = admin_url('setup-wizard');
            //if temklate is bootrap we wil lreriect the user to the setup wirzard
            if (str_ends_with($urlPath, 'live-edit') && strtolower($active_template) == 'bootstrap') {


                $script .= '
            <script>
                setTimeout(() => {
                               window.location.href = "' . $setupWizardUrl . '";
                }, 500);
            </script>';


            }



            $script .= '

<script>
    setTimeout(() => {

          if (typeof clarity !== "undefined") {
            window.clarity(
                "identify",
                "' . user_email() . '",
                "' . md5(user_email()) . '",
                "' . url_path() . '"
            );

            window.clarity("consentv2", {
                ad_storage: "granted",
                analytics_storage: "granted"
            });
        }


        if (typeof posthog !== "undefined") {

            posthog.opt_in_capturing()
            posthog.startSessionRecording()

            posthog.identify(
                "' . md5(user_email()) . '",
                {
                    email: "' . user_email() . '"
                }
            );
        }
    }, 500);
</script>

                ';
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn() => new HtmlString($script)
            );
        });

        // Frontend scripts handling
        event_bind('mw.init', function () {

            if (is_ajax()) {
                return;
            }

//dd(user_id());
            // Get website info from SaaS server
            $checkWebsite = getSaasWebsiteInfoFromServer();

            $hasActiveSubscription = false;
            if (isset($checkWebsite['activeSubscription']) && !empty($checkWebsite['activeSubscription'])) {
                $hasActiveSubscription = true;
            }
            if (!defined('MW_WHITE_LABEL_HAS_ACTIVE_SUBSCRIPTION')) {
                define('MW_WHITE_LABEL_HAS_ACTIVE_SUBSCRIPTION', $hasActiveSubscription);
            }



            // Handle website subscription status and preview mode
            if (isset($checkWebsite['success'])) {

                if (mw()->ui->enable_service_links()) {

                    $saasUrl = getWebsiteManagerUrl();

                    \MicroweberPackages\LiveEdit\Facades\LiveEditManager::getMenuInstance('top_right_menu')
                        ->addChild('My websites', [
                            'attributes' => [
                                'id' => 'js-live-edit-my-websites-link',
                                'href' => $saasUrl . '/projects',
                                'target' => '_blank',

                                'icon' => '<svg viewBox="0 0 40 32.29">
                                                                <path
                                                                    d="M40 3v26c0 .8-.3 1.5-.9 2.1-.6.6-1.3.9-2.1.9H3c-.8 0-1.5-.3-2.1-.9-.6-.6-.9-1.3-.9-2.1V3C0 2.2.3 1.5.9.9 1.5.3 2.2 0 3 0h34c.8 0 1.5.3 2.1.9.6.6.9 1.3.9 2.1zM3 8.45h34V3H3v5.45zm0 6.45V29h34V14.9H3zM3 29V3v26z"/>
                                                            </svg>'
                            ]
                        ]);


                    \MicroweberPackages\LiveEdit\Facades\LiveEditManager::getMenuInstance('top_right_menu')
                        ->menuItems
                        ->getChild('My websites')
                        ->setExtra('orderNumber', 13);

                }

                /*            // Admin sidebar button for "My Websites"
                            event_bind('mw.admin.sidebar.li.first', function () {
                                $saasUrl = getWebsiteManagerUrl();

                                if ($saasUrl) {
                                    echo '<a href="' . $saasUrl . '/projects"
                                    style="border-radius: 40px;" class="btn btn-outline-primary">
                               <i class="mdi mdi-arrow-left"></i> &nbsp; My Websites
                            </a>';
                                }
                            });
            */


                // Append admin panel scripts
                if (isset($checkWebsite['appendScriptsAdminPanel']) && !empty($checkWebsite['appendScriptsAdminPanel'])) {

                    admin_head($checkWebsite['appendScriptsAdminPanel']);

                }


                event_bind('mw.front', function () use ($checkWebsite) {

                    $hasActiveSubscription = false;
                    if (isset($checkWebsite['activeSubscription']) && !empty($checkWebsite['activeSubscription'])) {
                        $hasActiveSubscription = true;
                    }


                    if (!$hasActiveSubscription) {

                        $canISeeTheWebsite = false;
                        if (app()->user_manager->session_get('hidden_preview')) {
                            $canISeeTheWebsite = true;
                        }

                        // SHOW WEBSITE PASSWORD PROTECTED PREVIEW
                        if (isset($_GET['hidden_preview'])) {
                            if (!in_live_edit() && !user_id()) {
                                echo view('modules.saas_connector::hidden-website-preview', [
                                    'branding' => getBranding(),
                                ]);
                                exit;
                            }
                        }

                        // SHOW UPGRADE PLAN
                        if (!$canISeeTheWebsite) {
                            if (!in_live_edit() && !user_id()) {
                                echo view('modules.saas_connector::upgrade-plan', [
                                    'branding' => getBranding(),
                                ]);
                                exit;
                            }
                        }

                    }
                });

                if (!in_live_edit()) {
                    if (isset($checkWebsite['appendScriptsFrontendLogged']) && !empty($checkWebsite['appendScriptsFrontendLogged'])) {
                        if (user_id()) {
                            meta_tags_footer_add($checkWebsite['appendScriptsFrontendLogged']);
                        }
                    }
                    if (isset($checkWebsite['appendScriptsFrontend']) && !empty($checkWebsite['appendScriptsFrontend'])) {
                        if (!user_id()) {
                            meta_tags_footer_add($checkWebsite['appendScriptsFrontend']);
                        }
                    }
                } else {
                    if (isset($checkWebsite['appendScriptsLiveEdit']) && !empty($checkWebsite['appendScriptsLiveEdit'])) {
                        meta_tags_footer_add($checkWebsite['appendScriptsLiveEdit']);
                    }
                }


            }


        });

        // Ads bar functionality (commented out by default)
        // Uncomment if needed for inactive websites
        /*
        $canIShowAdsBar = false; // Set this based on your logic
        if ($canIShowAdsBar && !in_live_edit()) {
            event_bind('mw.front', function () {
                $css = '
                <style>
                .js-microweber-add-iframe-wrapper {
                    height: 40px;
                    width: 100%;
                    min-height: 40px !important;
                    max-height: 40px !important;
                    position:relative;
                }
                .js-microweber-add-iframe {
                    z-index: 99999;
                    position: fixed;
                    min-height: 0;
                    height: 40px !important;
                    border: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    width: 100%;
                    overflow: hidden;
                }
                </style>
                ';

                if (is_live_edit()) {
                    $url = '/ads-bar?live_edit=1';
                } else {
                    $url = '/ads-bar';
                }

                mw()->template->foot($css . '<div class="js-microweber-add-iframe-wrapper">
                         <iframe class="js-microweber-add-iframe" scrolling="no" frameborder="0" src="' . $url . '"></iframe>
                    </div>');
            });
        }
        */
    }


}
