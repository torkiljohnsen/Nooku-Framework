<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Object
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Object class
 *
 * Provides getters and setters, mixin, object handles
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Object
 */
class KObject
{
    /**
     * Mixed in objects
     *
     * @var array
     */
    protected $_mixinObjects = array();

    /**
     * Mixed in methods
     *
     * @var array
     */
    protected $_mixinMethods = array();
    
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct( array $options = array() ) { }
	    
 	/**
     * Set the object properties
     *
     * @param   string|array|object	The name of the property, an associative array or an object
     * @param   mixed  				The value of the property
     * @throws	KObjectException
     * @return  KObject
     */
    public function set( $property, $value = null )
    {
    	if(is_object($property)) {
    		$property = (array) $property;
    	}
    	
    	if(is_array($property)) 
        {
        	foreach ($property as $k => $v) {
            	$this->set($k, $v);
        	}
        }
        else 
        {
       		if('_' == substr($property, 0, 1)) {
        		throw new KObjectException("Protected or private properties can't be set outside of object scope in ".get_class($this));
        	}
        	
        	$this->$property = $value;
        }
    	
        return $this;
    }

    /**
     * Get the object properties
     * 
     * If no property name is given then the function will return an associative
     * array of all properties.
     * 
     * If the property does not exist and a  default value is specified this is
     * returned, otherwise the function return NULL.
     *
     * @param   string	The name of the property
     * @param   mixed  	The default value
     * @return  mixed 	The value of the property, an associative array or NULL
     */
    public function get($property = null, $default = null)
    {
        $result = $default;
    	
    	if(is_null($property)) 
        {
        	$result  = get_object_vars($this);

        	foreach ($result as $key => $value)
        	{
            	if ('_' == substr($key, 0, 1)) {
                	unset($result[$key]);
            	}
        	}
        } 
        else
        {
    		if(isset($this->$property)) {
            	$result = $this->$property;
        	}
        }
        
        return $result;
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
     * Mixin an object
     *
     * When using mixin(), the calling object inherits the methods of the mixed
     * in objects, in a LIFO order
     *
     * @param	object
     * @return	KObject
     */
    public function mixin(KMixinInterface $object)
    {
        array_unshift($this->_mixinObjects, $object);

       	$methods = $object->getMixinMethods();

        foreach($methods as $method) {
            $this->_mixinMethods[$method] = $object;
        }

        return $this;
    }

    /**
     * Search the method map, and call the method or trigger an error
     *
   	 * @param  string $function		The function name
	 * @param  array  $arguments	The function arguments
	 * @return mixed The result of the function
     */
    public function __call($method, $args)
    {
        if(isset($this->_mixinMethods[$method])) {
            return call_user_func_array(array($this->_mixinMethods[$method], $method), $args);
        }

        $trace = debug_backtrace();
        trigger_error("Call to undefined method {$trace[1]['class']}::$method()", E_USER_ERROR);
    }
}