<?php
/**
 * @package PHP-Util
 */
if (! function_exists('id')) :
	
	/**
	 * Identity function, returns its argument unmodified.
	 *
	 * @author facebook/libphutil
	 * 
	 * @param wild Anything.
	 * @return wild Unmodified argument.
	 */
	function id($var) {
		return $var;
	}

endif;

/**
 * Returns true if server OS is Windows.
 * 
 * @return boolean True if server OS is Windows, otherwise false.
 */
function is_win() {
	return '\\' === DIRECTORY_SEPARATOR;
}

/**
 * Returns true if HipHop Virtual Machine be runnin.
 * 
 * @return boolean True if HHVM is active, otherwise false.
 */
function is_hhvm() {
	return (array_key_exists('HPHP', $_ENV) && $_ENV['HPHP'] === 1);
}

/**
 * Defines a constant if not yet defined.
 * 
 * @param string $const Name of constant
 * @param scalar $value Value to define constant if undefined.
 * @return void
 */
function define_default($const, $value) {
	defined($const) or define($const, $value);
}

/**
 * Retrieve the value of a cookie.
 * 
 * Complement to setcookie().
 *
 * @param string $name Cookie name.
 * @return mixed Value of cookie if exists, otherwise false.
 */
function getcookie($name) {
	return array_key_exists($name, $_COOKIE) ? $_COOKIE[$name] : false;
}

/** ================================
			Callables
================================= */

/**
 * Invokes an invokable callback given array of arguments.
 * 
 * @author FuelPHP
 * 
 * @param wild $var Anything - if Closure or object with __invoke() method, called with $args.
 * @param array $args Array of arguments to pass to callback.
 * @return mixed Result of callback if invokable, otherwise original value.
 */
function result($var, array $args = array()) {
	return ($var instanceof \Closure || method_exists($var, '__invoke')) 
		? call_user_func_array($var, $args) 
		: $var;
}

/**
 * Invokes a callback using array of arguments.
 * 
 * Uses the Reflection API to invoke an arbitrary callable.
 * Thus, arguments can be named and/or not in the proper 
 * order for calling (they will be correctly ordered).
 * 
 * Use case: url routing, where the order of route variables may
 * create an "unordered" array of callback parameters.
 * 
 * @param callable $callback Callable callback.
 * @param array $args Array of callback parameters.
 * @return mixed Result of callback.
 * @throws LogicException on invalid callable
 * @throws RuntimeException on missing callback param
 */
function invoke($callback, array $args = array()) {
	
	$type = null;
	
	if ($callback instanceof \Closure || is_string($callback)) {
		$refl = new \ReflectionFunction($callback);
		$type = 'func';
	} else if (is_array($callback)) {
		$refl = new \ReflectionMethod($callback[0], $callback[1]);
		$type = 'method';
	} else if (is_object($callback)) {
		$refl = new \ReflectionMethod(get_class($callback), '__invoke');
		$type = 'object';
	} else {
		throw new \LogicException("Unknown callback type, given ".gettype($callback));
	}
	
	$params = array();
	
	foreach($refl->getParameters() as $i => $param) {
		
		$name = $param->getName();
		
		if (isset($args[$name])) {
			$params[$name] = $args[$name];
		} else if (isset($args[$i])) {
			$params[$name] = $args[$i];
		} else if ($param->isDefaultValueAvailable()) {
			$params[$name] = $param->getDefaultValue();
		} else {
			throw new \RuntimeException("Missing parameter '$param'.");
		}
	}
	
	switch($type) {
	
		case 'func' :
			return $refl->invokeArgs($params);
	
		case 'method' :
			return $refl->isStatic() 
				? call_user_func_array($callback, $params) 
				: $refl->invokeArgs($callback[0], $params);
	
		case 'object' :
			return $refl->invokeArgs($callback, $params);
	}
}

/**
 * Returns human-readable identifier for a callable.
 * @param callable $fn Callable.
 * @return string Human-readable callable identifier.
 */
function callable_uid($fn) {
	if (is_string($fn)) {
		return $fn;
	}
	if (is_object($fn)) {
		if ($fn instanceof \Closure) {
			return 'Closure';
		}
		return get_class($fn).'::__invoke';
	}
	if (is_array($fn)) {
		if (is_object($fn[0])){
			return get_class($fn[0]).'->'.$fn[1];
		}
		return $fn[0].'::'.$fn[1];
	}
}

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

	foreach ( $data as $key => $value ) {
		
		if (! ctype_alnum($key)) {
			$key = strip_tags(str_replace(array(' ', '-', '/', '\\'), '_', $key));
		}
		
		is_numeric($key) and $key = 'key_'. (int)$key;
		
		is_object($value) and $value = get_object_vars($value);

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
