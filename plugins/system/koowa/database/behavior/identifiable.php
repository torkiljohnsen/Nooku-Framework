<?php
/**
 * @version 	$Id$
 * @category	Koowa
 * @package		Koowa_Database
 * @subpackage 	Behavior
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Database Hittable Behavior
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage 	Behavior
 */
class KDatabaseBehaviorIdentifiable extends KDatabaseBehaviorAbstract
{
	/**
	 * File handle
	 *
	 * @var resource
	 */
	protected $_urand;
	
	/**
	 * Get the methods that are available for mixin based
	 * 
	 * This function conditionaly mixes of the behavior. Only if the mixer 
	 * has a 'uuid' property the behavior will be mixed in.
	 * 
	 * @param object The mixer requesting the mixable methods. 
	 * @return array An array of methods
	 */
	public function getMixableMethods(KObject $mixer = null)
	{
		$methods = array();
		
		if(isset($mixer->uuid)) {
			$methods = parent::getMixableMethods($mixer);
		}
		
		//Lazy load the random number
        $this->_urand = @fopen ( '/dev/urandom', 'rb' );
    
		return $methods;
	}
	
	/**
	 * Set created information
	 * 	
	 * Requires an created_on and created_by field in the table schema
	 * 
	 * @return void
	 */
	protected function _beforeTableInsert(KCommandContext $context)
	{
		$row = $context['data']; //get the row data being inserted
		
		if(isset($row->uuid)) {
			$row->uuid  = $this->_uuid();
		}
	}
	
	/**
     * Generates a Universally Unique IDentifier, version 4.
     *
     * This function generates a truly random UUID.
     *
     * @see http://tools.ietf.org/html/rfc4122#section-4.4
     * @see http://en.wikipedia.org/wiki/UUID
     * @return string A UUID, made up of 32 hex digits and 4 hyphens.
     */
    protected function _uuid() 
    {
        $pr_bits = false;
        if (is_a ( $this, 'uuid' )) 
        {
            if (is_resource ( $this->_urand )) {
                $pr_bits .= @fread ( $this->_urand, 16 );
            }
        }
        
        if (! $pr_bits) 
        {
            $fp = @fopen ( '/dev/urandom', 'rb' );
            if ($fp !== false) 
            {
                $pr_bits .= @fread ( $fp, 16 );
                @fclose ( $fp );
            } 
            else 
            {
                // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
                $pr_bits = "";
                for($cnt = 0; $cnt < 16; $cnt ++) {
                    $pr_bits .= chr ( mt_rand ( 0, 255 ) );
                }
            }
        }
        
        $time_low = bin2hex ( substr ( $pr_bits, 0, 4 ) );
        $time_mid = bin2hex ( substr ( $pr_bits, 4, 2 ) );
        $time_hi_and_version = bin2hex ( substr ( $pr_bits, 6, 2 ) );
        $clock_seq_hi_and_reserved = bin2hex ( substr ( $pr_bits, 8, 2 ) );
        $node = bin2hex ( substr ( $pr_bits, 10, 6 ) );
       
        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec ( $time_hi_and_version );
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;
       
        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec ( $clock_seq_hi_and_reserved );
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;
       
        return sprintf ( '%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node );
    }
	
}