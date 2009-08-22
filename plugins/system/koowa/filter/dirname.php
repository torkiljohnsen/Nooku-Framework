<?php
/**
* @version		$Id$
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.koowa.org
*/

/**
 * Dirname filter 
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterDirname extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);
	   	return ((string) $value === $this->sanitize($value));
	}
	
	/**
	 * Sanitize a value
	 *
	 * @param	scalar	Variable to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		$value = trim($value);
    	return dirname($value);
	}
}