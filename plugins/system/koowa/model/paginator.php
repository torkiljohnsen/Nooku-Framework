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
 * Pagination Model
 * 
 * To use, set the following states
 * 
 * total:  		Total number of items
 * limit:  		Number of items per page
 * offset: 		The starting item for the current page
 * display: 	Number of links to generate before and after the current offset,
 * 				or 0 for all (Optional)
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package     Koowa_Model
 */
class KModelPaginator extends KModelState
{
	/**
	 * Constructor
     *
     * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(array $options = array())
	{
		parent::__construct($options);
		
		// Set the state
		$this->insert('total'    , 'int')
			 ->insert('limit'    , 'int', 20)
			 ->insert('offset'   , 'int', 0)
			 ->insert('count'    , 'int')
			 ->insert('current'  , 'int')
			 ->insert('display'  , 'int', 4);
	}
	
	/**
     * Set the state data
     *
     * @param   array|object	An associative array of state data by name
     * @return  KModelState
     */
    public function setData(array $data)
    {
		parent::setData($data);
		
		$total	= (int) $this->total;
		$limit	= (int) max($this->limit, 1);
		$offset	= (int) max($this->offset, 0);

		if($limit > $total) {
			$offset = 0;
		}
		
		if(!$limit) 
		{
			$offset = 0;
			$limit  =  $total;
		}

		$count	= (int) ceil($total / $limit);

    	if($offset > $total) {
			$offset = ($count-1) * $limit;
		}

		$current = (int) floor($offset / $limit) +1;

		$this->total = $total;
		$this->limit = $limit;
		$this->offset = $offset;
		$this->count = $count;
		$this->current = $current;
		
		return $this;
    }
	
    /**
	 * Get a list of pages
	 *
	 * @return  array 	Returns and array of pages information
	 */
    public function getList()
    {
    	$elements  = array();
    	$prototype = new KObject();
    	$current   = ($this->current - 1) * $this->limit;

    	// First
    	$page    = 1;
    	$offset  = 0;
    	$active  = $offset != $this->offset;
    	$props   = array('page' => 1, 'offset' => $offset, 'limit' => $this->limit, 'current' => false, 'active' => $active );
    	$element = clone $prototype;
    	$elements['first'] = $element->set($props);

    	// Previous
    	$offset  = max(0, ($this->current - 2) * $this->limit);
    	$active  = $offset != $this->offset;
    	$props   = array('page' => $this->current - 1, 'offset' => $offset, 'limit' => $this->limit, 'current' => false, 'active' => $active);
    	$element = clone $prototype;
    	$elements['previous'] = $element->set($props);

		// Pages
		foreach($this->_getOffsets() as $page => $offset)
		{
			$current = $offset == $this->offset;
			$props = array('page' => $page, 'offset' => $offset, 'limit' => $this->limit, 'current' => $current, 'active' => !$current);
    		$element 	= clone $prototype;
    		$elements['pages'][] = $element->set($props);
		}

		// Next
    	$offset  = min(($this->count-1) * $this->limit, ($this->current) * $this->limit);
 		$active  = $offset != $this->offset;
    	$props   = array('page' => $this->current + 1, 'offset' => $offset, 'limit' => $this->limit, 'current' => false, 'active' => $active);
    	$element = clone $prototype;
    	$elements['next'] = $element->set($props);

    	// Last
    	$offset  = ($this->count - 1) * $this->limit;
    	$active  = $offset != $this->offset;
    	$props   = array('page' => $this->count, 'offset' => $offset, 'limit' => $this->limit, 'current' => false, 'active' => $active);
    	$element = clone $prototype;
    	$elements['last'] = $element->set($props);

    	return $elements;
    }
    
 	/**
     * Get the offset for each page, optionally with a range
     *
     * @return 	array	Page number => offset
     */
	protected function _getOffsets()
    {
   	 	if($display = $this->display)
    	{
    		$start	= (int) max($this->current - $display, 1);
    		$start	= min($this->count, $start);
    		$stop	= (int) min($this->current + $display, $this->count);
    	}
    	else // show all pages
    	{
    		$start = 1;
    		$stop = $this->count;
    	}

    	$result = array();
    	foreach(range($start, $stop) as $pagenumber) {
    		$result[$pagenumber] = 	($pagenumber-1) * $this->limit;
    	}

    	return $result;
    }
}