<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Row
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Database Row Interface
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Row
 */
interface KDatabaseRowInterface extends KObjectIdentifiable
{
	/**
     * Returns the status of this row.
     *
     * @return string The status value.
     */
    public function getStatus();

	/**
     * Load the row from the database.
     *
     * @return KDatabaseRowAbstract
     */
	public function load();
    
    /**
     * Saves the row to the database.
     *
     * This performs an intelligent insert/update and reloads the properties
     * with fresh data from the table on success.
     *
     * @return KDatabaseRowInterface
     */
    public function save();

	/**
     * Deletes the row form the database.
     *
     * @return KDatabaseRowInterface
     */
    public function delete();
    
	/**
     * Count the rows in the database based on the data in the row
     *
     * @return KDatabaseRowAbstract
     */
	public function count();

	/**
     * Resets to the default properties
     *
     * @return KDatabaseRowInterface
     */
    public function reset();

   /**
 	* Returns an associative array of the raw data
  	*
  	* @param   boolean 	If TRUE, only return the modified data. Default FALSE
  	* @return  array
  	*/
 	public function getData($modified = false);

  	/**
  	 * Set the row data
  	 *
  	 * @param   mixed 	Either and associative array, an object or a KDatabaseRow
  	 * @param   boolean If TRUE, update the modified information for each column being set.
  	 * 					Default TRUE
 	 * @return 	KDatabaseRowInterface
  	 */
  	 public function setData( $data, $modified = true );

	/**
	 * Get a list of columns that have been modified
	 *
	 * @return array	An array of column names that have been modified
	 */
	public function getModified();

	/**
     * Checks if the row is new or not
     *
     * @return bool
     */
    public function isNew();
}