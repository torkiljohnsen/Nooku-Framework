<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Command
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Command handler
 * 
 * The command handler will translate the command name to a _beforeCommandName or
 * _afterCommandName format and call it for the object class to handle it if the 
 * method exists.
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Command
 * @uses 		KFactory
 * @uses 		KInflector
 */
class KCommandHandler extends KObject implements KCommandInterface 
{
	/**
	 * Command handler
	 * 
	 * @param 	string  	The command name
	 * @param 	object   	The command context
	 * @return 	boolean		Can return both true or false.  
	 */
	final public function execute( $name, KCommandContext $context) 
	{
		$type = '';
		
		if($context->caller)
		{
			$identifier = clone $context->caller->getIdentifier();
			
			if($identifier->path) {
				$type = array_shift($identifier->path);
			} else {
				$type = $identifier->name;
			}
		}
		
		$parts  = explode('.', $name);	
		$method = '_'.$type.lcfirst(KInflector::implode($parts));
	
		if(in_array($method, $this->getMethods())) {
			return $this->$method($context);
		}
		
		return true;
	}
}