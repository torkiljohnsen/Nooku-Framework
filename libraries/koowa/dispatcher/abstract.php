<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Dispatcher
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract controller dispatcher
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Dispatcher
 * @uses		KMixinClass
 * @uses        KObject
 * @uses        KFactory
 */
abstract class KDispatcherAbstract extends KControllerAbstract
{
	/**
	 * Controller object or identifier (APP::com.COMPONENT.controller.NAME)
	 *
	 * @var	string|object
	 */
	protected $_controller;
	
	/**
	 * Default controller name
	 *
	 * @var	string
	 */
	protected $_controller_default;
	
	/**
	 * The request data
	 * 
	 * @var KConfig
	 */
	protected $_request;
	
	/**
	 * The request persistency
	 * 
	 * @var boolean
	 */
	protected $_request_persistent;

	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		//Set the request
		$this->_request = $config->request;
		
		//Set the request persistency
		$this->_request_persistent = $config->request_persistent;
		
		//Set the controller default
		$this->_controller_default = $config->controller_default;
		
		if($config->controller !== null) {
			$this->setController($config->controller);
		}

		if(KRequest::method() != 'GET') 
		{
			$this->registerCallback('before.dispatch', array($this, 'authorize'));
			$this->registerCallback('after.dispatch' , array($this, 'forward'));
	  	}

	  	$this->registerCallback('after.dispatch', array($this, 'render'));
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
        	'controller'			=> null,
    		'controller_default'	=> $this->_identifier->package,
    		'request'				=> KRequest::get('get', 'string'),
    		'request_persistent' 	=> false
        ));

        parent::_initialize($config);
    }

	/**
	 * Method to get a controller identifier
	 *
	 * @return	object	The controller.
	 */
	public function getController()
	{
		if(!$this->_controller)
		{
			$application 	= $this->_identifier->application;
			$package 		= $this->_identifier->package;

			//Get the controller name
			$controller = KRequest::get('get.view', 'cmd', $this->_controller_default);
			
			//In case we are loading a subview, we use the first part of the name as controller name
			if(strpos($controller, '.') !== false)
			{
				$result = explode('.', $controller);

				//Set the controller based on the parent
				$controller = $result[0];
			}

			// Controller names are always singular
			if(KInflector::isPlural($controller)) {
				$controller = KInflector::singularize($controller);
			}
			
			$config = array(
        		'request' 	   => $this->_request,
        		'persistent'   => $this->_request_persistent,
        		'auto_display' => true
        	);

			$this->_controller = KFactory::get($application.'::com.'.$package.'.controller.'.$controller, $config);
		}

		return $this->_controller;
	}

	/**
	 * Method to set a controller object attached to the dispatcher
	 *
	 * @param	mixed	An object that implements KObjectIdentifiable, an object that
	 *                  implements KIndentifierInterface or valid identifier string
	 * @throws	KDatabaseRowsetException	If the identifier is not a controller identifier
	 * @return	KDispatcherAbstract
	 */
	public function setController($controller)
	{
		if(!($controller instanceof KControllerAbstract))
		{
			$identifier = KFactory::identify($controller);

			if($identifier->path[0] != 'controller') {
				throw new KDispatcherException('Identifier: '.$identifier.' is not a controller identifier');
			}

			$this->_controller = $identifier;
		}
		
		$this->_controller = $controller;
		return $this;
	}

	/**
	 * Get the action that is was/will be performed.
	 *
	 * If the action cannot be found in the POST request it will determined based on the request 
	 * method and mapped to one of the 5 BREAD actions.
	 *
	 * - GET    : either 'browse' (for list views) or 'read' (for item views).
	 * - POST   : add
	 * - PUT    : edit
	 * - DELETE : delete
	 *
	 * @return	 string Action name
	 */
	public function getAction()
	{
		$action = KRequest::get('post.action', 'cmd');

		if(empty($action))
		{
			switch(KRequest::method())
			{
				case 'GET'    :
				{
					//Determine if the action is browse or read based on the view information
					$view   = KRequest::get('get.view', 'cmd');
					$action = KInflector::isPlural($view) ? 'browse' : 'read';
				} break;

				case 'POST'   : $action = 'add';    break;
				case 'PUT'    : $action = 'edit'  ; break;
				case 'DELETE' : $action = 'delete';	break;
			}
		}

		return $action;
	}
	
	/**
	 * Get the data from the reques based the request method
	 *
	 * @return	array 	An array with the request data
	 */
	public function getData()
	{
		$method = KRequest::method();
        $data   = $method != 'GET' ? KRequest::get(strtolower($method), 'raw') : null;
        
        return $data;
	}

	/**
	 * Dispatch the controller
	 *
	 * @param	string		The controller to dispatch. If null, it will default to
	 * 						retrieve the controller information from the request or
	 * 						default to the component name if no controller info can
	 * 						be found.
	 *
	 * @return	mixed
	 */
	protected function _actionDispatch(KCommandContext $context)
	{        	
		if($context->data) {
        	$this->_controller_default = KConfig::toData($context->data);
        }
        	
        $result = $this->getController()->execute($this->getAction(), $this->getData());
        return $result;
	}
	
	/**
	 * Check the token to prevent CSRF exploits
	 *
	 * @return  void|false Returns false if the authorization failed
	 * @throws 	KDispatcherException
	 */
	public function _actionAuthorize(KCommandContext $context)
	{
        if( KRequest::token() !== JUtility::getToken())
        {
        	throw new KDispatcherException('Invalid token or session time-out.', KHttp::UNAUTHORIZED);
        	return false;
        }
	}

	/**
	 * Forward after a post request
	 *
	 * Either do a redirect or a execute a browse or read action in the controller
	 * depending on the request method and type
	 *
	 * @return mixed
	 */
	public function _actionForward(KCommandContext $context)
	{
		if (KRequest::type() == 'HTTP')
		{
			if($redirect = KFactory::get($this->getController())->getRedirect())
			{
				KFactory::get('lib.koowa.application')
					->redirect($redirect['url'], $redirect['message'], $redirect['type']);
			}
		}

		if(KRequest::type() == 'AJAX')
		{
			$view = KRequest::get('get.view', 'cmd');
			$context->result = KFactory::get($this->getController())->execute(KInflector::isPlural($view) ? 'browse' : 'read');
			return $context->result;
		}
	}

	/**
	 * Push the controller data into the document
	 *
	 * This function divert the standard behavior and will push specific controller data
	 * into the document
	 *
	 * @return	mixed
	 */
	protected function _actionRender(KCommandContext $context)
	{
		if(is_string($context->result)) {
			return $context->result;
		}
	}
}