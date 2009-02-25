<?php
/**
 * @version     $Id$
 * @package     Koowa_Chart
 * @subpackage  Sparkline
 * @copyright   Copyright (C) 2007 - 2009 Joomlatools. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

Koowa::import('koowa.chart.renderer.sparkline.Sparkline');

/**
 * Sparkline
 *
 * @author      Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Chart
 * @subpackage  Sparkline
 * @version     1.0
 */
abstract class KChartSparkline extends KPatternProxy
{
    /**
     * Resample
     *
     * @var boolean
     */
    protected $_resample = true;

    /**
     * Get an instance of a Sparkline object by type
     *
     * @param string Type [bar|line]
     */
    static public function getInstance($type, $config = array())
    {
        $type = ucfirst(strtolower($type));

        $classname = "KChartSparkline$type";
        if(class_exists($classname))
        {
            return call_user_func(array($classname, 'getInstance'), array($config));
        }
        JError::raiseWarning( 0, "Sparkline type '$type' doesn't exist." );
        return false;
    }

    abstract public function render($width, $height);

    /**
     * @deprecated
     */
    final public function renderResampled()
    {
        JError::raiseWarning(0, 'Use KChartSparkline::setResample() to change rendering behaviour');
        return false;
    }

    /**
     * @deprecated
     */
    final public function output()
    {
        JError::raiseWarning(0, 'Use KChartSparkline::render() to render sparklines');
        return false;
    }

    /**
     * @deprecated
     */
    final public function outputToFile()
    {
    	JError::raiseWarning(0, 'Use KChartSparkline::render() to render sparklines');
        return false;
    }

    /**
     * Set resampling on or off
     *
     * Only 'Line' sparklines support resampling, 'Bar' sparklines do not
     *
     * @param bool
     */
    public function setResample($resample)
    {
    	$this->_resample = $resample;
    }

    /**
     * Get resampling
     *
     * @return bool
     */
    public function getResample($resample)
    {
        return (bool) $this->_resample;
    }

    /**
     * Set color
     *
     * Accepts an RGB array or a hex string
     *
     * @param	string	Name
     * @param	array|string (R,G,B) or hex
     */
    public function setColor($name, $c)
     {
     	if(is_string($c))
        {
        	$str   = str_replace('#', '', $c);
            $c = array( hexdec(substr($str, 0, 2)),
                            hexdec(substr($str, 2, 2)),
                            hexdec(substr($str, 4, 2)) );
        }
        return $this->getObject()->setColor($name, $c[0], $c[1], $c[2]);
     }
}