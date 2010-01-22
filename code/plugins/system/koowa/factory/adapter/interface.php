<?php
/**
 * @version 	$Id$
 * @category	Koowa
 * @package		Koowa_Factory
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Factory Adapter Interface
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Factory
 * @subpackage 	Adapter
 */
interface KFactoryAdapterInterface extends KCommandInterface
{
	/**
	 * Create an object instance based on a class identifier
	 *
	 * @param 	mixed 	The class identifier
	 * @param 	array 	An optional associative array of configuration settings.
	 * @return 	object|false 	Return object on success, returns FALSE on failure
	 */
	public function instantiate($identifier, array $options);
}