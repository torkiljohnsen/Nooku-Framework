<?php
/**
 * @version     $Id: default.php 2721 2010-10-27 00:58:51Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Style Filter
.*
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateFilterStyle extends KTemplateFilterStyle
{
   	/**
	 * Render style information
	 * 
	 * @param string	The style information
	 * @param boolean	True, if the style information is a URL
	 * @param array		Associative array of attributes
	 * @return string
	 */
	protected function _renderStyle($style, $link, $attribs = array())
	{
		if(KRequest::type() == 'AJAX') {
			return parent::_renderStyle($style, $link, $attribs);
		}
		
		$document = KFactory::get('lib.joomla.document');
			
		if($link) 
		{
			$type = 'text/css';
			if(isset($attribs['type']))
			{ 
				$type = $attribs['type'];
				unset($attribs['type']);
			}
			
			$media = null;
			if(isset($attribs['media']))
			{ 
				$media = $attribs['media'];
				unset($attribs['media']);
			}
			
			$document->addStyleSheet($style, $type, $media, $attribs);
		} 
		else $document->addStyleDeclaration($style);
	}
}