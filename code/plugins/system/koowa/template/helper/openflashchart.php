<?php
/**
 * @version     $Id$
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage 	Helper
 * @copyright   Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license     GNU GPL <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Template Open Flash Chart HTML Helper
 *
 * @author      Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage 	Helper
 */
class KTemplateHelperOpenflashchart extends KObject
{
    /**
     * Renders the <object> tag for Open Flash Chart
     *
     * @param string	Data Url
     * @param string	Unique ID
     * @param string	SWF file url
     * @param string	Width (px, %)
     * @param string	Height (px, %)
     * @param string	Background color
     * @param string	Attributes for the surrounding <div>
     */
    public function swfobject( $dataUrl, $id, $swfUrl = null, $width = '100%', $height = '450px', $bgcolor = '#FFFFFF', $divAttr = '')
    {
    	$swfUrl = $swfUrl ? $swfUrl : Koowa::getURL('media').'swf/open-flash-chart.swf';
		return KChartOpenflashchart::renderSwfobject( $dataUrl, $id, $swfUrl, $width, $height, $bgcolor, $divAttr);
    }

}