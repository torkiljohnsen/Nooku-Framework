<?php
/**
* @version      $Id$
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Rule
* @copyright    Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.koowa.org
*/

/**
 * Template rule for short_open_tags support
 *
 * @author		Mathias Verraes <mathias@koowa.org>
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
        
        /**
         * We could also convert <%= like the real T_OPEN_TAG_WITH_ECHO
         * but that's not necessary.
         * 
         * It might be nice to also convert PHP code blocks <? ?> but 
         * let's quit while we're ahead.  It's probably better to keep 
         * the <?php for larger code blocks but that's your choice.  If
         * you do go for it, explicitly check for <?xml as this will
         * probably be the biggest headache.
         */
                
        // convert "<?=" to "<?php echo"
        $find = '/\<\?\=\s?(.*?)/';
        $replace = "<?php echo \$1";
        $text = preg_replace($find, $replace, $text);
        
        // convert "<?" to "<?php"
        $find = '/\<\?\s(.*?)/';
        $replace = "<?php \$1";
        $text = preg_replace($find, $replace, $text);
	}
}