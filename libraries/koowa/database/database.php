<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_Database
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Database Namespace class
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Database
 */
class KDatabase
{
	/**
	 * Database operations
	 */
	const OPERATION_SELECT = 1;
	const OPERATION_INSERT = 2;
	const OPERATION_UPDATE = 4;
	const OPERATION_DELETE = 8;

	/**
	 * Database result mode
	 */
	const RESULT_STORE = 0;
	const RESULT_USE   = 1;
	
	/**
	 * Database fetch mode
	 */
	const FETCH_ROWSET  = 0;
	const FETCH_ROW     = 1;
	const FETCH_FIELD   = 2;
	
	/**
	 * Row states
	 */
	const STATUS_DELETED  = 'deleted';
    const STATUS_INSERTED = 'inserted';
    const STATUS_UPDATED  = 'updated';
	
}