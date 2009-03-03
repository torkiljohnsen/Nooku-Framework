<?php
/**
 * @version 	$Id:factory.php 46 2008-03-01 18:39:32Z mjaz $
 * @category	Koowa
 * @package		Koowa_Security
 * @subpackage	Token
 * @copyright	Copyright (C) 2007 - 2009 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Utility class to work with tokens in forms, to prevent CSRF attacks
 *
 * @example:
 * In a form:
 * <code>
 * <?php echo KSecurityToken::render();?>
 * </code>
 * Where the form is submitted:
 * <code>
 * <?php KSecurityToken::check() or die('Invalid Token'); ?>
 * </code>
 * 
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Security
 * @subpackage	Token
 */
class KSecurityToken
{
	/**
	 * Token
	 *
	 * @var	string
	 */
	protected static $_token;
	
    /**
     * Generate new token and store it in the session
     * 
     * @param	bool	Reuse from session (defaults to false, useful for ajax forms)
     * @return	string	Token
     */
    static public function get($reuse = false)
    {
        return  JUtility::getToken($forceNew);
    }

    /**
     * Render the hidden input field with the token
     *
     * @param	bool	Reuse from session (defaults to false, useful for ajax forms)
     * @return	string	Html hidden input field
     */
    static public function render($reuse = false)
    {
    	return '<input type="hidden" name="_token" value="'.self::get($reuse).'" />';
    }

    /**
     * Check if a valid token was submitted
     *
     * @param 	boolean	Maximum age, defaults to 600 seconds
     * @return	boolean	True on success
     */
    static public function check($max_age = 600)
    {
    	// Using getVar instead of getString, because if the request is not a string, 
		// we consider it a hacking attempt
        $req		= JRequest::getVar('_token', null, 'post', 'alnum');
        $token		= self::get();
        
        return (self::isMd5($req) && $req===$token);
    }
    
 	/**
     * Check if a string is a valid md5 (32 digit hexadecimal number)
     * 
     * @todo	Move to a separate validation class?
     * 
     * @param 	mixed	Variable to be tested
     * @return 	bool
     */
    static public function isMd5($var)
    {
    	$pattern = '/^[0-9a-f]{32}$/';
    	return (is_string($var) && preg_match($pattern, $var) == 1);
    }
}