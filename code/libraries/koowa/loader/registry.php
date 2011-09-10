<?php
/**
 * @version 	$Id$
 * @category	Koowa
 * @package		Koowa_Loader
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * KLoader Registry Class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Loader
 */
class KLoaderRegistry extends ArrayObject
{
 	/**
 	 * Cache
 	 * 
 	 * @var boolean
 	 */
    protected $_cache = false;
    
    /**
 	 * Cache Prefix
 	 * 
 	 * @var boolean
 	 */
    protected $_cache_prefix = 'koowa.loader.registry';
    
    /**
     * Enable class caching
     * 
     * @return boolean	TRUE if caching is enabled. FALSE otherwise.
     */
	public function enableCache() 
	{
	    if(extension_loaded('apc')) {
            $this->_cache = true;    
        }
        
        return $this->_cache;
	}
	
	/**
     * Disable class caching
     * 
     * @return void
     */
	public function disableCache()
	{
	    $this->_cache = false;
	}
	
	/**
     * Set the cache prefix
     * 
     * @param string The cache prefix
     * @return void
     */
	public function setCachePrefix($prefix)
	{
	    $this->_cache_prefix = $prefix;
	}
	
	/**
     * Get the cache prefix
     * 
     * @return string	The cache prefix
     */
	public function getCachePrefix()
	{
	    return $this->_cache_prefix;
	}
    
 	/**
     * Get an item from the array by offset
     *
     *
     * @param   int     The offset
     * @return  mixed   The item from the array
     */
    public function offsetGet($offset)
    {   
        if(!parent::offsetExists($offset))
        {
            if($this->_cache) {
                $path = apc_fetch($this->_cache_prefix.$offset);
            } else {
                $path = false;
            }
        }
        else $path = parent::offsetGet($offset);
        
        return $path; 
    }

    /**
     * Set an item in the array
     *
     * @param   int     The offset of the item
     * @param   mixed   The item's value
     * @return  object  KObjectArray
     */
    public function offsetSet($offset, $value)
    {
        if($this->_cache) {
            apc_store($this->_cache_prefix.$offset, $value);
        }
        
        parent::offsetSet($offset, $value);
    }
    
	/**
     * Check if the offset exists
     *
     * @param   int     The offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        if(false === $result = parent::offsetExists($offset)) 
        {
            if($this->_cache) {
                $result = apc_exists($this->_cache_prefix.$offset);
            }  
        }
        
        return $result;
    }
}