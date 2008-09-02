<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Abstract array filter
 * 
 * Extend this class and add the name of the filter as the suffix. 
 * Eg MycompFilterArrayMyformat extends KFilterArrayAbstract
 * This will validate or sanitize each item in the passed array through 
 * MycompFilterMyformat
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Filter
 * @version     1.0
 * @uses		KHelperClass
 */
abstract class KFilterArrayAbstract extends KObject implements KFilterInterface
{	
	/**
	 * Filter object
	 *
	 * @var KFilterInterface
	 */
	protected $_filter;

	public function __construct(array $options = array())
	{
		// Initialize the options
        $options  = $this->_initialize($options);
        
       // Mixin the KClass
		$this->mixin(new KHelperClass($this, 'FilterArray'));

        // Assign the classname with values from the config
        $this->setClassName($options['name']);
		$classname = $this->getClassName();
		
		if(empty($classname['suffix'])) {
			throw new KFilterArrayExeption('Incorrect classname: '.get_class($this));
		}
		$filtername = ucfirst($classname['prefix']).'Filter'.ucfirst(KInflector::singularize($classname['suffix']));
	
		$this->_filter = new $filtername($options);	
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
            'name'          => array(
                        'prefix'    => 'k',
                        'base'      => 'filterArray',
                        'suffix'    => 'default'
                        )
        );

        return array_merge($defaults, $options);
    }
	
	/**
	 * Validate an array
	 *
	 * @param	array	Array to be validated
	 * @return	bool	True when the array is valid
	 */
	public function validate($arr)
	{
		if(!is_array($arr)) {
			return false;
		}
		
		foreach($arr as $var) 
		{
			if(false === $this->_filter->validate($var)) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Sanitize an array
	 *
	 * @param	mixed	Array to be sanitized
	 * @return	array
	 */
	public function sanitize($arr)
	{
		settype($arr, 'array');
		
		foreach($arr as & $var) {
			$var = $this->_filter->sanitize($var);
		}
		
		return $arr;
	}
}

