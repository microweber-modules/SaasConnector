<?php

namespace Modules\SaasConnector\Http\Controllers\Admin;

use Illuminate\Http\Request;
use MicroweberPackages\Install\TemplateInstaller;
use MicroweberPackages\Admin\Http\Controllers\AdminController;

class SetupWizardController extends AdminController
{
    public function index(Request $request)
    {
        $filterCategory = $request->get('category', false);
        $getCategories = [];
        $siteTemplates = [];
        $getTemplates = site_templates();



        foreach ($getTemplates as $template) {
            if (!isset($template['screenshot'])) {
                continue;
            }

            $templateCategories = [];
            $templateColors = [];
            $templateJson = templates_path() . $template['dir_name'] . '/composer.json';

            if (is_file($templateJson)) {
                $templateJson = @file_get_contents($templateJson);
                $templateJson = @json_decode($templateJson, true);
                if (!empty($templateJson)) {
                    if (isset($templateJson['extra']['colors'])) {
                        $templateColors = $templateJson['extra']['colors'];
                    }
                    if (isset($templateJson['extra']['categories'])) {
                        $templateCategories = $templateJson['extra']['categories'];
                    }
                }
            }

            foreach ($templateCategories as $templateCategory) {
                $getCategories[$templateCategory] = $templateCategory;
            }

            if ($filterCategory) {
                if (!in_array($filterCategory, $templateCategories)) {
                    continue;
                }
            }

            $template['categories'] = $templateCategories;
            $template['colors'] = $templateColors;
            $siteTemplates[] = $template;
        }

        return view('modules.saas_connector::admin.setup_wizard', [
            'templates' => $siteTemplates,
            'categories' => $getCategories
        ]);
    }

    public function installTemplate(Request $request)
    {
        $template = $request->get('template', false);
        if (!$template) {
            return ['error' => 'Please, select template for installation.'];
        }

        $installer = new TemplateInstaller();
        $installer->setSelectedTemplate($template);
        $installer->setInstallDefaultContent(true);

        $installer->run();

        return ['success' => 'Template is installed successfully.'];
    }
}
