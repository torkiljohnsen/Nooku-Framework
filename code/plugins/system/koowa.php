<?php
/**
 * @version     $Id$
 * @category	Koowa
 * @package     Koowa_Plugins
 * @subpackage  System
 * @copyright   Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Koowa System plugin
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa
 */
class plgSystemKoowa extends JPlugin
{
	public function __construct($subject, $config = array())
	{
		// Check if Koowa is active
		if(JFactory::getApplication()->getCfg('dbtype') != 'mysqli')
		{
    		JError::raiseWarning(0, JText::_("Koowa plugin requires MySQLi Database Driver. Please change your database configuration settings to 'mysqli'"));
    		return;
		}
		
		set_exception_handler(array($this, 'exceptionHandler'));

		// Require the library loader
		JLoader::import('plugins.system.koowa.koowa', JPATH_ROOT);
		JLoader::import('plugins.system.koowa.loader.loader', JPATH_ROOT);

		//Add loader adapters
		KLoader::addAdapter(new KLoaderAdapterJoomla());
		KLoader::addAdapter(new KLoaderAdapterModule());
		KLoader::addAdapter(new KLoaderAdapterPlugin());
        KLoader::addAdapter(new KLoaderAdapterComponent());
            
		//Add factory adapters
		KFactory::addAdapter(new KFactoryAdapterJoomla());
		KFactory::addAdapter(new KFactoryAdapterModule());
		KFactory::addAdapter(new KFactoryAdapterPlugin());
		KFactory::addAdapter(new KFactoryAdapterComponent());
		
		// Decorate the application object
		$app  =& JFactory::getApplication();
		$app  = new KDecoratorJoomlaApplication($app);
		
		//Create the koowa database object
		$jdb  =& JFactory::getDBO();
		
		KFactory::get('lib.koowa.database', array('adapter' => 'mysqli'))
			->setConnection($jdb->_resource)
			->setTablePrefix($jdb->_table_prefix);
		
		// Don't proxy the dataase if we are in com_installer
		/*if(KRequest::get('request.option', 'cmd') != 'com_installer')
		{
			// Decorate the database object
			$jdb = new KDecoratorJoomlaDatabase($jdb);
			
			//ACL uses the unwrapped DBO
       		$acl = JFactory::getACL();
        	$acl->_db = $jdb->getObject(); // getObject returns the unwrapped DBO
		}*/

        //Set factory identifier aliasses
        KFactory::map('lib.koowa.application', 'lib.joomla.application');
        KFactory::map('lib.koowa.language',    'lib.joomla.language');
        KFactory::map('lib.koowa.document',    'lib.joomla.document');
         
        //Force the format to ajax is request type is AJAX
        if(KRequest::type() == 'AJAX') {
        	$GLOBALS['_JREQUEST']['format']['DEFAULTWORD0'] = 'raw';
        }

		//Load the koowa plugins
		JPluginHelper::importPlugin('koowa', null, true, KFactory::get('lib.koowa.event.dispatcher'));

		parent::__construct($subject, $config = array());
	}
	
 	/**
	 * Catch all exception handler
	 *
	 * Calls the Joomla error handler to process the exception
	 *
	 * @param object an Exception object
	 * @return void
	 */
	public function exceptionHandler($exception)
	{
		$this->_exception = $exception; //store the exception for later use
		
		//Change the Joomla error handler to our own local handler and call it
		JError::setErrorHandling( E_ERROR, 'callback', array($this,'errorHandler'));
		JError::raiseError('500', $exception->getMessage());
	}

	/**
	 * Custom JError callback
	 *
	 * Push the exception call stack in the JException returned through the call back
	 * adn then rener the custom error page
	 *
	 * @param object A JException object
	 * @return void
	 */
	public function errorHandler($error)
	{
		$error->backtrace = $this->_exception->getTrace();
		$error->file      = $this->_exception->getFile();
		$error->line      = $this->_exception->getLine();
		
		if(KFactory::get('lib.joomla.config')->getValue('config.debug')) {
			$error->message   = $this->_exception;
		} else {
			$error->message   = '';
		}
		
		JError::customErrorPage($error);
	}
}