<?php
/**
* @version      $Id$
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Export to CSV button for a toolbar
 * 
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 */
class KToolbarButtonCsv extends KToolbarButtonAbstract
{
	
	public function __construct(array $options = array())
	{
		if(!isset($options['icon'])) {
			$options['icon'] = 'icon-32-export';
		}
		parent::__construct($options);
		
		KFactory::get('lib.koowa.document')->addStyleDeclaration('.icon-32-export { background-image: url('.KRequest::root().'/media/plg_koowa/images/32/export.png); }');		
	}
	
	public function getLink()
	{
		// Unset limit and offset
		$url = clone KRequest::url();
		$query = parse_str($url->getQuery(), $vars);
		unset($vars['limit']);
		unset($vars['offset']);
		$vars['format'] = 'csv';
		$url->setQuery(http_build_query($vars));
		
		return (string) $url;
	}
}