<?php
/**
 * @version 	$Id: abstract.php 1528 2010-01-26 23:14:08Z johan $
 * @category	Koowa
 * @package		Koowa_Database
 * @subpackage 	Behavior
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Database Creatable Behavior
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage 	Behavior
 */
class KDatabaseBehaviorCreatable extends KDatabaseBehaviorAbstract
{
	/**
	 * Get the methods that are available for mixin based
	 * 
	 * This function conditionaly mixes the behavior. Only if the mixer 
	 * has a 'created_b'y property the behavior will be mixed in.
	 * 
	 * @param object The mixer requesting the mixable methods. 
	 * @return array An array of methods
	 */
	public function getMixableMethods(KObject $mixer = null)
	{
		$methods = array();
		
		if(isset($mixer->created_by)) {
			$methods = parent::getMixableMethods($mixer);
		}
	
		return $methods;
	}
	
	/**
	 * Set created information
	 * 	
	 * Requires an 'created_on' and 'created_by' column
	 * 
	 * @return void
	 */
	protected function _beforeTableInsert(KCommandContext $context)
	{
		$row = $context->data; //get the row data being inserted
		
		if(isset($row->created_by)) {
			$row->created_by  = (int) KFactory::get('lib.koowa.user')->get('id');
		}
		
		if(isset($row->created_on)) {
			$row->created_on  = gmdate('Y-m-d H:i:s');
		}
	}
}