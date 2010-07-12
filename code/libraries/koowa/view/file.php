<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_View
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Use to force browser to download a file from the file system
 *
 * @example
 * // in child view class
 * public function display()
 * {
 * 		$this->path = path/to/file');
 * 		// OR
 * 		$this->output = $file_contents;
 * 
 * 		$this->filename = foobar.pdf'; 
 *
 * 		// optional:
 * 		$this->mimetype    = 'application/pdf';
 * 		$this->disposition =  'inline'; // defaults to 'attachment' to force download
 *
 * 		return parent::display();
 * }
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package     Koowa_View
 */
class KViewFile extends KViewAbstract
{
	/**
	 * The file path
	 * 
	 * @var string
	 */
	public $path = '';
	
	/**
	 * The file name
	 * 
	 * @var string
	 */
	public $filename = '';
	
	/**
	 * The file disposition
	 * 
	 * @var string
	 */
	public $disposition = 'attachment';
	
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
        parent::__construct($config);
        
        $this->set($config->toArray());
	}
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
		$count = count($this->_identifier->path);

		$config->append(array(
            'path'		  => '',
			'filename'	  => $this->_identifier->path[$count-1].'.'.$this->_identifier->name,
			'disposition' => 'attachment'
       	));
       	
       	parent::_initialize($config);
    }
	
	/**
	 * Return the views output
 	 *
	 * @return KViewFile
	 */
	public function display()
	{
		// For a certain unmentionable browser
		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
		
		// Remove php's time limit
	    if(!ini_get('safe_mode') ) {
		    @set_time_limit(0);
        }

		// Mimetype
		// @TODO magic mimetypes
		if($this->mimetype) {
			header('Content-type: '.$this->mimetype);
		}
		 
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');

		// Prevent caching
		header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");
        
		// Clear buffer
        while (@ob_end_clean());
    
		$this->filename = basename($this->filename);		
    	if(isset($this->output)) // File body is passed as string
    	{
			if(empty($this->filename)) {
				throw new KViewException('No filename supplied');
			}
			$this->_setDisposition();
			$filesize = strlen($this->output);
			header('Content-Length: '.$filesize);
			flush();
			echo $this->output;
    	}
    	elseif(isset($this->path)) // File is read from disk
    	{
     		if(empty($this->filename)) {
				$this->filename = basename($this->path);				
			}
			$filesize = @filesize($this->path);
			header('Content-Length: '.$filesize);
    		$this->_setDisposition();
			flush();
			$this->_readChunked($this->path);
    	}
    	else throw new KViewException('No output or path supplied');
		
		die;
	}

	
	/**
	 * Set the header disposition headers
 	 *
	 * @return KViewFile
	 */
	protected function _setDisposition()
	{
		// @TODO :Content-Disposition: inline; filename="foo"; modification-date="'.$date.'"; size=123;	
		if(isset($this->disposition) && $this->disposition == 'inline') {		
			header('Content-Disposition: inline; filename="'.$this->filename.'"');
		} else {	
			header('Content-Description: File Transfer');
			header('Content-type: application/force-download');
			header('Content-Disposition: attachment; filename="'.$this->filename.'"');
		}
		return $this;
	}
	
	
	/**
	 * Read a file in chunks and flush it to the output stream
 	 *
 	 * @param  string 	Path to a file to be read
	 * @return integer 	Number of chunks being flushed
	 */
    protected function _readChunked($path)
    {
   		$chunksize	= 1*(1024*1024); // Chunk size
   		$buffer 	= '';
   		$cnt 		= 0;
   		
   		$handle = fopen($path, 'rb');
   		if ($handle === false) {
       		throw new KViewException('Cannot open file');
   		}
   		
   		while (!feof($handle)) 
   		{
       		$buffer = fread($handle, $chunksize);
       		echo $buffer;
			@ob_flush();
			flush();
       		$cnt += strlen($buffer);
   		}
   		
       $status = fclose($handle);
   	   return $cnt; 
	}
}
