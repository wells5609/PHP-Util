<?php

namespace phputil;

class JsonReader 
{
	
	const SOURCE_FILE = 1;
	const SOURCE_STREAM = 2;
	const SOURCE_STRING = 4;
	
	const FLAG_ASSOC = 1;
	
	protected $flags;
	protected $source;
	protected $source_type;
	protected $data;
	
	public function __construct($flags = 0) {
		$this->flags = $flags;
	}
	
	public function setFlags($flags) {
		$this->flags = $flags;
	}
	
	public function getFlags() {
		return $this->flags;
	}
	
	public function setSource($source) {
		$this->detectSource($source);
	}
	
	public function read($json_flags = JSON_BIGINT_AS_STRING, $depth = 512) {
		
		if (! isset($this->source)) {
			throw new \RuntimeException("Cannot read JSON data: no source set.");
		}
		
		if (static::SOURCE_FILE === $this->source_type) {
			$data = file_get_contents($this->source);
		
		} else if (static::SOURCE_STREAM === $this->source_type) {
			rewind($this->source);
			$data = stream_get_contents($this->source);
		
		} else {
			$data = $this->source;
		}
		
		$assoc = ($this->flags & static::FLAG_ASSOC);
		
		return json_decode($data, $assoc, $depth, $json_flags);
	}
	
	protected function detectSource($source) {
		
		if (is_file($source)) {
			
			if (! is_readable($source)) {
				throw new \InvalidArgumentException("Unreadable file given: '$source'.");
			}
			
			$this->source_type = static::SOURCE_FILE;
		
		} else if (is_resource($source)) {
			
			$meta = stream_get_meta_data($source);
			
			if (false === strpos($meta['mode'], 'r') && false === strpos($meta['mode'], '+')) {
				throw new \InvalidArgumentException("Unwritable file stream given: '{$source}'.");
			}
			
			$this->source_type = static::SOURCE_STREAM;
			
		} else {
			
			if (! is_string($source)) {
				throw new \InvalidArgumentException("Expecting file, stream, or string, given: ".gettype($source));
			}
			
			$this->source_type = static::SOURCE_STRING;
		}
		
		$this->source = $source;
	}
	
}
