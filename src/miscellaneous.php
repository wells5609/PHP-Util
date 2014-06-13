<?php
/**
 * @package wells5609/php-util
 */

/**
 * Defines a constant if not yet defined.
 * 
 * @param string $const Name of constant
 * @param scalar $value Value to define constant if undefined.
 * @return void
 */
function define_default($const, $value) {
	defined($const) OR define($const, $value);
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
 * Returns true if given string is valid JSON.
 * 
 * @param string $str String to test.
 * @return boolean True if string is JSON, otherwise false.
 */
function is_json($str) {
	$json = @json_decode($str, true);
	return is_array($json);
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
 * Returns a unique ID for an object.
 * 
 * Why not use spl_object_hash()? Because it creates hashes that are
 * unique only for objects that have already been instantiated.
 * 
 * This function uses the object class and SHA-1 hash of its serialized 
 * value to create the ID. Therefore, two objects of the same class with 
 * the exact same data will generate the same ID.
 * 
 * @param object $object
 * @return string
 */
function object_id($object) {
	if (! is_object($object)) {
		trigger_error('Must pass object to object_id(): given '.gettype($object));
		return null;
	}
	return get_class($object).sha1(serialize($object));
}

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
 * Changes sissy XDebug defaults. 
 */
function xdebug_config(array $vars = null) {
	if (! extension_loaded('xdebug')) {
		return false;
	}
	$settings = array(
		'xdebug.var_display_max_depth' => '20', // default 3
		'xdebug.var_display_max_data' => '1024', // default 512
		'xdebug.var_display_max_children' => '256' // default 128
	);
	isset($vars) and $settings = array_merge($settings, $vars);
	foreach($settings as $varname => $value) {
		ini_set($varname, $value);
	}
	return true;
}
