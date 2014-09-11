<?php

namespace phputil;

class CsvReader {
	
	const SOURCE_STRING = 1;
	const SOURCE_FILE = 2;
	const SOURCE_STREAM = 4;
	
	const FLAG_HAS_HEADERS = 8;
	
	protected $src;
	protected $flags;
	protected $handle;
	
	public function __construct($flags = 0) {
		ini_set('auto_detect_line_endings', '1');
		$this->flags = $flags;
	}
	
	public function setFlags($flags) {
		$this->flags = $flags;
	}
	
	public function getFlags() {
		return $this->flags;
	}
	
	public function setSource($csv) {
			
		switch($this->detectSourceType($csv)) {
				
			case static::SOURCE_STREAM:
				$this->handle = $csv;
				break;
			
			case static::SOURCE_FILE:
				$this->handle = fopen($csv, 'rb');
				break;
			
			case static::SOURCE_STRING:
				// write to temporary stream (2MB memory, then file)
				$this->handle = fopen('php://temp/maxmemory='.(2*1024*1024), 'wb+');
				fwrite($this->handle, $csv);
				break;
				
			default:
				throw new \RuntimeException("Invalid CSV source given.");
		}
	}
	
	public function read() {
		
		if (! isset($this->handle)) {
			throw new \RuntimeException("Cannot read CSV: no source set.");
		}
		
		rewind($this->handle);
		
		$has_headers = ($this->flags & static::FLAG_HAS_HEADERS);
		
		if ($has_headers) {
			$headers = fgetcsv($this->handle);
			$num_headers = count($headers);
		}
		
		$data = array();
		
		while($line = fgetcsv($this->handle)) {
			
			if ($has_headers) {
				// pad the values so array_combine doesnt choke
				$data[] = array_combine($headers, array_pad($line, $num_headers, ''));
			} else {
				$data[] = $line;
			}
		}
		
		return $data;
	}
	
    /**
     * transform a CSV into a XML
     *
     * @param string $root_tag XML root node name
     * @param string $row_tag  XML row node name
     * @param string $cell_tag XML cell node name
     *
     * @return \DOMDocument
     */
    public function toDOMDocument($root_tag = 'XML', $row_tag = 'row', $cell_tag = 'cell') {
    	
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement($root_tag);
		$data = $this->read();
        $key_as_cell_tag = false;
        
		if ($this->flags & static::FLAG_HAS_HEADERS) {
			
			// HTML ?
			if ('table' === $root_tag && 'tr' === $row_tag) {
				
				// get the first data row
				$first_row = array_shift($data);
				// extract the headers from keys
				$headers = array_keys($first_row);
				// add the row back
				array_unshift($first_row, $data);
				
				$item = $doc->createElement($row_tag);
				
				array_walk($headers, function ($value) use (&$item, $doc) {
	                $content = $doc->createTextNode($value);
	                $cell = $doc->createElement('th');
	                $cell->appendChild($content);
	                $item->appendChild($cell);
	            });
	            
	            $root->appendChild($item);
			
			} else {
				// XML
				$key_as_cell_tag = true;
			}
		}
		
        $iterator = new \ArrayIterator($data);
		
        foreach ($iterator as $row) {
        	
            $item = $doc->createElement($row_tag);
            
            array_walk($row, function ($value, $key) use (&$item, $doc, $cell_tag, $key_as_cell_tag) {
                $content = $doc->createTextNode($value);
				if ($key_as_cell_tag) {
                	$cell = $doc->createElement($key);
				} else {
					$cell = $doc->createElement($cell_tag);
				}
                $cell->appendChild($content);
                $item->appendChild($cell);
            });
            
            $root->appendChild($item);
        }
		
        $doc->appendChild($root);

        return $doc;
    }
	
	/**
     * Return a HTML table representation of the CSV Table
     *
     * @param string $class_name optional classname
     *
     * @return string
     */
    public function toHTML($class_name = 'table-csv-data') {
    	$doc = $this->toDOMDocument('table', 'tr', 'td');
        $doc->documentElement->setAttribute('class', $class_name);
        return $doc->saveHTML($doc->documentElement);
    }
	
	public function toXML(array $options = array()) {
		
		$args = array_replace(array(
			'root_tag' => 'XML',
			'row_tag' => 'row',
			'cell_tag' => 'cell',
			'format_output' => false,
		), $options);
		
		$doc = $this->toDOMDocument($args['root_tag'], $args['row_tag'], $args['cell_tag']);
		
		if ($args['format_output']) {
			$doc->formatOutput = true;
		}
        
        return $doc->saveXML($doc->documentElement);
	}
	
	public function toJSON($options = JSON_NUMERIC_CHECK) {
		return json_encode($this->read(), $options);
	}
	
	protected function detectSourceType($csv) {
		
		if (is_resource($csv)) {
			
			$meta = stream_get_meta_data($csv);
			
			if (false === strpos($meta['mode'], 'r') && false === strpos($meta['mode'], '+')) {
				throw new \InvalidArgumentException("Unreadable CSV stream given: '{$csv}'.");
			}
			
			return $this->src = static::SOURCE_STREAM;
			
		} else if (is_file($csv)) {
			
			if (! is_readable($csv)) {
				throw new \InvalidArgumentException("Unreadable CSV file given: '{$csv}'.");
			}
			
			return $this->src = static::SOURCE_FILE;
		
		} else {
			
			if (! is_string($csv)) {
				throw new \InvalidArgumentException("Invalid CSV input, given: ".gettype($csv));
			}
			
			return $this->src = static::SOURCE_STRING;
		}
	}
	
	public function __destruct() {
		is_resource($this->handle) and fclose($this->handle);
	}
	
}
