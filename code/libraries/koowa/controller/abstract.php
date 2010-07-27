<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Controller
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract Controller Class
 *
 * Note: Concrete controllers must have a singular name
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package		Koowa_Controller
 * @uses		KMixinClass
 * @uses 		KCommandChain
 * @uses        KObject
 * @uses        KFactory
 */
abstract class KControllerAbstract extends KObject implements KObjectIdentifiable
{
	/**
	 * Array of class methods to call for a given action.
	 *
	 * @var	array
	 */
	protected $_action_map = array();

	/**
	 * Current or most recent action to be performed.
	 *
	 * @var	string
	 */
	protected $_action;


	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct( KConfig $config = null)
	{
        //If no config is passed create it
		if(!isset($config)) $config = new KConfig();
		
		parent::__construct($config);
        
		//Set the action
		$this->_action = $config->action;

        // Mixin the command chain
        $this->mixin(new KMixinCommandchain(new KConfig(
        	array('mixer' => $this, 'command_chain' => $config->command_chain, 'auto_events' => $config->auto_events)
        )));

        //Mixin a filter
        $this->mixin(new KMixinCommand(new KConfig(
        	array('mixer' => $this, 'command_chain' => $this->getCommandChain())
        )));
	}


    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
            'command_chain' =>  new KCommandChain(),
    		'action'		=> null,
    		'auto_events'	=> true
        ));
        
        parent::_initialize($config);
    }
    
    /**
	 * Get the object identifier
	 * 
	 * @return	KIdentifier	
	 * @see 	KObjectIdentifiable
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	}

	/**
	 * Execute an action by triggering a method in the derived class.
	 *
	 * @param	string		The action to execute
	 * @param	array		The data to pass to the action method
	 * @return	mixed|false The value returned by the called method, false in error case.
	 * @throws 	KControllerException
	 */
	public function execute($action, $data = null)
	{
		$action = strtolower($action);
		
		//Set the original action in the controller to allow it to be retrieved
		$this->setAction($action);

		//Find the mapped action if one exists
		if (isset( $this->_action_map[$action] )) {
			$action = $this->_action_map[$action];
		}
		
		//Create the command arguments object
		$context = $this->getCommandChain()->getContext();
		$context->caller = $this;
		$context->action = $action;
		$context->data   = $data;
		$context->result = false;
		
		if($this->getCommandChain()->run('controller.before.'.$action, $context) === true) 
		{
			$action = $context->action;
			$method = '_action'.ucfirst($action);
	
			if (!in_array($method, $this->getMethods())) {
				throw new KControllerException("Can't execute '$action', method: '$method' does not exist");
			}
			
			//Transfrom the data to pass it to the action method
			if(is_array($data) && $context->data instanceof KConfig) {
				$data = $context->data->toArray();
			} else {
				$data = $context->data;
			}
			
			$context->result = $this->$method($data);
			$this->getCommandChain()->run('controller.after.'.$action, $context);
		}

		return $context->result;
	}

	/**
	 * Gets the available actions in the controller.
	 *
	 * @return	array Array[i] of action names.
	 */
	public function getActions()
	{
		$result = array();
		foreach(get_class_methods($this) as $action)
		{
			if(substr($action, 0, 7) == '_action') {
				$result[] = strtolower(substr($action, 7));
			}
			
			$result = array_unique(array_merge($result, array_keys($this->_action_map)));
		}
		return $result;
	}

	/**
	 * Get the action that is was/will be performed.
	 *
	 * @return	 string Action name
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**
	 * Set the action that will be performed.
	 *
	 * @param	string Action name
	 * @return  KControllerAbstract
	 */
	public function setAction($action)
	{
		$this->_action = $action;
		return $this;
	}

	/**
	 * Register (map) an action to a method in the class.
	 *
	 * @param	string	The action.
	 * @param	string	The name of the method in the derived class to perform
	 *                  for this action.
	 * @return	KControllerAbstract
	 */
	public function registerActionAlias( $alias, $action )
	{
		$alias = strtolower( $alias ); 
       	
		if ( !in_array($alias, $this->getActions()) )  { 
          	$this->_action_map[$alias] = $action; 
       	} 
	
		return $this;
	}

	/**
	 * Unregister (unmap) an action
	 *
	 * @param	string	The action
	 * @return	KControllerAbstract
	 */
	public function unregisterActionAlias( $action )
	{
		unset($this->_action_map[strtolower($action)]);
		return $this;
	}

	/**
	 * Execute a controller action by it's name. 
	 * 
	 * @param	string	Method name
	 * @param	array	Array containing all the arguments for the original call
	 * @see execute()
	 */
	public function __call($method, $args)
	{
		if(in_array($method, $this->getActions())) {
			return $this->execute($method, !empty($args) ? $args[0] : null);
		}
		
		return parent::__call($method, $args);
	}
}