<?php
/**
 * @version		$Id:proxy.php 46 2008-03-01 18:39:32Z mjaz $
 * @category	Koowa
 * @package		Koowa_Pattern
 * @subpackage	Observable
 * @copyright	Copyright (C) 2007 - 2009 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract observable class to implement the observer design pattern
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Pattern
 * @subpackage  Observable
 * @uses		KObject
 */
abstract class KPatternObservable extends KObject
{
	/**
	 * An array of Observer objects to notify
	 *
	 * @var array
	 */
	protected $_observers;

	/**
	 * Constructor
	 * 
	 * @return	void
	 */
	public function __construct() 
	{
		$this->_observers = new ArrayObject();
	}

	/**
	 * Update each attached observer object and return an array of their return values
	 *
	 * @param object	$args	An associative array of arguments
	 * @return array Array of return values from the observers
	 */
	public function notify(ArrayObject $args)
	{
		$result = array();
		$iterator = $this->_observers->getIterator();

		// Iterate through the _observers array
		while($iterator->valid()) 
		{
    		$observer = $iterator->current();
			$result[] = $observer->onNotify($args);
    		$iterator->next();
		}	
	
		return $result;
	}

	/**
	 * Attach an observer object
	 *
	 * @param 	object 	$observer An observer object to attach
	 * @return void
	 */
	public function attach( KPatternObserver $observer )
	{
		$handle = $observer->getHandle(); //get the object handle
		
		$this->_observers->offsetSet($handle, $observer);
	}

	/**
	 * Detach an observer object
	 *
	 * @param 	object 	$observer An observer object to detach
	 * @return 	boolean True if the observer object was detached
	 */
	public function detach( KPatternObserver $observer)
	{
		$handle = $observer->getHandle(); //get the object handle

		$result = false;
  		if($this->_observers->offsetExist($handle)) {
			$this->_observers->offsetUnset($handle);	
  			$result = true;
		}
		
		return $result;
	}
}