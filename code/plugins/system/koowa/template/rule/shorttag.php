<?php
/**
* @version      $Id$
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Rule
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.koowa.org
*/

/**
 * Template rule for short_open_tags support
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage	Rule 
 */
class KTemplateRuleShorttag extends KObject implements KTemplateRuleInterface
{
	/**
	 * Convert <?= ?> to long-form <?php echo ?> when needed
	 *
	 * @param string $text
	 */
	public function parse(&$text)
	{
        if (ini_get('short_open_tag')) {
        	return;
        }
   
        // convert "<?=" to "<?php echo"
        $find = '/\<\?\s*=\s*(.*?)/';
        $replace = "<?php echo \$1";
        $text = preg_replace($find, $replace, $text);

        // convert "<?" to "<?php"
        $find = '/\<\?(?:php)?\s*(.*?)/';
        $replace = "<?php \$1";
        $text = preg_replace($find, $replace, $text);
	}
}