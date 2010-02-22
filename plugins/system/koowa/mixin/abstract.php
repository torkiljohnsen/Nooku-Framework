<?php
/**
 * @version     $Id$
 * @category	Koowa
 * @package     Koowa_Mixin
 * @copyright   Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Abstract mixing class
 * 
 * This class does not extend from KObject and acts as a special core 
 * class that is intended to offer semi-multiple inheritance features
 * to KObject derived classes.
 *  
 * @author      Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Mixin
 * @uses 		KObject
 */
abstract class KMixinAbstract implements KMixinInterface
{   
	/**
     * The object doing the mixin
     *
     * @var object
     */
    protected $_mixer;
    
    /**
     * List of mixable methods
     *
     * @var array
     */
    private $__methods;
        
    /**
	 * Object constructor
	 *
	 * @param	array 	An optional associative array of configuration settings.
	 * 					Recognized key values include 'mixer' (this list is not 
	 * 					meant to be comprehensive).
	 */
	public function __construct(array $options = array())
	{
		if(isset($options['mixer'])) {
			$this->_mixer = $options['mixer'];
		} else {
			$this->_mixer = $this;
		}
	}
	
	/**
     * Initializes the options for the object
     * 
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   array   Options
     * @return  array   Options
     */
    protected function _initialize(array $options)
    {
    	$defaults = array(
            'mixer' =>  null,
        );

        return array_merge($defaults, $options);
    }
    
	/**
	 * Get a handle for this object
	 *
	 * This function returns an unique identifier for the object. This id can be used as
	 * a hash key for storing objects or for identifying an object
	 *
	 * @return string A string that is unique
	 */
	public function getHandle()
	{
		return spl_object_hash( $this );
	}
	
	/**
	 * Get the methods that are available for mixin. 
	 * 
	 * @param object The mixer requesting the mixable methods. 
	 * @return array An array of methods
	 */
	public function getMixableMethods(KObject $mixer = null)
	{
		if(!$this->__methods)
		{
			$methods = array();
	
			//get_class_methods also returns none-public method for inside object scope
			foreach (get_class_methods($this) as $method)
			{
				$reflect = new ReflectionMethod(get_class($this), $method);
	
				if($reflect->isPublic()) {
					$methods[] = $method;
				}
			}
		
     	   	$this->_methods = array_diff($methods, get_class_methods(__CLASS__));  
		}
     	
		return $this->_methods;
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
		if($key == 'mixer') {
			$this->_mixer = $value;
		} else {
			$this->_mixer->$key = $value;
		}
	}

	/**
	 * Overloaded get function
	 *
	 * @param  string 	The variable name.
	 * @return mixed
	 */
	public function __get($key)
	{
		if($key == 'mixer') {
			return $this->_mixer;
		} else {
			return $this->_mixer->$key;
		}
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
		return isset($this->_mixer->$key);
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
		if (isset($this->_mixer->$key)) {
            unset($this->_mixer->$key);
        }
	}
	
  	/**
     * Search the mixin method map and call the method or trigger an error
     *
   	 * @param  string 	The function name
	 * @param  array  	The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
     */
    public function __call($method, array $arguments)
    {
        if(isset($this->_mixer)) 
        {
			// Call_user_func_array is ~3 times slower than direct method calls. 
 		    switch(count($arguments)) 
 		    { 
 		    	case 0 :
 		    		$result = $this->_mixer->$method();
 		    		break;
 		    	case 1 : 
 	              	$result = $this->_mixer->$method($arguments[0]); 
 		           	break; 
 	           	case 2: 
 	               	$result = $this->_mixer->$method($arguments[0], $arguments[1]); 
 		           	break; 
 		      	case 3: 
 	              	$result = $this->_mixer->$method($arguments[0], $arguments[1], $arguments[2]); 
 	               	break; 
 	           	default: 
 	             	// Resort to using call_user_func_array for many segments 
 		            $result = call_user_func_array(array($this->_mixer, $method), $arguments);                               
 	         } 
 	         
        	return $result;
        }
        
      	throw new BadMethodCallException('Call to undefined method :'.$method); 	
    }
}