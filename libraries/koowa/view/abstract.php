<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_View
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract View Class
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package		Koowa_View
 * @uses		KMixinClass
 * @uses 		KTemplate
 * @uses 		KFactory
 */
abstract class KViewAbstract extends KObject implements KObjectIdentifiable
{
	/**
	 * Model identifier (APP::com.COMPONENT.model.MODELNAME)
	 *
	 * @var	string|object
	 */
	protected $_model;
	
	/**
	 * The output of the view
	 *
	 * @var string
	 */
	public $output = '';

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config = null)
	{
		//If no config is passed create it
		if(!isset($config)) $config = new KConfig();
		
		parent::__construct($config);
		
		//Set the output if defined in the config
		$this->output = $config->output;

		// set the model
		if(!empty($config->model)) {
			$this->setModel($config->model);
		}
	}

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
            'base_url'      => KRequest::base(),
        	'media_url'		=> KRequest::root().'/media',
			'model'   		=> null,
    		'output'		=> ''
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
	 * Get the name
	 *
	 * @return 	string 	The name of the object
	 */
	public function getName()
	{
		$total = count($this->_identifier->path);
		return $this->_identifier->path[$total - 1];
	}

	/**
	 * Echo's the views output
 	 *
	 * @return KViewAbstract
	 */
	public function display()
	{
		echo $this->output;
		return $this;
	}

	/**
	 * Get the identifier for the model with the same name
	 *
	 * @return	KIdentifierInterface
	 */
	final public function getModel()
	{
		if(!$this->_model)
		{
			$identifier	= clone $this->_identifier;
			$name = array_pop($identifier->path);
			$identifier->name	= KInflector::isPlural($name) ? $name : KInflector::pluralize($name);
			$identifier->path	= array('model');
			
			$this->_model = $identifier;
		}
       	
		return $this->_model;
	}
	
	/**
	 * Method to set a model object attached to the view
	 *
	 * @param	mixed	An object that implements KObjectIdentifiable, an object that 
	 *                  implements KIndentifierInterface or valid identifier string
	 * @throws	KDatabaseRowsetException	If the identifier is not a table identifier
	 * @return	KViewAbstract
	 */
	public function setModel($model)
	{
		$identifier = KFactory::identify($model);
		
		if($identifier->path[0] != 'model') {
			throw new KModelException('Identifier: '.$identifier.' is not a model identifier');
		}
		
		$this->_model = $identifier;
		return $this;
	}

	/**
	 * Create a route based on a full or partial query string 
	 * 
	 * index.php, option, view and layout can be ommitted. The following variations 
	 * will all result in the same route
	 *
	 * - foo=bar
	 * - option=com_mycomp&view=myview&foo=bar
	 * - index.php?option=com_mycomp&view=myview&foo=bar
	 * 
	 * If the route starts '&' the information will be appended to the current URL.
	 *
	 * In templates, use @route()
	 *
	 * @param	string	The query string used to create the route
	 * @return 	string 	The route
	 */
	public function createRoute( $route = '')
	{
		$route = trim($route);

		// Special cases
		if($route == 'index.php' || $route == 'index.php?') 
		{
			$result = $route;
		} 
		else if (substr($route, 0, 1) == '&') 
		{
			$url   = clone KRequest::url();
			$vars  = array();
			parse_str($route, $vars);
			
			$result = (string) $url->setQuery(array_merge($url->getQuery(true), $vars));;
		}
		else 
		{
			// Strip 'index.php?'
			if(substr($route, 0, 10) == 'index.php?') {
				$route = substr($route, 10);
			}

			// Parse route
			$parts = array();
			parse_str($route, $parts);
			$result = array();

			// Check to see if there is component information in the route if not add it
			if(!isset($parts['option'])) {
				$result[] = 'option=com_'.$this->_identifier->package;
			}

			// Add the layout information to the route only if it's not 'default'
			if(!isset($parts['view']))
			{
				$result[] = 'view='.$this->getName();
				if(!isset($parts['layout']) && $this->_layout != 'default') {
					$result[] = 'layout='.$this->_layout;
				}
			}
			
			// Add the format information to the URL only if it's not 'html'
			if(!isset($parts['format']) && $this->_identifier->name != 'html') {
				$result[] = 'format='.$this->_identifier->name;
			}

			// Reconstruct the route
			if(!empty($route)) {
				$result[] = $route;
			}

			$result = 'index.php?'.implode('&', $result);
			
		}

		return JRoute::_($result);
	}
	
	/**	
	 * Returns the views output
 	 *
	 * @return 	string
	 */
	public function __toString()
	{
		return $this->output;
	}
}