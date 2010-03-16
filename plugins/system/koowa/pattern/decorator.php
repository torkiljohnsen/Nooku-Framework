<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Pattern
 * @subpackage	Decorator
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Decorator class
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Pattern
 * @subpackage  Decorator
 * @uses 		KObject
 */
abstract class KPatternDecorator extends KObject
{
	/**
	 * The decorated object
	 *
	 * @var object
	 */
	protected $_object;

	/**
	 * Constructor
	 *
	 * @param	object	The object to decorate
	 * @return	void
	 */
	public function __construct($object)
	{
		$this->_object = $object;
	}

	/**
	 * Get the decorated object
	 *
	 * @return	object The decorated object
	 */
	public function getObject() 
	{
		return $this->_object;
	}

	/**
	 * Overloaded set function
	 *
	 * @param  string	The variable name
	 * @param  mixed 	The variable value.
	 * @return mixed
	 */
	public function __set($key, $value) 
	{
		$this->getObject()->$key = $value;
	}

	/**
	 * Overloaded get function
	 *
	 * @param  string 	The variable name.
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->getObject()->$key;
	}

	/**
	 * Overloaded isset function
	 *
	 * Allows testing with empty() and isset() functions
	 *
	 * @param  string 	The variable name
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->getObject()->$key);
	}

	/**
	 * Overloaded isset function
	 *
	 * Allows unset() on object properties to work
	 *
	 * @param string 	The variable name.
	 * @return void
	 */
	public function __unset($key)
	{
		if (isset($this->getObject()->$key)) {
            unset($this->getObject()->$key);
        }
	}
	
	/**
	 * Get a list of all the available methods
	 *
	 * This function returns an array of all the methods, both native and mixed in
	 *
	 * @return array An array 
	 */
	public function getMethods()
  	{
      	$object = $this->getObject();
  		
  		if($object instanceof KObject) { 
      		$methods = $object->getMethods();
      	} else {
      		$methods = get_class_methods($object);
      	}

      	return array_merge(parent::getMethods(), $methods);
 	} 

   	/**
	 * Overloaded call function
	 *
	 * @param  string 	The function name
	 * @param  array  	The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
	 */
	public function __call($method, array $arguments)
	{
		$object = $this->getObject();
		
		//Call the method if it exists
		if(in_array($method, $this->getMethods())) 
		{
 			$result = null;
 				
			// Call_user_func_array is ~3 times slower than direct method calls. 
 		    switch(count($arguments)) 
 		    { 
 		    	case 0 :
 		    		$result = $object->$method();
 		    		break;
 		    	case 1 : 
 	              	$result = $object->$method($arguments[0]); 
 		           	break; 
 	           	case 2: 
 	               	$result = $object->$method($arguments[0], $arguments[1]); 
 		           	break; 
 		      	case 3: 
 	              	$result = $object->$method($arguments[0], $arguments[1], $arguments[2]); 
 	               	break; 
 	           	default: 
 	             	// Resort to using call_user_func_array for many segments 
 		            $result = call_user_func_array(array($object, $method), $arguments);                               
 	         } 
 	         
 	         //Allow for method chaining through the decorator
 	         $class = get_class($object);
             if ($result instanceof $class) {
          		return $this;
             }
             
             return $result; 
		}
		
		return parent::__call($method, $arguments);
	}
}
