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
 * Factory Adapter for a component
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Factory
 * @subpackage 	Adapter
 */
class KFactoryAdapterComponent extends KFactoryAdapterAbstract
{
	/**
	 * The alias map
	 *
	 * @var	array
	 */
	protected $_alias_map = array(
      	'table'     => 'DatabaseTable',
        'row'       => 'DatabaseRow',
      	'rowset'    => 'DatabaseRowset'
	);


	/**
	 * Create an instance of a class based on a class identifier
	 * 
	 * This factory will try to create an generic or default object based on the identifier information
	 * if the actual object cannot be found using a predefined fallback sequence.
	 * 
	 * Sequence : Component Generic -> Component Default -> Framework Generic -> Framework Default
	 *
	 * @param mixed  		 Identifier or Identifier object - application::com.component.[.path].name
	 * @param array  		 An optional associative array of configuration settings.
	 * @return object|false  Return object on success, returns FALSE on failure
	 */
	public function instantiate($identifier, array $options)
	{
		$instance = false;
		
		if($identifier->type == 'com') 
		{			
			$path      = KInflector::camelize(implode('_', $identifier->path));
        	$classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
        	        	
      		//Don't allow the auto-loader to load component classes if they don't exists yet
			if (!class_exists( $classname, false ))
			{
				//Find the file
				if($path = KLoader::load($identifier))
				{
					//Don't allow the auto-loader to load component classes if they don't exists yet
					if (!class_exists( $classname, false )) {
						throw new KFactoryAdapterException("Class [$classname] not found in file [".$path."]" );
					}
				}
				else 
				{
					$classpath = $identifier->path;
					$classtype = !empty($classpath) ? array_shift($classpath) : $identifier->name;
					
					//Check to see of the type is an alias
					if(array_key_exists($classtype, $this->_alias_map)) {
						$classtype = $this->_alias_map[$classtype];
					}
					
					//Create the fallback path and make an exception for views
					$path = ($classtype != 'view') ? KInflector::camelize(implode('_', $classpath)) : '';
				 	
					/*
					 * Find the classname to fallback too and auto-load the class
					 * 
					 * Fallback sequence : Component Generic -> Component Default -> Framework Generic -> Framework Default
					 */
					if(class_exists('Com'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name))) {
						$classname = 'Com'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name);
					} elseif(class_exists('Com'.ucfirst($identifier->package).ucfirst($classtype).$path.'Default')) {
						$classname = 'Com'.ucfirst($identifier->package).ucfirst($classtype).$path.'Default';
					} elseif(class_exists( 'K'.ucfirst($classtype).$path.ucfirst($identifier->name))) {
						$classname = 'K'.ucfirst($classtype).$path.ucfirst($identifier->name);
					} else {
						$classname = 'K'.ucfirst($classtype).$path.'Default';
					}
				}
			}
			
			//If the object is indentifiable push the identifier in through the constructor
			if(array_key_exists('KFactoryIdentifiable', class_implements($classname))) 
			{
				$identifier->filepath = KLoader::path($identifier);
				$options['identifier'] = $identifier;
			}
							
			// If the class has a factory method call it
			if(is_callable(array($classname, 'factory'), false)) {
				$instance = call_user_func(array($classname, 'factory'), $options);
			} else {
				$instance = new $classname($options);
			}
		}

		return $instance;
	}
}