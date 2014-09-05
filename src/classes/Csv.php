<?php

namespace phputil;

class Csv {
	
	const MODE_READ = 0;
	const MODE_WRITE = 1;
	
	const SRC_STRING = 1;
	const SRC_FILE = 2;
	const SRC_STREAM = 4;
	
	const ATTR_HAS_HEADERS = 8;
	const ATTR_WRITE_DEST = 16;
	const ATTR_WRITE_ROW_CALLBACK = 32;
	
	protected $mode;
	protected $src;
	protected $csv;
	protected $attributes = array();
	
	public function __construct($mode, array $attributes = null) {
		
		if (! in_array($mode, array(static::MODE_READ, static::MODE_WRITE), true)) {
			throw new \InvalidArgumentException("Invalid mode given: '{$mode}'.");
		}
		
		$this->mode = $mode;
		
		ini_set('auto_detect_line_endings', true);
		
		if (isset($attributes)) {
			$this->setAttributes($attributes);
		}
	}
	
	public function isRead() {
		return static::MODE_READ === $this->mode;
	}
	
	public function isWrite() {
		return static::MODE_WRITE === $this->mode;
	}
	
	public function setAttributes(array $attributes) {
		foreach($attributes as $key => $value) {
			$this->setAttribute($key, $value);
		}
	}
	
	public function setAttribute($attr, $value) {
		$this->attributes[$attr] = $value;
	}
	
	public function getAttribute($attr) {
		return isset($this->attributes[$attr]) ? $this->attributes[$attr] : null;
	}
	
	public function set($csv) {
		
		if ($this->isRead()) {
			$this->detectInputSource($csv);
		
		} else if (! is_array($csv)) {
			
			if (! is_object($csv)) {
				throw new \InvalidArgumentException("Write data must be array or object, given: ".gettype($csv));
			}
			
			$csv = object_to_array($csv);
		}
		
		$this->csv = $csv;
	}
	
	public function get(array $attributes = null) {
		
		isset($attributes) and $this->setAttributes($attributes);
		
		return $this->isRead() ? $this->read() : $this->write();
	}
	
	protected function read() {
		
		$fh = $this->getInputHandle();
		rewind($fh);
		
		$data = array();
		$has_headers = $this->getAttribute(static::ATTR_HAS_HEADERS);
		
		if ($has_headers) {
			$headers = fgetcsv($fh);
			$num_headers = count($headers);
		}
		
		while($line = fgetcsv($fh)) {
		
			if ($has_headers) {
				// pad the values so array_combine doesnt choke
				$values = array_pad($line, $num_headers, '');
				$data[] = array_combine($headers, $values);
			
			} else {
				$data[] = $line;
			}
		}
		
		fclose($fh);
		
		return $data;
	}
	
	protected function write() {
		
		$handle = $this->getOutputHandle();
		
		rewind($handle);
		
		$row_callback = $this->getAttribute(static::ATTR_WRITE_ROW_CALLBACK);
		
	    foreach ($this->csv as $i => $row) {
	    
			if (isset($row_callback)) {
	    		$row_callback($row, $i);
	    	}
		
			fputcsv($handle, $row);
		}
	    
		return $handle;
	}
	
	protected function getInputHandle() {
		
		switch($this->src) {
			
			case static::SRC_STREAM:
				return $this->csv;
			
			case static::SRC_FILE:
				return fopen($this->csv, 'rb');
			
			case static::SRC_STRING:
				// write to temporary stream (2MB memory, then file)
				$fh = fopen('php://temp/maxmemory='.(2*1024*1024), 'wb+');
				fwrite($fh, $this->csv);
				return $fh;
		}
	}
	
	protected function detectInputSource($csv) {
		
		if (is_resource($csv)) {
			
			$meta = stream_get_meta_data($csv);
			
			if (false === strpos($meta['mode'], 'r') && false === strpos($meta['mode'], '+')) {
				throw new \InvalidArgumentException("Unreadable CSV stream given: '{$csv}'.");
			}
			
			$this->src = static::SRC_STREAM;
			
		} else if (is_file($csv)) {
			
			if (! is_readable($csv)) {
				throw new \InvalidArgumentException("Unreadable CSV file given: '{$csv}'.");
			}
			
			$this->src = static::SRC_FILE;
		
		} else {
			
			if (! is_string($csv)) {
				throw new \InvalidArgumentException("Invalid CSV input, given: ".gettype($csv));
			}
			
			$this->src = static::SRC_STRING;
		}
	}
	
	protected function getOutputHandle() {
			
		$destination = $this->getAttribute(static::ATTR_WRITE_DEST);
		
		if (! $destination) {
			$destination = fopen('php://temp/maxmemory='.(4*1024*1024), 'wb+');
		
		} else if (is_resource($destination)) {
		
			$meta = stream_get_meta_data($destination);
			
			if (false === strpos($meta['mode'], 'w') && false === strpos($meta['mode'], '+')) {
				throw new \InvalidArgumentException("Unwritable CSV stream given: '{$destination}'.");
			}
			
		} else if (is_file($destination)) {
			
			if (! is_writable($destination)) {
				throw new \InvalidArgumentException("Unwritable CSV file given: '{$destination}'.");
			}
			
			$destination = fopen($destination, 'wb+');
		
		} else {
			throw new \InvalidArgumentException("Invalid CSV write destination, given: ".gettype($destination));
		}
		
		return $destination;
	}
	
}
