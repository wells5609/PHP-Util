<?php
/**
 * @package wells5609\PHP-Util
 */

/** ================================
				XML
================================= */

class XML {
	
	protected $writer;
	protected $reader;
	protected $root_tag = 'XML';
	protected $version = '1.0';
	protected $encoding = 'UTF-8';
	
	public static function writeDocument(array $data, $root_tag = 'document', $version = '1.0', $encoding = 'UTF-8') {
		
		$xml = new static();
		$xml->setRootTag($root_tag);
		$xml->setVersion($version);
		$xml->setEncoding($encoding);
		
		return $xml->write($data);
	}
	
	public function __construct(\XMLWriter $writer = null, \XMLReader $reader = null) {
		if (isset($writer)) {
			$this->writer = $writer;
		}
		if (isset($reader)) {
			$this->reader = $reader;
		}
	}
	
	public function setRootTag($tag) {
		$this->root_tag = esc_alnum($tag);
	}
	
	public function setVersion($ver) {
		$this->version = $ver;
	}
	
	public function setEncoding($encoding) {
		$this->encoding = strtoupper($encoding);
	}
	
	public function write(array $data) {
		
		if (! isset($this->writer)) {
			$this->writer = new \XMLWriter();
		}
		
		$this->writer->openMemory();
		
		$this->writer->startDocument($this->version, $this->encoding);
		$this->writer->startElement($this->root_tag);
		
		$this->element($data);
		
		$this->writer->endElement();
		$this->writer->endDocument();
		
		return $this->writer->outputMemory(true);
	}
	
	protected function element(array $data) {
	
		foreach ($data as $key => $value) {
			
			if (! ctype_alnum($key)) {
				$key = strip_tags(str_replace(array(' ', '-', '/', '\\'), '_', $key));
			}
			
			if (is_numeric($key)) {
				$key = 'Item_'.intval($key);
			}
			
			if (is_object($value)) {
				if (method_exists($value, 'xmlSerialize')) {
					$value = $value->xmlSerialize();
				} else {
					$value = method_exists($value, 'toArray') ? $value->toArray() : get_object_vars($value);
				}
			}
			
			if (is_array($value)) {
	
				if (isset($value['@tag']) && is_string($value['@tag'])) {
					$key = str_replace(' ', '', $value['@tag']);
					unset($value['@tag']);
				}
	
				$this->writer->startElement($key);
	
				if (isset($value['@attributes'])) {
					foreach (array_unique($value['@attributes']) as $k => $v) {
						$this->writer->writeAttribute($k, $v);
					}
					unset($value['@attributes']);
				}
	
				$this->element($value);
				
				$this->writer->endElement();
				
			} else if (is_scalar($value)) {
			
				$this->writer->writeElement($key, htmlspecialchars($value));
			}
		}
	}
	
}

/**
 * Creates and returns a new XML document as string.
 * 
 * @param array $data Data to format as XML. Nested arrays are preferred.
 * @param string $root_tag Tag to place at root of document. Default 'document'.
 * @param string $version XML version to use. Default '1.0'.
 * @param string $encoding XML encoding to use. Default 'UTF-8'.
 * @return string XML
 */
function xml_write_document(array $data, $root_tag = 'document', $version = '1.0', $encoding = 'UTF-8') {
	return XML::writeDocument($data, $root_tag, $version, $encoding);
}

/**
 * Adds an XML element to the given XMLWriter object.
 * 
 * @param XMLWriter $xml XMLWriter object, possibly from xml_write_document().
 * @param array $data Associative array of the element's data.
 * @return void
 */
function xml_write_element(\XMLWriter $xml, array $data) {

	foreach ($data as $key => $value) {
		
		if (! ctype_alnum($key)) {
			$key = strip_tags(str_replace(array(' ', '-', '/', '\\'), '_', $key));
		}
		
		if (is_numeric($key)) {
			$key = 'Item_'.intval($key);
		}
		
		if (is_object($value)) {
			$value = get_object_vars($value);
		}
		
		if (is_array($value)) {

			if (isset($value['@tag']) && is_string($value['@tag'])) {
				$key = str_replace(' ', '', $value['@tag']);
				unset($value['@tag']);
			}

			$xml->startElement($key);

			if (isset($value['@attributes'])) {
				foreach (array_unique($value['@attributes']) as $k => $v) {
					$xml->writeAttribute($k, $v);
				}
				unset($value['@attributes']);
			}

			xml_write_element($xml, $value);

			$xml->endElement();

		} else if (is_scalar($value)) {
			$xml->writeElement($key, htmlspecialchars($value));
		}
	}
}

/**
 * Converts XML to an array.
 * 
 * JSON-encodes and decodes the XML after loading into a SimpleXML object. 
 * The returned arrays may therefore have an "@attributes" key.
 * 
 * @param string $xml XML string, or path to an XML file.
 * @return array XML as a nested array.
 */
function xml2array($xml) {
	
	if (is_file($xml)) {
		if (! is_readable($xml)) {
			trigger_error("Unreadable XML file given with path $xml.");
			return null;
		}
		$xml = simplexml_load_file($xml);
	} else {
		$xml = simplexml_load_string($xml);
	}
	
	return json_decode(json_encode($xml), true);
}
