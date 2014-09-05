<?php

namespace phputil;

use XMLWriter;
use XMLReader;

class Xml {
	
	protected $writer;
	protected $reader;
	protected $root_tag = 'XML';
	protected $version = '1.0';
	protected $encoding = 'UTF-8';
	
	/**
	 * Constructor.
	 * 
	 * @param \XMLWriter $writer [Optional]
	 * @param \XMLReader $reader [Optional]
	 */
	public function __construct(XMLWriter $writer = null, XMLReader $reader = null, array $options = null) {
		
		isset($writer) and $this->writer = $writer;
		isset($reader) and $this->reader = $reader;
		
		if (! empty($options)) {
			isset($options['root_tag']) and $this->setRootTag($options['root_tag']);
			isset($options['version']) and $this->setVersion($options['version']);
			isset($options['encoding']) and $this->setEncoding($options['encoding']);
		}
	}
	
	public function setRootTag($tag) {
		$this->root_tag = str_alnum($tag);
	}
	
	public function setVersion($ver) {
		$this->version = $ver;
	}
	
	public function setEncoding($encoding) {
		$this->encoding = strtoupper($encoding);
	}
	
	public function write(array $data) {
		
		if (! isset($this->writer)) {
			$this->writer = new XMLWriter();
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
	public static function writeDocument(array $data, $root_tag = 'XML', $version = '1.0', $encoding = 'UTF-8') {
		$xml = new static(null, null, array(
			'root_tag' => $root_tag,
			'version' => $version,
			'encoding' => $encoding,
		));
		return $xml->write($data);
	}
	
	/**
	 * Writes an element.
	 * 
	 * @param \XMLWriter $xml_writer An XMLWriter instance.
	 * @param array $data Data to write to the document.
	 * @return void
	 */
	public static function writeElement(XMLWriter $xml_writer, array $data) {
	
		foreach ($data as $key => $value) {
			
			if (! ctype_alnum($key)) {
				$key = strip_tags(str_replace(array(' ', '-', '/', '\\'), '_', $key));
				$key = str_alnum($key);
			}
			
			if (is_numeric($key)) {
				$key = 'Item_'.intval($key);
			}
			
			if (is_object($value)) {
				
				if (method_exists($value, 'xmlSerialize')) {
					$value = $value->xmlSerialize();
				
				} else if (method_exists($value, 'toArray')) {
					$value = $value->toArray();
				
				} else if ($value instanceof \Traversable) {
					$value = iterator_to_array($value);
				
				} else {
					$value = get_object_vars($value);
				}
			}
			
			if (is_array($value)) {
	
				if (isset($value['@tag']) && is_string($value['@tag'])) {
					$key = str_alnum(str_replace(' ', '', $value['@tag']));
					unset($value['@tag']);
				}
				
				$xml_writer->startElement($key);
	
				if (isset($value['@attributes']) && is_array($value['@attributes'])) {
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