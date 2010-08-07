<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Query
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Database Select Class for database select statement generation
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Query
 */
class KDatabaseQuery extends KObject
{
	/**
	 * Count operation
	 *
	 * @var boolean
	 */
	public $count	  = false;
	
	/**
	 * Distinct operation
	 *
	 * @var boolean
	 */
	public $distinct  = false;

	/**
	 * The columns
	 *
	 * @var array
	 */
	public $columns = array();

	/**
	 * The from element
	 *
	 * @var array
	 */
	public $from = array();

	/**
	 * The join element
	 *
	 * @var array
	 */
	public $join = array();

	/**
	 * The where element
	 *
	 * @var array
	 */
	public $where = array();

	/**
	 * The group element
	 *
	 * @var array
	 */
	public $group = array();

	/**
	 * The having element
	 *
	 * @var array
	 */
	public $having = array();

	/**
	 * The order element
	 *
	 * @var string
	 */
	public $order = array();

	/**
	 * The limit element
	 *
	 * @var integer
	 */
	public $limit = 0;

	/**
	 * The limit offset element
	 *
	 * @var integer
	 */
	public $offset = 0;
	
	/**
     * Data to bind into the query as key => value pairs.
     * 
     * @var array
     */
    protected $_bind = array();

	/**
	 * Database connector
	 *
	 * @var		object
	 */
	protected $_adapter;

	/**
	 * Object constructor
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct( KConfig $config = null)
	{
        //If no config is passed create it
		if(!isset($config)) $config = new KConfig();

		parent::__construct($config);

		//set the model adapter
		$this->_adapter = $config->adapter;
	}


    /**
     * Initializes the options for the object
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
            'adapter' => KFactory::get('lib.koowa.database')
        ));

        parent::_initialize($config);
    }

    /**
     * Gets the database adapter for this particular KDatabaseQuery object.
     *
     * @return KDatabaseAdapterInterface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }


	/**
	 * Built a select query
	 *
	 * @param	array|string	A string or an array of column names
	 * @return 	KDatabaseQuery
	 */
	public function select( $columns = '*')
	{
		if(func_num_args() > 1) {
			$columns = func_get_args();
		} else {
			settype($columns, 'array');
		}

		$this->columns = array_unique( array_merge( $this->columns, $columns ) );
		return $this;
	}

	/**
	 * Built a count query
	 *
	 * @return KDatabaseQuery
	 */
	public function count()
	{
		$this->count   = true;
		$this->columns = null;
		return $this;
	}

	/**
	 * Make the query distinct
	 *
	 * @return KDatabaseQuery
	 */
	public function distinct()
	{
		$this->distinct = true;
		return $this;
	}

	/**
	 * Built the from clause of the query
	 *
	 * @param	array|string	A string or array of table names
	 * @return 	KDatabaseQuery
	 */
	public function from( $tables )
	{
		if(func_num_args() > 1) {
			$tables = func_get_args();
		} else {
			settype($tables, 'array');
		}
	
		//Prepent the table prefix
		array_walk($tables, array($this, '_prefix'));

		$this->from = array_unique( array_merge( $this->from, $tables ) );
		return $this;
	}

	/**
     * Built the join clause of the query
     *
     * @param string 		The type of join; empty for a plain JOIN, or "LEFT", "INNER", etc.
     * @param string 		The table name to join to.
     * @param string|array 	Join on this condition.
     * @return KDatabaseQuery
     */
    public function join($type, $table, $condition)
    {
		settype($condition, 'array'); //force to an array

		$this->_prefix($table); //add a prefix to the table
	
    	$this->join[] = array(
        	'type'  	=> strtoupper($type),
        	'table' 	=> $table,
        	'condition' => $condition,
        );

        return $this;
    }

	/**
	 * Built the where clause of the query
	 *
	 * @param   string 			The name of the property the constraint applies too, or a SQL function or statement
	 * @param	string  		The comparison used for the constraint
	 * @param	string|array	The value compared to the property value using the constraint
	 * @param	string			The where condition, defaults to 'AND'
	 * @return 	KDatabaseQuery
	 */
	public function where( $property, $constraint = null, $value = null, $condition = 'AND' )
	{
		if(!empty($property)) 
		{
			$where = array();
			$where['property'] = $property;

			if(isset($constraint) && isset($value))
			{
				$constraint	= strtoupper($constraint);
				$condition	= strtoupper($condition);
			
        		$where['constraint'] = $constraint;
        		$where['value']      = $value;
			}
		
			$where['condition']  = count($this->where) ? $condition : '';

			//Make sure we don't store the same where clauses twice
			$signature = md5($property.$where.$value);
        	$this->where[$signature] = $where;
		}
	
        return $this;
	}

	/**
	 * Built the group clause of the query
	 *
	 * @param	array|string	A string or array of ordering columns
	 * @return 	KDatabaseQuery
	 */
	public function group( $columns )
	{
		if(func_num_args() > 1) {
			$columns = func_get_args();
		} else {
			settype($columns, 'array');
		}
			
		$this->group = array_unique( array_merge( $this->group, $columns));
		return $this;
	}

	/**
	 * Built the having clause of the query
	 *
	 * @param	array|string	A string or array of ordering columns
	 * @return 	KDatabaseQuery
	 */
	public function having( $columns )
	{
		if(func_num_args() > 1) {
			$columns = func_get_args();
		} else {
			settype($columns, 'array');
		}

		$this->having = array_unique( array_merge( $this->having, $columns ));
		return $this;
	}

