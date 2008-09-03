<?php
/**
 * @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
 * @package      Koowa_Input
 * @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Request class
 *
 * Allows to get input from GET, POST, COOKIE, ENV, SERVER, REQUEST
 * 
 * @todo Ideally, REQUEST should never be used, unfortunately Joomla has too 
 * many places where you can't get around it. WIP
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Input
 * @version     1.0
 * @uses 		KInflector
 * @uses		KFilter
 * @static
 */
class KInput
{
	/**
	 * List of accepted hashes
	 * 
	 * @var	array
	 */
	protected static $_hashes = array('COOKIE', 'ENV', 'FILES', 'GET', 'POST', 'SERVER', 'REQUEST');
	
	
	/**
	 * Get a validated and optionally sanitized variable from the request. 
	 * 
	 * When no sanitizers are supplied, the same filters as the validators will 
	 * be used.
	 * 
	 * @param	string	Variable name
	 * @param 	string  Hash [GET|POST|COOKIE|ENV|SERVER]
	 * @param 	mixed	Validator(s), can be a KFilterInterface object, or array of objects 
	 * @param 	mixed	Sanitizer(s), can be a KFilterInterface object, or array of objects
	 * @param 	mixed	Default value when the variable doesn't exist
	 * @throws	KInputException	When the variable doesn't validate
	 * @return 	mixed	(Sanitized) variable 
	 */
	public static function get($var, $hash, $validators, $sanitizers = array(), $default = null)
	{
		// Is the hash in our list?
		$hash = strtoupper($hash);
		if(!in_array($hash, self::$_hashes)) {
			throw new KRequestException('Unknown hash: '.$hash);
		}		
		
		// return the default value if $var wasn't set in the request
		if(empty($GLOBALS['_'.$hash][$var])) {
			return $default; 	
		}

		$result = $GLOBALS['_'.$hash][$var];
		$result = is_scalar($result) ? trim($GLOBALS['_'.$hash][$var]) : $result;
	
		// turn $validators or $sanitizers is an object, turn it into an array of objects
		// don't use settype because it will convert objects to arrays
		$validators = is_array($validators) ? $validators : (empty($validators) ? array() : array($validators));
		// if no sanitizers are given, use the validators
		$sanitizers = empty($sanitizers) ? $validators : (is_array($sanitizers) ? $sanitizers : array($sanitizers));
		
		// validate the variable
		foreach($validators as $filter)
		{
			//Create the filter if needed
			if(is_string($filter)) {
				$filter = KFactory::tmp('lib.koowa.filter.'.$filter);
			}
		
			if(!($filter instanceof KFilterInterface)) {
				throw new KInputException('Invalid filter passed: '.get_class($filter));
			}
			
			if(!$filter->validate($result)) 
			{
				$filtername = KInflector::getPart(get_class($filter), -1);
				throw new KInputException('Input is not a valid '.$filtername);
			}			 
		}
		
		// sanitize the variable
		foreach($sanitizers as $filter)
		{
			//Create the filter if needed
			if(is_string($filter)) {
				$filter = KFactory::tmp('lib.koowa.filter.'.$filter);
			}
		
			if(!($filter instanceof KFilterInterface)) {
				throw new KInputException('Invalid filter passed: '.get_class($filter));
			}
			
			$result = $filter->sanitize($result);		 
		}
		
		return $result;
	}
	
	/**
	 * Set a variable in the request
	 *
	 * @param 	mixed	Variable name
	 * @param 	mixed	Variable value
	 * @param 	string	Hash
	 */
	public static function set($var, $value, $hash) 
	{
		self::_initialize();
		
		// Is the hash in our list?
		$hash = strtoupper($hash);
		if(!in_array($hash, self::$_hashes)) {
			throw new KInputException('Unknown hash: '.$hash);
		}
		
		if('REQUEST' == $hash) {
			throw new KInputException('Can\'t set _REQUEST values, use GET, POST or COOKIE');
		}
		
		// add to hash in the superglobal
		$GLOBALS['_'.$hash][$var] 		= $value;
		
		// Add to _REQUEST hash if original hash is get, post, or cookies
		if(in_array($hash, array('GET', 'POST', 'COOKIE'))) {
			$GLOBALS['_REQUEST'][$var] 	= $value;
		}
	}
	
	/**
	 * Get the request method
	 *
	 * @return 	string
	 */
	public static function getMethod()
	{
		return strtoupper($GLOBALS['_SERVER']['REQUEST_METHOD']);
	}
}