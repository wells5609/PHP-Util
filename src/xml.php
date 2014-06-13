<?php
/**
 * @package wells5609\PHP-Util
 */

/** ================================
				XML
================================= */

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
	$xml = new \XMLWriter();
	$xml->openMemory();
	$xml->startDocument($version, $encoding);
	$xml->startElement($root_tag);
	xml_write_element($xml, $data);
	$xml->endElement();
	$xml->endDocument();
	return $xml->outputMemory(true);
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
			$key = 'Item_'. (int)$key;
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
