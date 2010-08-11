<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Config
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Config Class
 * 
 * KConfig provides a property based interface to an array
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package		Koowa_Config
 */
class KConfig implements IteratorAggregate, ArrayAccess, Countable
{
	/**
     * The data container
     *
     * @var array
     */
    protected $_data;
	
	/**
	 * Constructor.
	 *
	 * @param	array|KConfig An associative array of configuration settings or a KConfig instance.
	 */
	public function __construct( $config = array() )
	{ 
		if ($config instanceof KConfig) {
			$config = clone $config;
		}
		
		$this->_data = array();
		foreach ($config as $key => $value) 
		{
			if (is_array($value) && !is_numeric(key($config)) ) {
                $this->_data[$key] = new self($value);
            } else {
                $this->_data[$key] = $value;
            }
        }
	} 
	
	/**
     * Deep clone of this instance to ensure that nested KConfigs
     * are also cloned.
     *
     * @return void
     */
    public function __clone()
    {
      	$array = array();
      	foreach ($this->_data as $key => $value) 
      	{
       		if ($value instanceof KConfig) {
           		$array[$key] = clone $value;
          	} else {
           		$array[$key] = $value;
          	}
      	}
      	
      	$this->_data = $array; 	
    }
    
   	/**
     * Retrieve a configuration item and return $default if there is no element set.
     *
     * @param string 
     * @param mixed 
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if(isset($this->_data[$name])) {
            $result = $this->_data[$name];
        }
        
        return $result;
    }

    /**
     * Retrieve a configuration element
     *
     * @param string 
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
    
    /**
     * Set a configuration element
     *
     * @param  string 
     * @param  mixed 
     * @return void
     */
    public function __set($name, $value)
    {
    	if (is_array($value) && !empty($value) && !is_numeric(key($value))) {
         	$this->_data[$name] = new self($value);
      	} else {
         	$this->_data[$name] = $value;
       	}
    }
    
    /**
     * Test existence of a configuration element
     *
     * @param string 
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Unset a configuration element
     *
     * @param  string 
     * @return void
     */
    public function __unset($name)
    {
      	unset($this->_data[$name]);
    }
    
	/**
	 * Get a new iterator
	 * 
	 * @return	ArrayIterator
	 */
	public function getIterator() 
	{
        return new ArrayIterator($this->_data);
    }

	/**
     * Returns the number of elements in the collection.
     *
     * Required by the Countable interface
     *
     * @return int
     */
    public function count()
    {
    	return count($this->_data);
    }
    
	/**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param 	int 	The offset
     * @return  bool
     */
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}

    /**
     * Get an item from the array by offset
     *
     * Required by interface ArrayAccess
     *
     * @param 	int 	The offset
     * @return  mixed	The item from the array
     */
	public function offsetGet($offset)
	{
		$result = null;
		if(isset($this->_data[$offset])) 
		{ 
			$result = $this->_data[$offset];
			if($result instanceof KConfig) {
				$result = $result->toArray();
			}
		} 
			
		return $result;	
	}

    /**
     * Set an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param 	int 	The offset of the item
     * @param 	mixed	The item's value
     * @return 	object KObjectArray
     */
	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
		return $this;
	}

    /**
     * Unset an item in the array
     *
     * All numerical array keys will be modified to start counting from zero while
     * literal keys won't be touched.
     *
     * Required by interface ArrayAccess
     *
     * @param 	int 	The offset of the item
     * @return 	object KObjectArray
     */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
        return $this;
	}

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array(); 
        $data  = $this->_data;
        foreach ($data as $key => $value) 
        {
            if ($value instanceof KConfig) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        
        return $array;
    }
    
    /**
     * Return the data 
     *
     * If the data being passed is an instance of KConfig the data will be transformed
     * to an associative array.
     *
     * @return array|scalar
     */
    public static function toData($data)
    {
    	return ($data instanceof KConfig) ? $data->toArray() : $data;
    }
    
	/**
     * Merge an array. Only adding keys that don't exist.
     *
     * @param  array 	An associative array of configuration elements to be appended
     * @return KConfig
     */
    public function append(array $config)
    {
    	foreach($config as $key => $value) 
        {
        	if(array_key_exists($key, $this->_data)) 
            {
                if(is_array($value))
                {
               	 	if($this->_data[$key] instanceof KConfig) {
                    	$this->_data[$key] = $this->_data[$key]->append($value);
                	} else if (is_array($this->_data[$key])) {
                    	$this->_data[$key] = array_merge($value, $this->_data[$key]);
                    }
                }
            } 
            else $this->$key = $value;
        }
        
        return $this;
    } 
}