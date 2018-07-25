<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Diagnostics\Diagnostic;

use Piwik\Config;
use Piwik\ProxyHttp;
use Piwik\Translation\Translator;

/**
 * Check that Matomo is configured to force SSL.
 */
class ForceSSLCheck implements Diagnostic
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function execute()
    {
        $label = $this->translator->translate('General_ForcedSSL');

        $forceSSLEnabled = (Config::getInstance()->General['force_ssl'] == 1);

        if ($forceSSLEnabled) {
            return array(DiagnosticResult::singleResult($label, DiagnosticResult::STATUS_OK));
        }

        $comment = $this->translator->translate('General_ForceSSLRecommended', ['<code>force_ssl = 1</code>', '<code>General</code>']);

        if (!ProxyHttp::isHttps()) {
            $comment .= '<br /><br />' . $this->translator->translate('General_NotPossibleWithoutHttps');
        }

        return array(DiagnosticResult::singleResult($label, DiagnosticResult::STATUS_WARNING, $comment));
    }
}