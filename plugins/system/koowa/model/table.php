 <?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Table Model Class
 * 
 * Provides interaction with a database table
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Model
 */
class KModelTable extends KModelAbstract
{
	/**
	 * Table object or identifier (APP::com.COMPONENT.table.TABLENAME)
	 *
	 * @var	string|object
	 */
	protected $_table;
	
	/**
	 * Constructor
     *
     * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		if(!empty($config->table)) {
			$this->setTable($config->table);
		}

		// Set the static states
		$this->_state
			->insert('limit'    , 'int', 0)
			->insert('offset'   , 'int', 0)
			->insert('order'    , 'cmd')
			->insert('direction', 'word', 'asc')
			->insert('search'   , 'string');
			
		
		//Get the table object
		$table = KFactory::get($this->getTable());
		
		//Set the table behaviors
		$table->addBehaviors($config->table_behaviors);
			
		// Set the dynamic states based on the unique table keys
      	foreach($table->getUniqueColumns() as $key => $data) {
      		$this->_state->insert($key, $data->filter, $data->default, true);
		}	
	}
	
	/**
	 * Initializes the config for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'table'   			=> null,
			'table_behaviors'	=> array()
       	));
       	
       	parent::_initialize($config);
    }
    
	/**
     * Set the model state properties
     * 
     * This function overloads the KTableAbstract::set() function and only acts on state properties.
     *
     * @param   string|array|object	The name of the property, an associative array or an object
     * @param   mixed  				The value of the property
     * @return	KModelTable
     */
    public function set( $property, $value = null )
    {
    	// If limit has been changed, adjust offset accordingly
    	if($property == 'limit') {
    		$this->_state->offset = $value != 0 ? (floor($this->_state->offset / $value) * $value) : 0;
    	}
    	
    	parent::set($property, $value);
    
    	return $this;
    }
    
	/**
	 * Get the identifier for the table with the same name
	 *
	 * @return	KIdentifierInterface
	 */
	final public function getTable()
	{
		if(!$this->_table)
		{
			$identifier 		= clone $this->_identifier;
			$identifier->name	= KInflector::tableize($identifier->name);
			$identifier->path	= array('table');
		
			$this->_table = $identifier;
		}
       	
		return $this->_table;
	}

	/**
	 * Method to set a table object attached to the model
	 *
	 * @param	mixed	An object that implements KObjectIdentifiable, an object that 
	 *                  implements KIndentifierInterface or valid identifier string
	 * @throws	KDatabaseRowsetException	If the identifier is not a table identifier
	 * @return	KModelTable
	 */
	public function setTable($table)
	{
		$identifier = KFactory::identify($table);

		if($identifier->path[0] != 'table') {
			throw new KModelException('Identifier: '.$identifier.' is not a table identifier');
		}
		
		$this->_table = $identifier;
		return $this;
	}

    /**
     * Method to get a item object which represents a table row 
     * 
     * If the model state is unique a row is fetched from the database based on the state. 
     * If not, an empty row is be returned instead.
     * 
     * @return KDatabaseRow
     */
    public function getItem()
    {
        if (!isset($this->_item))
        {
        	$table  = KFactory::get($this->getTable());
        	$query  = null;
        	
        	if($this->_state->isUnique())
        	{
       			$query = $table->getDatabase()->getQuery();
        		
        		$this->_buildQueryColumns($query);
        		$this->_buildQueryFrom($query);
        		$this->_buildQueryJoins($query);
        		$this->_buildQueryWhere($query);
        		$this->_buildQueryGroup($query);
        		$this->_buildQueryHaving($query);	
         	}
         	
         	$this->_item = $table->select($query, KDatabase::FETCH_ROW);
        }

        return $this->_item;
    }

    /**
     * Get a list of items which represnts a  table rowset
     *
     * @return KDatabaseRowset
     */
    public function getList()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_list))
        {
        	$table = KFactory::get($this->getTable());
        	$query = $table->getDatabase()->getQuery();
        	
       	 	$this->_buildQueryColumns($query);
        	$this->_buildQueryFrom($query);
        	$this->_buildQueryJoins($query);
        	$this->_buildQueryWhere($query);
        	$this->_buildQueryGroup($query);
        	$this->_buildQueryHaving($query);
        	$this->_buildQueryOrder($query);
        	$this->_buildQueryLimit($query);

        	$this->_list = $table->select($query, KDatabase::FETCH_ROWSET);	
        }

        return $this->_list;
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
            $table = KFactory::get($this->getTable());
            $query = $table->getDatabase()->getQuery();

        	$this->_buildQueryFrom($query);
        	$this->_buildQueryJoins($query);
        	$this->_buildQueryWhere($query);
        	
        	$total = $table->count($query);
        	$limit  = $this->_state->limit;
    		$offset = $this->_state->offset;
    	
    		//If the offset is higher than the total recalculate the offset 
    		if($limit !== 0 && $offset !== 0)
    		{
    			if($total !== 0 && $offset >= $total) { 
    				$this->_state->offset = floor(($total-1) / $limit) * $limit;
    			}
    		}
			
        	$this->_total = $total;
        }
        
        return $this->_total;
    }
    

	public function getState()
	{
		return $this->_state;
	}
    
    /**
     * Builds SELECT columns list for the query
     */
    protected function _buildQueryColumns(KDatabaseQuery $query)
    {
		$query->select(array('tbl.*'));
    }

	/**
     * Builds FROM tables list for the query
     */
    protected function _buildQueryFrom(KDatabaseQuery $query)
    {
      	$name = KFactory::get($this->getTable())->getName();
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
    	//Get only the unique states
    	$states = $this->_state->getData(true);
    	
    	if(!empty($states))
    	{
    		$states = KFactory::get($this->getTable())->map($states); 
    		foreach($states as $key => $value) {
         		$query->where('tbl.'.$key, 'IN', $value);
        	}
    	}
    }
    
  	/**
     * Builds a GROUP BY clause for the query
     */
    protected function _buildQueryGroup(KDatabaseQuery $query)
    {
    	
    }

    /**
     * Builds a HAVING clause for the query
     */
    protected function _buildQueryHaving(KDatabaseQuery $query)
    {
    	
    }

    /**
     * Builds a generic ORDER BY clasue based on the model's state
     */
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
    	$order      = $this->_state->order;
       	$direction  = strtoupper($this->_state->direction);

    	if($order) {
    		$query->order($order, $direction);
    	}

		if(in_array('ordering', KFactory::get($this->getTable())->getColumns())) {
    		$query->order('ordering', 'ASC');
    	}
    }

    /**
     * Builds LIMIT clause for the query
     */
    protected function _buildQueryLimit(KDatabaseQuery $query)
    {
		$query->limit($this->_state->limit, $this->_state->offset);
    }
}