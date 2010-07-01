<?php
/**
 * @version 	$Id$
 * @category	Koowa
 * @package		Koowa_Database
 * @subpackage 	Behavior
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Behavior Factory
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage 	Behavior
 */
class KDatabaseBehavior
{
	/**
	 * Factory method for KDatabaseBehaviorInterface classes.
	 *
	 * @param	string 	Behavior indentifier
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return KDatabaseBehaviorAbstract
	 */
	public static function factory($identifier, $config = array())
	{		
		//Create the behavior
		try 
		{
			if(is_string($identifier) && strpos($identifier, '.') === false ) {
				$identifier = 'lib.koowa.database.behavior.'.trim($identifier);
			} 
			
			$behavior = KFactory::tmp($identifier, $config);
			
		} catch(KFactoryAdapterException $e) {
			throw new KDatabaseBehaviorException('Invalid identifier: '.$identifier);
		}
		
		//Check the behavior interface
		if(!($behavior instanceof KDatabaseBehaviorInterface)) 
		{
			$identifier = $behavior->getIdentifier();
			throw new KDatabaseBehaviorException("Database behavior $identifier does not implement KDatabaseBehaviorInterface");
		}
		
		return $behavior;
	}
}