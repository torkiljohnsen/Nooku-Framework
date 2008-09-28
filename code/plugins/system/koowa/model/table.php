<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract Model Class
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Model
 */
class KModelTable extends KModelAbstract
{
	/**
	 * Database Connector
	 *
	 * @var object
	 */
	protected $_db;

	/**
	 * Constructor
     *
     * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(array $options = array())
	{
		//set the model dbo
		$this->_db = isset($options['dbo']) ? $options['dbo'] : KFactory::get('lib.joomla.database');
		
		parent::__construct($options);
	}

	/**
	 * Method to get the database connector object
	 *
	 * @return	object KDatabase connector object
	 */
	public function getDBO()
	{
		return $this->_db;
	}

	/**
	 * Method to set the database connector object
	 *
	 * @param	object	$db	A KDatabase based object
	 * @return	void
	 */
	public function setDBO($db)
	{
		$this->_db = $db;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * This function overrides the default model behavior and sets the table
	 * prefix based on the model prefix.
	 *
	 * @param	string	$table 			The name of the table. Optional, defaults to the class name.
	 * @param	string	$component		The name of the component. Optional.
	 * @param	string	$application	The name of the application. Optional.
	 * @param	array	$opations		Options array for view. Optional.
	 * @return	object	The table
	 */
	public function getTable($table = '', $component = '', $application = '', array $options = array())
	{
		if (empty($table)) {
			$table = KInflector::tableize($this->getClassName('suffix'));
		}
	
		if ( empty( $component ) ) {
			$component = $this->getClassName('prefix');
		}
		
		if (empty( $application) )  {
			$application = KFactory::get('lib.joomla.application')->getName();
		}

		//Make sure we are returning a DBO object
		if (!array_key_exists('dbo', $options))  {
			$options['dbo'] = $this->getDBO();
		}
		
		return KFactory::get($application.'::com.'.$component.'.table.'.$table, $options);
	}

    /**
     * Method to get a item object which represents a table row
     *
     * @return  object KDatabaseRow
     */
    public function getItem()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_item)) {
            $this->_item = $this->getTable()->find((int)$this->getState('id'));
        }

        return parent::getItem();
    }

    /**
     * Get a list of items which represnts a  table rowset
     *
     * @return  object KDatabaseRowset
     */
    public function getList()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_list)) 
        {
        	$query = $this->_buildQuery();
        	$this->_list = $this->getTable()->fetchAll(
        		$query->__toString(), 
        		$this->getState('offset'), 
        		$this->getState('limit')
        	);
        }

        return parent::getList();
    }

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function getTotal()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_total)) 
        {
            $query = $this->_buildCountQuery();
        	$this->_db->select( $query );
			$this->_total = $this->_db->loadResult();
        }

        return parent::getTotal();
    }

    /**
     * Get a list of filters
     *
     * @return  array
     */
    public function getFilters()
    {
       $filters = parent::getFilters();
    	
       $filters['order']       = $this->getState('order');
       $filters['direction']   = $this->getState('direction');
       $filters['filter']      = $this->getState('filter');

        return $filters;
    }
    
    /**
     * Builds a generic SELECT query
     *
     * @return  string  SELECT query
     */
    protected function _buildQuery()
    {
    	$query = $this->_db->getQuery();
    	$key   = $this->getTable()->getPrimaryKey();
        $query->select(array('tbl.*',  'tbl.'.$key.' AS id'));
        
        $this->_buildQueryFields($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        $this->_buildQueryOrder($query);
               
		return $query;
    }
    
 	/**
     * Builds a generic SELECT COUNT(*) query
     */
    protected function _buildCountQuery()
    {
        $query = $this->_db->getQuery();
        $query->count();
       
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        
        return $query;
    }
    
    /**
     * Builds SELECT fields list for the query
     */
    protected function _buildQueryFields(KDatabaseQuery $query)
    {
    	
    } 
    
	/**
     * Builds FROM tables list for the query
     */
    protected function _buildQueryFrom(KDatabaseQuery $query)
    {
    	$name = $this->getTable()->getTableName();
    	$query->from($name.' AS tbl');
    }

    /**
     * Builds LEFT JOINS clauses for the query
     */
    protected function _buildQueryJoins(KDatabaseQuery $query)
    {
        
    }

    /**
     * Builds a WHERE clause for the query
     */
    protected function _buildQueryWhere(KDatabaseQuery $query)
    {
       
    }

    /**
     * Builds a generic ORDER BY clasue based on the model's state
     */
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
       	$order      = $this->getState('order');
       	$direction  = strtoupper($this->getState('direction'));
    	if($order) {
    		$query->order($order, $direction);
    	}
    }
    
 	/**
     * Get the default states
     */
    public function getDefaultState()
    {
		$app 	= KFactory::get('lib.joomla.application');
    	
    	//Get the namespace
    	$ns  = $this->getClassName('prefix').'.'.$this->getClassName('suffix');
        
        $state = parent::getDefaultState();
        $state['order']      = $app->getUserStateFromRequest($ns.'filter_order', 'filter_order', '', 'cmd');
        $state['direction']  = $app->getUserStateFromRequest($ns.'filter_direction', 'filter_direction', 'ASC', 'word');
        $state['filter']     = $app->getUserStateFromRequest($ns.'filter', 'filter', '', 'string');
        $state['id']         = KInput::get('id', 'request', 'raw'); //TODO fix this filter
         
  		return $state;
    }
}