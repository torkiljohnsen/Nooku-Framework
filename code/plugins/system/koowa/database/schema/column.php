<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Schema
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Database Schema Column Class
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Schema
 */
class KDatabaseSchemaColumn extends KObject
{
	/**
	 * Column name
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Column type
	 * 
	 * @var	string
	 */
	public $type;
	
	/**
	 * Column size
	 * 
	 * @var integer
	 */
	public $size;
	
	/**
	 * Column scope
	 * 
	 * @var string
	 */
	public $scope;
	
	/**
	 * Column default value
	 * 
	 * @var string
	 */	
	public $default;
			
	/**
	 * Required column
	 * 
	 * @var bool
	 */
	public $required = false;

	/**
	 * Is the column a primary key
	 * 
	 * @var bool
	 */
	public $primary = false;
	
	/**
	 * Is the column autoincremented
	 * 
	 * @var	bool
	 */
	public $autoinc = false;
	
	/**
	 * Is the column unqiue
	 * 
	 * @var	bool
	 */
	public $unique = false;
	
	/**
	 * Filter object
	 * 
	 * Public access is allowed via __get() with $filter.
	 * 
	 * @var	KFilter
	 */
	protected $_filter;
	
	/** 
     * Implements the virtual $filter property.
     * 
     * The value can be a KFilter object, a filter name, an array of filter 
     * names or a filter identifier
     * 
     * @param 	string 	The virtual property to set, only accepts 'filter'
     * @param 	string 	Set the virtual property to this value.
     */
    public function __set($key, $value)
    {
        if ($key == 'filter') {
        	$this->_filter = $value;
        }
    }
    
    /**
     * Implements access to $_filter by reference so that it appears to be 
     * a public $filter property.
     * 
     * @param 	string	The virtual property to return, only accepts 'filter'
     * @return 	mixed 	The value of the virtual property.
     */
    public function __get($key)
    {
        if ($key == 'filter') 
        {
           if(!isset($this->_filter)) {
				$this->_filter = $this->type;
			}
			
			if(!($this->_filter instanceof KFilterInterface)) {
				$this->_filter = KFilter::instantiate(array('filter' => $this->_filter));
			}
		
			return $this->_filter;
        }
    }
}