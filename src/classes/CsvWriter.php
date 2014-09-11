<?php

namespace phputil;

class CsvWriter {
	
	protected $data;
	protected $handle;
	protected $row_callback;
	
	public function __construct($data = null) {
		ini_set('auto_detect_line_endings', '1');
		isset($data) and $this->setData($data);
	}
	
	public function setData($data) {
		
		if (! is_array($data)) {
			
			if (! is_object($data)) {
				throw new \InvalidArgumentException("Write data must be array or object, given: ".gettype($data));
			}
			
			$data = object_to_array($data);
		}
		
		$this->data = $data;
	}
	
	public function setDestination($destination) {
		$this->detectDestination($destination);
	}
	
	public function setRowCallback($callback) {
		
		if (! is_callable($callback)) {
			throw new \InvalidArgumentException("Row callback must be callable.");
		}
		
		$this->row_callback = $callback;
	}
	
	public function write($destination = null) {
		
		if (isset($destination)) {
			$this->detectDestination($destination);
		}
		
		if (! isset($this->handle)) {
			$this->handle = fopen('php://temp/maxmemory='.(2*1024*1024), 'wb+');
		}
		
		rewind($this->handle);
		
		if (isset($this->row_callback)) {
			$row_callback = $this->row_callback;
		}
		
	    foreach ($this->data as $i => $row) {
	    
			if (isset($row_callback)) {
	    		$row_callback($row, $i);
	    	}
		
			fputcsv($this->handle, $row);
		}
	    
		return $this->handle;
	}
	
	protected function detectDestination($destination) {
		
		if (is_resource($destination)) {
		
			$meta = stream_get_meta_data($destination);
			
			if (false === strpos($meta['mode'], 'w') && false === strpos($meta['mode'], '+')) {
				throw new \InvalidArgumentException("Unwritable CSV stream given: '{$destination}'.");
			}
			
			$this->handle = $destination;
			
		} else if (is_file($destination)) {
			
			if (! is_writable($destination)) {
				throw new \InvalidArgumentException("Unwritable CSV file given: '{$destination}'.");
			}
			
			$this->handle = fopen($destination, 'wb+');
		
		} else {
			throw new \InvalidArgumentException("Invalid CSV write destination, given: ".gettype($destination));
		}
	}
	
	public function __destruct() {
		is_resource($this->handle) and fclose($this->handle);
	}
	
}
