<?php
/**
 * @version     $Id$
 * @category	Koowa
 * @package     Koowa_View
 * @subpackage  Json
 * @copyright   Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license     GNU GPL <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * View JSON Class
 *
 * @author      Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package     Koowa_View
 * @subpackage  Json
 */
class KViewJson extends KViewAbstract
{
	public function __construct(array $options = array())
	{
		parent::__construct($options);

		//Set the correct mime type
		$this->_document->setMimeEncoding('application/json');
	}

    public function display()
    {
    	echo json_encode($this->getProperties());
    }
}