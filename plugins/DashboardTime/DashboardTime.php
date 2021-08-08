<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\DashboardTime;

class DashboardTime extends \Piwik\Plugin
{
    public function registerEvents()
    {
        return [
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'AssetManager.getJavaScriptFiles' => 'getJsFiles',
            'CronArchive.getArchivingAPIMethodForPlugin' => 'getArchivingAPIMethodForPlugin',
        ];
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = "plugins/DashboardTime/angularjs/current-time-widget/current-time-widget.directive.js";
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/DashboardTime/angularjs/current-time-widget/current-time-widget.directive.css";
    }

    // support archiving just this plugin via core:archive
    public function getArchivingAPIMethodForPlugin(&$method, $plugin)
    {
        if ($plugin == 'DashboardTime') {
            $method = 'DashboardTime.getExampleArchivedMetric';
        }
    }
}
