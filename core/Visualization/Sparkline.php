<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik
 * @package Piwik
 */

namespace Piwik\Visualization;

use Piwik\Common;
use Piwik\View\ViewInterface;
use Sparkline_Line;

/**
 * @see libs/sparkline/lib/Sparkline_Line.php
 * @link http://sparkline.org
 */
require_once PIWIK_INCLUDE_PATH . '/libs/sparkline/lib/Sparkline_Line.php';

/**
 * Renders a sparkline image given a PHP data array.
 * Using the Sparkline PHP Graphing Library sparkline.org
 *
 * @package Piwik
 * @subpackage Piwik_Visualization
 */
class Sparkline implements ViewInterface
{
    const DEFAULT_WIDTH = 100;
    const DEFAULT_HEIGHT = 25;

    private static $colorNames = array('backgroundColor', 'lineColor', 'minPointColor', 'lastPointColor', 'maxPointColor');

    /**
     * Width of the sparkline
     * @var int
     */
    protected $_width = self::DEFAULT_WIDTH;

    /**
     * Height of sparkline
     * @var int
     */
    protected $_height = self::DEFAULT_HEIGHT;

    /**
     * Array with format: array( x, y, z, ... )
     * @param array $data
     */
    public function setValues($data)
    {
        $this->values = $data;
    }

    /**
     * Sets the height of the sparkline
     * @param int $height
     */
    public function setHeight($height)
    {

        if (!is_numeric($height) || $height <= 0) {
            return;
        }

        $this->_height = (int)$height;
    }

    /**
     * Sets the width of the sparkline
     * @param int $width
     */
    public function setWidth($width)
    {

        if (!is_numeric($width) || $width <= 0) {
            return;
        }

        $this->_width = (int)$width;
    }

    /**
     * Returns the width of the sparkline
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Returns the height of the sparkline
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    public function main()
    {
        $width = $this->getWidth();
        $height = $this->getHeight();

        $sparkline = new Sparkline_Line();
        $this->setSparklineColors($sparkline);

        $min = $max = $last = null;
        $i = 0;
        $toRemove = array('%', str_replace('%s', '', Piwik_Translate('General_Seconds')));
        foreach ($this->values as $value) {
            // 50% and 50s should be plotted as 50
            $value = str_replace($toRemove, '', $value);
            // replace localized decimal separator
            $value = str_replace(',', '.', $value);
            if ($value == '') {
                $value = 0;
            }

            $sparkline->SetData($i, $value);

            if (null == $min || $value <= $min[1]) {
                $min = array($i, $value);
            }
            if (null == $max || $value >= $max[1]) {
                $max = array($i, $value);
            }
            $last = array($i, $value);
            $i++;
        }
        $sparkline->SetYMin(0);
        $sparkline->SetYMax($max[1]);
        $sparkline->SetPadding(3, 0, 2, 0); // top, right, bottom, left
        $sparkline->SetFeaturePoint($min[0], $min[1], 'minPointColor', 5);
        $sparkline->SetFeaturePoint($max[0], $max[1], 'maxPointColor', 5);
        $sparkline->SetFeaturePoint($last[0], $last[1], 'lastPointColor', 5);
        $sparkline->SetLineSize(3); // for renderresampled, linesize is on virtual image
        $ratio = 1;
        $sparkline->RenderResampled($width * $ratio, $height * $ratio);
        $this->sparkline = $sparkline;
    }

    public function render()
    {
        $this->sparkline->Output();
    }

    private function setSparklineColors($sparkline)
    {
        $colors = Common::getRequestVar('colors', false, 'json');
        if (!empty($colors)) {
            foreach (self::$colorNames as $name) {
                if (!empty($colors[$name])) {
                    $sparkline->SetColorHtml($name, $colors[$name]);
                }
            }
        }
    }
}
