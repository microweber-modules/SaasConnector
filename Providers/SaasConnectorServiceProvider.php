<?php

namespace Modules\SaasConnector\Providers;

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


        // Admin sidebar button for "My Websites"
//        event_bind('mw.admin.sidebar.li.first', function () {
//            $saasUrl = getWebsiteManagerUrl();
//
//            if ($saasUrl) {
//                echo '<a href="' . $saasUrl . '/projects"
//                        style="border-radius: 40px;" class="btn btn-outline-primary">
//                   <i class="mdi mdi-arrow-left"></i> &nbsp; My Websites
//                </a>';
//            }
//        });

        // Frontend scripts handling
        event_bind('mw.front', function () {

            if (is_ajax()) {
                return;
            }


            // Get website info from SaaS server
            $checkWebsite = getSaasWebsiteInfoFromServer();


            // Append admin panel scripts
            if (isset($checkWebsite['appendScriptsAdminPanel']) && !empty($checkWebsite['appendScriptsAdminPanel'])) {
                event_bind('admin_head', function () use ($checkWebsite) {
                    echo $checkWebsite['appendScriptsAdminPanel'];
                });
            }

            // Handle website subscription status and preview mode
            if (isset($checkWebsite['success'])) {
                $hasActiveSubscription = false;
                if (isset($checkWebsite['activeSubscription']) && !empty($checkWebsite['activeSubscription'])) {
                    $hasActiveSubscription = true;
                }

                define('HAS_ACTIVE_SUBSCRIPTION', $hasActiveSubscription);

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
