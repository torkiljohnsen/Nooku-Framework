<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Controller
 * @copyright	Copyright (C) 2007 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract Bread Controller Class
 *
 * @author		Mathias Verraes <mathias@joomlatools.eu>
 * @category	Koowa
 * @package		Koowa_Controller
 */
class KControllerBread extends KControllerAbstract
{
	/**
	 * Browse a list of items
	 */
	public function browse()
	{
		$layout	= KInput::get('layout', 'get', 'cmd', null, 'default' );
		
		$this->getView()
			->setLayout($layout)
			->display();
	}
	
	/**
	 * Display a single item
	 */
	public function read()
	{
		$layout	= KInput::get('layout', 'get', 'cmd', null, 'default' );
		
		$this->getView()
			->setLayout($layout)
			->display();
	}
	
	/*
	 * Generic edit action, saves over an existing item
	 */
	public function edit()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		// Get the post data from the request
		$data = $this->_getRequest('post');

		// Get the id
		$id	 = KInput::get('id', 'get', 'int');

		// Get the table object attached to the model
		$component 	= $this->getClassName('prefix');
		$suffix    	= $this->getClassName('suffix');
		$model		= KInflector::pluralize($suffix);

		$app   		= KFactory::get('lib.joomla.application')->getName();
		$table 		= KFactory::get($app.'::com.'.$component.'.model.'.$model)->getTable();
		$row 		= $table->fetchRow($id)
					->setProperties($data)
					->save();
	}
	
	/*
	 * Generic add action, saves a new item
	 */
	public function add()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');

		// Get the post data from the request
		$data = $this->_getRequest('post');

		// Get the table object attached to the model
		$component = $this->getClassName('prefix');
		$suffix    	= $this->getClassName('suffix');
		$model		= $suffix;
		$view	   	= $suffix;
		
		$app   		= KFactory::get('lib.joomla.application')->getName();
		$table 		= KFactory::get($app.'::com.'.$component.'.model.'.$model)->getTable();
		$row 		= $table->fetchRow()
					->setProperties($post)
					->save();
	}	
	
	/*
	 * Generic delete function
	 */
	public function delete()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		$cid = KInput::get('cid', 'post', 'array.ints', null, array());

		// Get the table object attached to the model
		$component = $this->getClassName('prefix');
		$model    	= $this->getClassName('suffix');

		$app   = KFactory::get('lib.joomla.application')->getName();
		$table = KFactory::get($app.'::com.'.$component.'.model.'.$model)->getTable()
				->delete($cid);
	}

}
