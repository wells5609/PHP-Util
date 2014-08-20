<?php
/**
 * @package wells5609\PHP-Util
 * 
 * XML class and functions:
 * 
 *  * \phpUtil\Xml::writeDocument()
 *  * \phpUtil\Xml::writeElement()
 *  * xml_write_document()
 *  * xml_write_element()
 *  * xml2array()
 * 
 */

namespace phpUtil {
	
	class Xml {
		
		protected $writer;
		protected $reader;
		protected $root_tag = 'XML';
		protected $version = '1.0';
		protected $encoding = 'UTF-8';
		
		/**
		 * Constructor - takes a writer and reader.
		 */
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
			
			static::writeElement($this->writer, $data);
			
			$this->writer->endElement();
			$this->writer->endDocument();
			
			return $this->writer->outputMemory(true);
		}
		
		/**
		 * Static all-in-one document writer.
		 * 
		 * @param array $data Document data.
		 * @param string $root_tag Root document element. Default "document".
		 * @param string $version XML version. Default "1.0".
		 * @param string $encoding XML encoding. Default "UTF-8".
		 * @return string XML document string.
		 */
		public static function writeDocument(array $data, $root_tag = 'document', $version = '1.0', $encoding = 'UTF-8') {
			
			$xml = new static();
			$xml->setRootTag($root_tag);
			$xml->setVersion($version);
			$xml->setEncoding($encoding);
			
			return $xml->write($data);
		}
		
		/**
		 * Writes an element.
		 * 
		 * @param \XMLWriter $xml_writer An XMLWriter instance.
		 * @param array $data Data to write to the document.
		 * @return void
		 */
		public static function writeElement(\XMLWriter $xml_writer, array $data) {
		
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
					} else if (method_exists($value, 'toArray')) {
						$value = $value->toArray();
					} else {
						$value = get_object_vars($value);
					}
				}
				
				if (is_array($value)) {
		
					if (isset($value['@tag']) && is_string($value['@tag'])) {
						$key = str_replace(' ', '', $value['@tag']);
						unset($value['@tag']);
					}
		
					$xml_writer->startElement($key);
		
					if (isset($value['@attributes'])) {
						foreach(array_unique($value['@attributes']) as $k => $v) {
							$this->writer->writeAttribute($k, $v);
						}
						unset($value['@attributes']);
					}
		
					static::writeElement($xml_writer, $value);
					
					$xml_writer->endElement();
					
				} else if (is_scalar($value)) {
				
					$xml_writer->writeElement($key, htmlspecialchars($value));
				}
			}
		}
		
	}
}

namespace {

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
	return \phpUtil\Xml::writeDocument($data, $root_tag, $version, $encoding);
}

/**
 * Adds an XML element to the given XMLWriter object.
 * 
 * @param XMLWriter $xml XMLWriter instance.
 * @param array $data Associative array of data.
 * @return void
 */
function xml_write_element(\XMLWriter $xml, array $data) {
	return \phpUtil\Xml::writeElement($xml, $data);
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
			trigger_error("Unreadable XML file given: '$xml'.");
			return null;
		}
		$xml = simplexml_load_file($xml);
	} else {
		$xml = simplexml_load_string($xml);
	}
	
	return json_decode(json_encode($xml), true);
}

}
