<?php
/**
* @version		$Id$
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPL <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.koowa.org
*/

/**
 * Raw filter
 *
 * Always validates and returns the raw variable
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterRaw extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return true;
	}
	
	/**
	 * Sanitize a value
	 *
	 * @param	scalar	Variable to be sanitized
	 * @return	mixed
	 */
	protected function _sanitize($value)
	{
		return $value;
	}
}