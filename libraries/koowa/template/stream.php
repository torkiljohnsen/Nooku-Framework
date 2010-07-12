<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Template
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

 /**
  * Abstract stream wrapper to convert markup of mostly-PHP templates into PHP prior to include().
  *
  * Based in large part on the example at
  * http://www.php.net/manual/en/function.stream-wrapper-register.php
  * 
  * @author		Johan Janssens <johan@koowa.org>
  * @category	Koowa
  * @package	Koowa_Template
  */
class KTemplateStream
{
    /**
     * Current stream position.
     *
     * @var int
     */
    private $_pos = 0;

    /**
     * Data for streaming.
     *
     * @var string
     */
    private $_data;

    /**
     * Stream stats.
     *
     * @var array
     */
    private $_stat;
    
 	/**
     * Register the stream wrapper 
     * 
     * Function prevents from registering the wrapper twice
     */
   	public static function register()
 	{       
     	if (!in_array('tmpl', stream_get_wrappers())) {
         	stream_wrapper_register('tmpl', __CLASS__);
       	}
    } 

 	/**
     * Opens the template file and converts markup.
     * 
     * This function filters the data from the stream by pushing it through the template's 
     * read filter chain. The template object to use for filtering is looked up based on the 
     * stream's path. 
     * 
     * @param string 	The stream path
     * @return boolean
     */
    public function stream_open($path) 
	{   
        //Get the view script source
        $path = str_replace('tmpl://', '', $path);
        $this->_data = file_get_contents($path);
        
        //If reading the file failed, update our local stat store
        //to reflect the real stat of the file, then return on failure
        if ($this->_data === false) 
        {
            $this->_stat = stat($path);
            return false;
        }
         
  		//Get the template object from the template repository and filter 
  		//the data before reading
     	$this->_data = 	KFactory::get('lib.koowa.template.registry')
     						->get($path)
     						->filter($this->_data, KTemplateFilter::MODE_READ);
     	
       // file_get_contents() won't update PHP's stat cache, so performing
       // another stat() on it will hit the filesystem again. Since the file
       // has been successfully read, avoid this and just fake the stat
       // so include() is happy.
        $this->_stat = array('mode' => 0100777, 'size' => strlen($this->_data));

        return true;
    }
   
    /**
     * Reads from the stream.
     * 
     * @return string
     */
    public function stream_read($count) 
	{
        $ret = substr($this->_data, $this->_pos, $count);
        $this->_pos += strlen($ret);
        return $ret;
    }

    /**
     * Tells the current position in the stream.
     * 
     * @return int
     */
    public function stream_tell() 
    {
        return $this->_pos;
    }
  
    /**
     * Tells if we are at the end of the stream.
     * 
     * @return bool
     */
    public function stream_eof() 
    {
        return $this->_pos >= strlen($this->_data);
    }
    
    /**
     * Stream statistics.
     * 
     * @return array
     */
    public function stream_stat() 
    {
        return $this->_stat;
    }
    
    /**
     * Flushes the output
     * 
     * @return boolean
     */
    public function stream_flush()
    {
    	return false;
    }
    
    
    /**
     * Close the stream
     * 
     * @return void
     */
	public function stream_close()
    {
    	
    }

    /**
     * Seek to a specific point in the stream.
     * 
     * @return bool
     */
    public function stream_seek($offset, $whence) 
	{
        switch ($whence) 
		{
            case SEEK_SET:
            	
                if ($offset < strlen($this->_data) && $offset >= 0) {
                $this->_pos = $offset;
                    return true;
                } 
                else return false;
                break;

            case SEEK_CUR:
                
            	if ($offset >= 0) 
                {
                    $this->_pos += $offset;
                    return true;
                } 
                else return false;
                break;

            case SEEK_END:
            	
                if (strlen($this->_data) + $offset >= 0) 
                {
                    $this->_pos = strlen($this->_data) + $offset;
                    return true;
                } 
                else return false;
                break;

            default:
                return false;
        }
    }
}