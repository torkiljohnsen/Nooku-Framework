<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Mixin
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Filter Command
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Mixin
 */
class KMixinCommand extends KMixinAbstract implements KCommandInterface 
{
 	/**
 	 * Array of fucntions to be executed on before commands
 	 * 
 	 * $var array
 	 */
	protected $_functions_before = array();
	
	/**
 	 * Array of functions to be executed on after commands
 	 * 
 	 * $var array
 	 */
	protected $_functions_after = array();

	/**
	 * Object constructor
	 *
	 * @param	array 	An optional associative array of configuration settings.
	 * Recognized key values include 'mixer' (this list is not meant to be comprehensive).
	 */
	public function __construct(array $options = array())
	{
		// Initialize the options
        $options = $this->_initialize($options);
        
        parent::__construct($options);
		
		if(is_null($options['command_chain'])) {
			throw new KMixinException('command_chain [KCommandChain] option is required');
		}
	
		//Enque the command in the mixer's command chain
		$options['command_chain']->enqueue($this, 2);
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
        parent::_initialize($options);
    	
    	$defaults = array(
    		'command_chain'	=> null,
    	);

        return array_merge($defaults, $options);
    }
    
	/**
	 * Command handler
	 * 
	 * @param string  The command name
	 * @param object  The command context
	 *
	 * @return boolean
	 */
	final public function execute( $name, KCommandContext $context) 
	{
		$parts  = explode('.', $name);
		$result = true;
		
		$functions = ($parts[1] == 'before') ? $this->_functions_before :$this->_functions_after;
					
		if (isset($functions[$parts[2]]))
		{ 
			$functions = $functions[$parts[2]];
			
   		 	foreach($functions as $function => $parameters) 
   		 	{
   		 		if(empty($parameters)) {
        			$result = $this->_mixer->$function();
        		} else {
        			$result = call_user_func_array(array($this->_mixer, $function), $parameters);
        		}
        		
				if ( $result === false) {
        			break;
        		}
   		 	}
		}
		
		return $result === false ? false : true;
	}
	
	/**
 	 * Get the registered before functions for a method
 	 *  	  
 	 * @param  	string	The method to return the functions for
 	 * @return  array	A list of registered functions	
 	 */
	public function getFunctionsBefore($method)
	{
		$result = array();
		$method = strtolower($method);
		
		if (isset($this->_functions_before[$method]) ) {
       	 	$result = $this->_functions_before[$method];
		}
		
    	return $result;
	}
	
	/**
 	 * Get the registered after functions for a method
 	 *  	  
 	 * @param  	string	The method to return the functions for
 	 * @return  array	A list of registered functions	
 	 */
	public function getFunctionsAfter($method)
	{
		$result = array();
		$method = strtolower($method);
		
		if (isset($this->_functions_after[$method]) ) {
       	 	$result = $this->_functions_after[$method];
		}
		
    	return $result;
	}
	
	/**
 	 *  Registers a single function or an array of functions
 	 * 
 	 * @param  	string|array	The method name to register the funtion for or an array of method names
 	 * @param 	string|array	A single function or an array of functions to register
 	 * @param 	array			The parameters to be passed to the function, as an indexed array. 
 	 * @return  KObject	The mixer object
 	 */
	public function registerFunctionBefore($methods, $functions, array $parameters = array())
	{
		$methods   = (array) $methods;
		$functions = array_flip((array)$functions);
		
		foreach($methods as $method)
		{
			$method = strtolower($method);
		
			if (!isset($this->_functions_before[$method]) ) {
       	 		$this->_functions_before[$method] = array();	
			}
		
			foreach ($functions as $key => $value) {
				$functions[$key] = $parameters; 
			}

    		$this->_functions_before[$method] = array_merge($this->_functions_before[$method], $functions);
		}
		
		return $this->_mixer;
	}
	
	/**
 	 * Unregister a single function or an array of functions
 	 * 
 	 * @param  	string|array	The method name to register the function from or an array of method names
 	 * @param 	string|array	A single function or an array of functions to unregister
 	 * @return  KObject The mixer object
 	 */
	public function unregisterFunctionBefore($methods, $functions)
	{
		$methods = (array) $methods;
		
		foreach($methods as $method)
		{
			$method = strtolower($method);
			
			if (isset($this->_functions_before[$method]) ) 
			{
				foreach ((array) $functions as $function) {
					unset($this->_functions_before[$method][$function]);
				}
			}
		}
		
		return $this->_mixer;
	}
	
	/**
 	 * Registers a single function or an array of functions
 	 * 
 	 * @param  	string|array	The method name to register the function too or an array of method names
 	 * @param 	string|array	A single function or an array of functions to register
 	 * @param 	array			The parameters to be passed to the function, as an indexed array. 
 	 * @return  KObject The mixer object
 	 */
	public function registerFunctionAfter($methods, $functions,  array $parameters = array())
	{
		$methods   = (array) $methods;
		$functions = array_flip((array)$functions);
		
		foreach($methods as $method)
		{
			$method = strtolower($method);
		
			if (!isset($this->_functions_after[$method]) ) {
       	 		$this->_functions_after[$method] = array();	
			}
			
			foreach ($functions as $key => $value) {
				$functions[$key] = $parameters; 
			}

    		$this->_functions_after[$method] = array_merge($this->_functions_after[$method], $functions);
		}
		
    	return $this->_mixer;
	}
	
	/**
 	 * Unregister a single function or an array of functions
 	 * 
 	 * @param  	string|array	The method name to register the function too or an array of method names
 	 * @param 	string|array	A single function or an array of function to unregister
 	 * @return  KObject The mixer object
 	 */
	public function unregisterFunctionAfter($methods, $functions)
	{
		$methods = (array) $methods;
		
		foreach($methods as $method)
		{
			$method = strtolower($method);
		
			if (isset($this->_functions_after[$method]) ) 
			{
       	 		foreach ((array) $functions as $function) {
					unset($this->_functions_after[$method][$function]);
				}
			}
		}
				
		return $this->_mixer;
	}
	
	/**
	 * Get the methods that are available for mixin. 
	 * 
	 * This functions overloads KMixinAbstract::getMixableMethods and excludes the execute()
	 * function from the list of available mixable methods.
	 * 
	 * @return array An array of methods
	 */
	public function getMixableMethods(KObject $mixer = null) 
	{
        return array_diff(parent::getMixableMethods(), array('execute'));  
	}
}