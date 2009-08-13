<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_Controller
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPL <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * REST Controller Class
 *
 * @author		Mathias Verraes <mathias@koowa.org> 
 * @category	Koowa
 * @package     Koowa_Controller
 */
class KControllerRest extends KControllerAbstract
{	
	public function __construct()
	{
		parent::__construct();
		
		// needed because get() is already in KObject
		$this->registerActionAlias('get', 'get');
	}
	
	/**
	 * Get the action that is was/will be performed.
	 *
	 * @throws KControllerException
	 * @return	 string Action name
	 */
	public function getAction()
	{
		if(!isset($this->_action))
		{
			// Find the action from the _method variable, or use the request method
    		$action	= strtolower(KRequest::get('post._method', 'cmd'));
    		
    		if(is_null($action)) 
    		{ 
    			try {
    				$action = strtolower(KRequest::method());
    			} catch (KRequestException $e) {
    				throw new KControllerException('Action not supported: '.$action);
    			}
    			
    			$this->_action = $action;
    		} 
		}
		
		return $this->_action;
	}
	
	/**
	 * Typical REST get action
	 * 
	 * @return void
	 */
	protected function _actionGet()	
	{
		return KInflector::isPlural($view) ? $this->execute('browse') : $this->execute('read');
	}
	
	/**
	 * Typical REST post action
	 * 
	 * @return void
	 */
	protected function _actionPost()
	{
		$id = KRequest::get('post.id', 'int');
		// if there are no id's, we are adding an item
		return (empty($id)) ? $this->execute('add') : $this->execute('edit');
	}
	
	/**
	 * Typical REST put action
	 * 
	 * @return void
	 */
	protected function _actionPut()
	{
		return $this->execute('add');
	}
	
	/**
	 * Typical REST delete action
	 * 
	 * @return void
	 */
	protected function _actionDelete()
	{
		return parent::_actionDelete();
	}	
}