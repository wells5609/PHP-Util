<?php
/**
 * @package wells5609/php-util
 */

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
	return (bool) getenv('HPHP');
}

/**
 * Checks whether given string looks like a valid URL.
 * 
 * A valid URL will either start with two slashes ("//") or
 * contain a protocol followed by a colon and two slashes ("://").
 * 
 * @param string $str String to check.
 * @return boolean
 */
function is_url($str) {
	return 0 === strpos($str, '//') || fnmatch('*://*', $str);
}

/**
 * Checks whether the given value is a valid JSON string.
 * 
 * @param string $str String to test.
 * @return boolean True if string is JSON, otherwise false.
 */
function is_json($str) {
	if (! is_string($str)) {
		return false;
	}
	$json = @json_decode($str, true);
	return JSON_ERROR_NONE === json_last_error() ? is_array($json) : false;
}

/**
 * Checks whether the given value is a valid serialized string.
 * 
 * @param mixed $data Value to check if serialized
 * @return boolean TRUE If value is a valid serialized string, otherwise false.
 */
function is_serialized($data) {
	if (! is_string($data) || empty($data)) {
    	return false;
	}
	return (@unserialize($data) !== false);
}

/**
 * Checks whether the given value is a valid XML string.
 * 
 * @param mixed $data Value to check if XML.
 * @return boolean TRUE if value is a valid XML string, otherwise false.
 */
function is_xml($data) {
	if (! is_string($data) || '<?xml' !== substr($data, 0, 5)) {
		return false;
	}
	return (simplexml_load_string($data) instanceof SimpleXMLElement);
}

/**
 * Returns true if value can be used in a foreach() loop.
 * 
 * @param wil $var Thing to check if iterable.
 * @return boolean True if var is array or Traversable, otherwise false.
 */
function is_iterable($var) {
	return (is_array($var) || $var instanceof \Traversable);
}

/**
 * Returns true if value can be accessed as an array.
 * 
 * @param wild $var Thing to check if array-accessible.
 * @return boolean True if array or instance of ArrayAccess, otherwise false.
 */
function is_arraylike($var) {
	return (is_array($var) || $var instanceof \ArrayAccess);
}

/**
 * "Boxes" a value by intelligently typecasting the value.
 * 
 * @param mixed $value
 * @return mixed String, integer, float, or boolean value.
 */
function boxval($value) {
	if (! is_scalar($value)) {
		return is_bool($value) ? $value : (string)$value;
	} else if (is_numeric($value)) {
		return (false === strpos($value, '.')) ? (int)$value : (float)$value;
	} else if ($value === 'true' || $value === 'false') {
		return ($value === 'true');
	}
	return (string) $value;
}

/**
 * Returns a MySQL DATETIME string.
 * 
 * @param int|null $time Unix time, or current time if NULL.
 * @return string MySQL DATETIME representation of given time ("Y-m-d H:i:s").
 */
function mysql_datetime($time = null) {
	return date('Y-m-d H:i:s', $time ?: time());
}

/**
 * Returns a MySQL DATE string.
 * 
 * @param int|null $time Unix time, or current time if NULL.
 * @return string MySQL DATE representation of given time ("Y-m-d").
 */
function mysql_date($time = null) {
	return date('Y-m-d', $time ?: time());
}

/**
 * Returns Unix timestamp from a date[time] string or a DateTime object.
 * 
 * @param string|DateTime $date
 * @return int Unix timestamp
 */
function datetotime($date) {	
	if ($date instanceof \DateTime) {
		return $date->getTimestamp();
	}
	if (is_string($date)) {
		return strtotime(str_replace('-', '/', $date));
	}
	return null;
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

/**
 * Returns a unique ID for an object.
 * 
 * Why not use spl_object_hash()? Because the values are only unique for 
 * objects that exist at the time of hashing.
 * 
 * This function uses the object's class and SHA-1 hash of its serialized 
 * value to create the ID. Therefore, two objects of the same class with 
 * the exact same data will generate the same ID.
 * 
 * Raises an error notice if given a non-object.
 * 
 * @param object $object
 * @return string
 */
function objectid($object) {
	if (! is_object($object)) {
		trigger_error('Must pass object to object_id(): given '.gettype($object), E_USER_NOTICE);
		return null;
	}
	return get_class($object).sha1(serialize($object));
}

/**
 * Base64 encode a string, safe for URL's.
 * 
 * @param string $str Data to encode.
 * @return string URL-safe Base64-encoded string.
 */
function base64_url_encode($str) {
    return str_replace(array('+','/','=','\r','\n'), array('-','_'), base64_encode($str));
}

/**
 * Decodes a URL-safe Bas64-encoded string.
 * 
 * @param string $str URL-safe Base64-encoded string.
 * @return string Decoded string.
 */
function base64_url_decode($str) {
	$str = str_replace(array('-', '_'), array('+', '/'), $str);
	if ($m4 = (strlen($str) % 4)) {
		$str .= substr('====', $m4);
	}
	return base64_decode($str);
}

/**
 * Returns an array of items from a CSV string, file, or file handle.
 * 
 * @param string|resource $csv CSV string, file path, or file handle with read capability.
 * @param boolean $has_headers [Optional] Whether the first row of data is headers. Default true.
 * @return array Array of items with keys set to headers if $has_headers = true.
 */
function csv2array($csv, $has_headers = true) {
	
	if (is_resource($csv)) {
		$fh =& $csv;
	} else if (! is_file($csv)) {
		// use 4MB of memory then file
		$fh = fopen('php://temp/maxmemory='.(4*1024*1024), 'wb+');
		fwrite($fh, $csv);
	} else if (! $fh = fopen($csv, 'rb')) {
		trigger_error("Could not open CSV file stream.", E_USER_NOTICE);
		return null;
	}
	
	rewind($fh);
	$data = array();
	
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

if (! function_exists('id')) {
	
	/**
	 * Identity function, returns its argument unmodified.
	 * @author facebook/libphutil
	 * @param mixed Anything.
	 * @return mixed Unmodified argument.
	 */
	function id($var) { return $var; }
}

if (extension_loaded('xdebug')) {
	
	/** 
	 * Changes sissy XDebug defaults. 
	 */
	function xdebug_config(array $vars = null) {
		$settings = array(
			'xdebug.var_display_max_depth' => '15', // default 3
			'xdebug.var_display_max_data' => '1024', // default 512
			'xdebug.var_display_max_children' => '512' // default 128
		);
		isset($vars) and $settings = array_merge($settings, $vars);
		foreach($settings as $varname => $value) {
			ini_set($varname, $value);
		}
		return true;
	}
}
