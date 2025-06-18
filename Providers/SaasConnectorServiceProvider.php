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


}
