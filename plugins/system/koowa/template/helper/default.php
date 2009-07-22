<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPL <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Template Helper Class
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @uses   		KFactory
 */
class KTemplateHelperDefault extends KObject
{
	/**
	 * Write a <script></script> element
	 *
	 * @param	string 	The name of the script file
	 */
	public function script($url)
	{
		$document = KFactory::get('lib.koowa.document');
		$document->addScript($url);
		return;
	}

	/**
	 * Write a <link rel="stylesheet" style="text/css" /> element
	 *
	 * @param	string 	The relative URL to use for the href attribute
	 * @param	array	Attributes
	 */
	public function stylesheet($url, array $attribs = array())
	{
		$document = KFactory::get('lib.koowa.document');
		$document->addStylesheet($url, 'text/css', null, $attribs);
		return;
	}

	/**
	 * Returns formated date according to current local and adds time offset
	 *
	 * @access	public
	 * @param	string	date in an US English date format
	 * @param	string	format optional format for strftime
	 * @returns	string	formated date
	 * @see		strftime
	 */
	public function date($date, $format = null, $offset = NULL)
	{
		if ( ! $format ) {
			$format = JText::_('DATE_FORMAT_LC1');
		}

		if(is_null($offset))
		{
			$config = KFactory::get('lib.joomla.config');
			$offset = $config->getValue('config.offset');
		}

		$instance = KFactory::get('lib.joomla.date', array($date));
		$instance->setOffset($offset);

		return $instance->toFormat($format);
	}
}
