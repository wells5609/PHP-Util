<?php
/**
 * @package wells5609\php-util
 * 
 * XML functions:
 *  * xml_write_document()
 *  * xml_write_element()
 *  * xml2array()
 */

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
	return \phputil\Xml::writeDocument($data, $root_tag, $version, $encoding);
}

/**
 * Adds an XML element to the given XMLWriter object.
 * 
 * @param XMLWriter $xml XMLWriter instance.
 * @param array $data Associative array of data.
 * @return void
 */
function xml_write_element(\XMLWriter $xml, array $data) {
	return \phputil\Xml::writeElement($xml, $data);
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
	return \phputil\Xml::toArray($xml);
}
