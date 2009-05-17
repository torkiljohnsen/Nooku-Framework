<?php
/**
* @version      $Id$
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Base button class for a toolbar
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 * 
 * @uses		KInflector
 */
abstract class KToolbarButtonAbstract extends KObject implements KToolbarButtonInterface
{
	/**
	 * Method used to submit the form
	 *
	 * @var string	[get|post|delete]
	 */
	protected $_method;
	
	/**
	 * Constructor
	 * 
	 * @param array	Options array
	 */
	public function __construct(array $options = array())
	{
        // Initialize the options
        $this->_options  = $this->_initialize($options);
        if($this->_options['parent']) {
        	$this->setParent($this->_options['parent']);
        }
        $this->setMethod($this->_options['method']);
        
        // Mixin the KClass
        $this->mixin(new KMixinClass(array('mixer' => $this, 'name_base' => 'ToolbarButton')));

        // Assign the classname with values from the config
        $this->setClassName($this->_options['name']);
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
    	$tmpname = isset($options['name']['suffix']) ? $options['name']['suffix'] : KInflector::getPart(get_class($this), -1); 
    	
        $defaults = array(
            'name'      => array(
                        'prefix'    => 'k',
                        'base'      => 'toolbarButton',
                        'suffix'    => $tmpname
                        ),
            'parent'	=> null,
            'icon'		=> 'icon-32-'.$tmpname,
            'id'		=> $tmpname,
			'text'		=> ucfirst($tmpname),
            'method'	=> 'get'
        );

        return array_merge($defaults, $options);
    }    
    
    /**
	 * Set the parent toolbar
	 *
	 * @param 	KToolbarInterface 	Toolbar
	 * @return 	this
	 */
    public function setParent(KToolbarInterface $toolbar)
    {
    	$this->_parent = $toolbar;
    	return $this;
    }
    
	/**
	 * Get the parent toolbar
	 *
	 * @return 	KToolbarInterface 	Toolbar
	 */
    public function getParent()
    {
    	return $this->_parent;
    }
		
	/**
	 * Set the method used to submit the form
	 *
	 * @param	string	[get|post|delete]
	 * @return 	this
	 */
	public function setMethod($method)
	{
		$this->_method = strtolower($method);
		return $this;
	}
		
	/**
	 * Get the element name
	 *
	 * @return	string	Button name
	 */
	public function getName()
	{
		return $this->getClassName('suffix');
	}

	public function render()
	{
		$text	= JText::_($this->_options['text']);
		
		$html 	= array ();
		$html[]	= '<td class="button" id="'.$this->getId().'">';
		$html[]	= '<a href="'.JRoute::_($this->getLink()).'" onclick="'. $this->getOnClick().'" class="toolbar">';
		
		$html[]	= '<span class="'.$this->getClass().'" title="'.$text.'">';
		$html[]	= '</span>';
		$html[]	= $text;
		$html[]	= '</a>';
		$html[]	= '</td>';

		return implode(PHP_EOL, $html);
	}
	
	public function getLink()
	{
		return '#';
	}
	
	public function getOnClick()
	{
		return '';
	}
	
	public function getId()
	{
		return 'toolbar-'.$this->getParent()->getName().'-'.$this->_options['id'];
	}
	
	public function getClass()
	{
		return $this->_options['icon'];
	}
}