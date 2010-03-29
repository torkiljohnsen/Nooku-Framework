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
 * POST button class for a toolbar
 * 
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 */
abstract class KToolbarButtonPost extends KToolbarButtonAbstract
{
	protected $_fields = array();
	
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		$this->setMethod('post');
		
		KTemplate::loadHelper('script', KRequest::root().'/media/plg_koowa/js/koowa.js');
	}
	
	public function getOnClick()
	{
		$js = '';
		foreach($this->_fields as $name => $value) {
			$js .= "KForm.addField('$name', '$value');";
		}
		$js .= "KForm.submit('{$this->_method}');";
		return $js;
	}
	
	public function setField($name, $value)
	{
		$this->_fields[$name] = $value;
		return $this;
	}
}