	/**
	 * Build the order clause of the query
	 *
	 * @param	array|string  A string or array of ordering columns
	 * @param	string		  Either DESC or ASC
	 * @return 	KDatabaseQuery
	 */
	public function order( $columns, $direction = 'ASC' )
	{
		settype($columns, 'array'); //force to an array

		foreach($columns as $column)
		{
			$this->order[] = array(
        		'column'  	=> $column,
        		'direction' => $direction
        	);
		}

		return $this;
	}

	/**
	 * Built the limit element of the query
	 *
	 * @param 	integer Number of items to fetch.
	 * @param 	integer Offset to start fetching at.
	 * @return 	KDatabaseQuery
	 */
	public function limit( $limit, $offset = 0 )
	{
		$this->limit  = $limit;
		$this->offset = $offset;
		return $this;
	}
	
	/**
     * Adds data to bind into the query.
     * 
     * @param 	mixed 	The replacement key in the query.  If this is an
     * 					array or object, the $val parameter is ignored, 
     * 					and all the key-value pairs in the array (or all 
     *   				properties of the object) are added to the bind.
     * @param 	mixed 	The value to use for the replacement key.
     * @return 	KDatabaseQuery
     */
    public function bind($key, $val = null)
    {
        if (is_array($key)) {
            $this->_bind = array_merge($this->_bind, $key);
        } elseif (is_object($key)) {
            $this->_bind = array_merge((array) $this->_bind, $key);
        } else {
            $this->_bind[$key] = $val;
        }
        
        return $this;
    }
    
	/*
	 * Callback for array_walk to prefix elements of array with given prefix
	 *
	 * @param string The data to be prefixed
	 */
	protected function _prefix(&$data)
	{
		// Prepend the table modifier
		$prefix = $this->_adapter->getTablePrefix();
		$data = $prefix.$data;
	}

	/**
	 * Render the query to a string
	 *
	 * @return	string	The completed query
	 */
	public function __toString()
	{
		$query = '';
		if(!empty($this->columns) || $this->count)
		{	
			$query = 'SELECT';
			
			if($this->distinct) {
				$query .= ' DISTINCT';
			}
		
			if($this->count) {
				$query .= ' COUNT(*)';
			}
		}

		$query .= PHP_EOL;
	
		if (!empty($this->columns)) 
		{
			$columns = array();
			foreach($this->columns as $column) {
				$columns[] = $this->_adapter->quoteIdentifier($column);
			} 
			
			$query .= ' '.implode(' , ', $columns).PHP_EOL;
		}

		if (!empty($this->from)) 
		{
			$tables = array();
			foreach($this->from as $table) {
				$tables[] = $this->_adapter->quoteIdentifier($table);
			} 
			
			$query .= ' FROM '.implode(' , ', $tables).PHP_EOL;
		}

		if (!empty($this->join))
		{
			$joins = array();
            foreach ($this->join as $join)
            {
            	$tmp = '';
    
            	if (! empty($join['type'])) {
                    $tmp .= $join['type'] . ' ';
                }

                $tmp .= 'JOIN ' . $this->_adapter->quoteIdentifier($join['table']);
                $tmp .= ' ON ' . implode(' AND ', $this->_adapter->quoteIdentifier($join['condition']));

                $joins[] = $tmp;
            }

            $query .= implode(PHP_EOL, $joins) .PHP_EOL;
		}

		if (!empty($this->where)) 
		{
			$query .= ' WHERE';
			
			foreach($this->where as $where)
			{
				if(isset($where['condition'])) {
					$query .= ' '.$where['condition'];		
				}
				
				$query .= ' '. $this->_adapter->quoteIdentifier($where['property']);
				
				if(isset($where['constraint'])) 
				{
					$value = $where['value'];
					
					//Only quote if the value is not a named placeholder
					if(substr($value, 0, 1) != ':') {
						$value = $this->_adapter->quoteValue($value);
					} 
					
					if(in_array($where['constraint'], array('IN', 'NOT IN'))) {
        				$value = ' ( '.$value. ' ) ';
        			}
					
					$query .= ' '.$where['constraint'].' '.$value;
				}
			}
			
			$query .= PHP_EOL;
		}

		if (!empty($this->group)) 
		{
			$columns = array();
			foreach($this->group as $column) {
				$columns[] = $this->_adapter->quoteIdentifier($column);
			} 
			
			$query .= ' GROUP BY '.implode(' , ', $columns).PHP_EOL;
		}

		if (!empty($this->having)) 
		{
			$columns = array();
			foreach($this->having as $column) {
				$columns[] = $this->_adapter->quoteIdentifier($column);
			} 
			
			$query .= ' HAVING '.implode(' , ', $columns).PHP_EOL;
		}

		if (!empty($this->order) )
		{
			$query .= 'ORDER BY ';

			$list = array();
            foreach ($this->order as $order) {
            	$list[] = $this->_adapter->quoteIdentifier($order['column']).' '.$order['direction'];
            }

            $query .= implode(' , ', $list) . PHP_EOL;
		}

		if (!empty($this->limit)) {
			$query .= ' LIMIT '.$this->offset.' , '.$this->limit.PHP_EOL;
		}
		
		//Perform named binding
		preg_match_all("/:([a-zA-Z_][a-zA-Z0-9_]*)/m", $query, $matches);	
		foreach($matches[1] as $key => $match)
		{
			// only attempt to bind if the data key exists.
            // this allows for nulls and empty strings.
            if (! array_key_exists($match, $this->_bind)) {
                continue;
            }
			
            $value = $this->_adapter->quoteValue($this->_bind[$match]);
			$query = str_replace($matches[0][$key], $value, $query);
		}

		return $query;
	}
}