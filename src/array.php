<?php
/**
 * @package PHP-Util\Array
 * 
 * * is_iterable()
 * * is_arraylike()
 * * is_array_of_instances()
 * * is_array_of_arrays()
 * * in_arrayi()
 * * explode_trim()
 * * implode_nice()
 * * index()
 * * array_column() (compat PHP<5.5)
 */

/** ================================
 		  ARRAYS/ITERATION
================================= */

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
 * Checks if all values of array are instances of the passed class.
 * Throws InvalidArgumentException if it isn't true for any value.
 *
 * @param array
 * @param string Name of the class.
 * @param boolean Whether to throw exceptions for invalid values. Default false.
 * @return boolean True if all objects are instances of given class, otherwise false.
 * @throws InvalidArgumentException
 */
function is_array_of_instances(array $arr, $class, $throw_exceptions = false) {
	foreach ( $arr as $key => $object ) {
		if (! $object instanceof $class) {
			if ($throw_exceptions) {
				$given = gettype($object);
				if (is_object($object)) {
					$given = 'instance of '.get_class($object);
				}
				$msg = "Array item with key '{$key}' must be an instance of {$class}, {$given} given.";
				throw new InvalidArgumentException($msg);
			} else {
				return false;
			}
		}
	}
	return true;
}

/**
 * Checks if all values of an array are arrays.
 * 
 * @param array $arr
 * @param boolean $throw_exceptions Whether to throw exceptions for invalid values or just return false.
 * @return boolean
 * @throws InvalidArgumentException
 */
function is_array_of_arrays(array $arr, $throw_exceptions = false) {
	foreach($arr as $key => $object) {
		if (! is_array($object)) {
			if ($throw_exceptions) {
				$given = gettype($object);
				$msg = "Array item with key '{$key}' must be of type array, {$given} given.";
				throw new InvalidArgumentException($msg);
			} else {
				return false;
			}
		}
	}
	return true;
}

/**
 * Case-insensitive in_array().
 * 
 * Note you cannot pass an array as the needle to this function.
 * 
 * @param string $needle Needle
 * @param array $haystack Haystack
 */
function in_arrayi($needle, $haystack) {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

/**
 * Explodes a string into an array and trims whitespace from each item.
 * 
 * @param string $delim String delimeter to use in explode().
 * @param string $str String to explode.
 * @param string $charlist Characters to trim from each exploded item in trim().
 * @return array Indexed array of trimmed string parts delimited by $delim.
 */
function explode_trim($delim, $str, $charlist = '\t\r\n ') {
	return array_map('trim', explode($delim, $str), $charlist);
}

/**
 * Implode an array into a list of items separated by $separator.
 * Use $last_separator for the last list item.
 *
 * Useful for natural language lists (e.g "one, two, and threeve").
 *
 * @author humanmade/hm-core
 * @link https://github.com/humanmade/hm-core/blob/master/hm-core.functions.php
 *
 * @param array $array
 * @param string $separator. (default: ', ')
 * @param string $last_separator. (default: ', and ')
 * @return string a list of array values
 */
function implode_nice(array $array, $separator = ', ', $last_separator = ', and ') {
	if (1 === count($array)) {
		return reset($array);
	}
	$end_value = array_pop($array);
	return implode($separator, $array).$last_separator.$end_value;
}

if (! function_exists('index') && ! function_exists('idx')) :
		
	/**
	 * Access an array index, retrieving the value stored there if it exists or
	 * a default if it does not. This function allows you to concisely access an
	 * index which may or may not exist without raising a warning.
	 *
	 * @author facebook/libphutil
	 * 
	 * @param array Array to access.
	 * @param scalar Index to access in the array.
	 * @param wild Default value to return if the key is not present in the array.
	 * @return wild If $array[$key] exists, that value is returned. If not,
	 * $default is returned without raising a warning.
	 */
	function index(array $array, $key, $default = null) {
		if (isset($array[$key])) {
			return $array[$key];
		}
		if ($default === null || array_key_exists($key, $array)) {
			return null;
		}
		return $default;
	}

endif;

/**
 * array_column() back-compat (PHP < 5.5)
 */
if (! function_exists('array_column')) :
	
	function array_column(array $array, $column_key, $index_key = null) {
		$return = array();
		foreach($array as $arr) {
			if (isset($arr[$column_key])) {
				if (isset($index_key) && isset($arr[$index_key])) {
					$return[$arr[$index_key]] = $arr[$column_key];
				} else {
					$return[] = $arr[$column_key];
				}
			}
		}
		return $return;
	}
	
endif;
