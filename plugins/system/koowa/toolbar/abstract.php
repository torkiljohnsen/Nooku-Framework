<?php
/**
* @version      $Id$
* @category		Koowa
* @package		Koowa_Toolbar
* @copyright    Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPL <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Abstract Toolbar class
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @uses		KInflector
 * @uses		KMixinClass
 * @uses 		KFactory
 */
abstract class KToolbarAbstract extends KObject implements KToolbarInterface
{
	/**
	 * Buttons in the toolbar
	 *
	 * @var		array
	 */
	protected $_buttons = array();

	/**
	 * Constructor
	 *
	 * @param array	Options array
	 */
	public function __construct(array $options = array())
	{
		$this->identifier = $options['identifier'];

        // Initialize the options
        $options  = $this->_initialize($options);


        // Set the title
        $title = empty($options['title']) ? KInflector::humanize($this->getName()) : $options['title'];
        $this->setTitle($title);
	}

	/**
	 * Get the toolbar's name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->identifier->name;
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
            'title'		=> null
        );

        return array_merge($defaults, $options);
    }

	/**
	 * Append a button
	 *
	 * @param	KToolbarButtonInterface|string	Button
	 * @return	this
	 */
	public function append($button)
	{
		array_push($this->_buttons, $button);
		return $this;
	}

	/**
	 * Prepend a button
	 *
	 * @param	KToolbarButtonInterface	Button
	 * @return	this
	 */
	public function prepend($button)
	{
		array_unshift($this->_buttons, $button);
		return $this;
	}

	/**
	 * Render the toolbar
	 *
	 * @throws KToolbarException When the button could not be found
	 * @return	string	HTML
	 */
	public function render()
	{

		$id		= 'toolbar-'.$this->getName();
		$html = array ();

		// Start toolbar div
		$html[] = '<div class="toolbar" id="'.$id.'">';
		$html[] = '<table class="toolbar"><tr>';

		// Render each button in the toolbar
		foreach ($this->_buttons as $button)
		{
			if(!($button instanceof KToolbarButtonInterface))
			{
				$app		= $this->identifier->application;
				$component	= $this->identifier->component;
				$button = KFactory::tmp($app.'::com.'.$component.'.toolbar.button.'.$button);
			}

			$button->setParent($this);
			$html[] = $button->render();
		}

		// End toolbar div
		$html[] = '</tr></table>';
		$html[] = '</div>';

		return implode(PHP_EOL, $html);
	}

	/**
	 * Set the toolbar's title and icon
	 *
	 * @param 	string	Title
	 * @param 	string	Icon
	 * @return 	KToolbarInterface
	 */
	public function setTitle($title, $icon = 'generic.png')
	{
		//strip the extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		$html  = "<div class=\"header icon-48-$icon\">\n";
		$html .= "$title\n";
		$html .= "</div>\n";

		KFactory::get('lib.joomla.application')->set('JComponentTitle', $html);

		return $this;
	}
}