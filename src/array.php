<?php
/**
 * @package PHP-Util
 */

/** ================================
		Arrays & Iteration
================================= */

/**
 * Calls a method on each object in an array and returns an array of the results.
 * 
 * @param array $objects Array of objects.
 * @param string $method Method to call on each object.
 * @param string|null $key_method [Optional] Method used to get the key used in return array.
 * @return array Indexed array of values returned from each object.
 */
function array_mpull(array $objects, $method, $key_method = null) {
	$return = array();
	foreach($objects as $key => &$obj) {
		if (null !== $key_method) {
			$key = $obj->$key_method();
		}
		if (null === $method) {
			$return[$key] = $obj;
		} else {
			$return[$key] = $obj->$method();
		}
	}
	return $return;
}

/**
 * Pulls a property from each object in an array and returns an array of the values.
 * 
 * @param array $objects Array of objects.
 * @param string $property Name of property to get from each object.
 * @param string|null $key_prop [Optional] Property to use for keys in returned array.
 * @return array Indexed array of property value from each object.
 */
function array_ppull(array $objects, $property, $key_prop = null) {
	$return = array();
	foreach($objects as $key => &$obj) {
		if (null !== $key_prop) {
			$key = $obj->$key_prop;
		}
		if (null === $property) {
			$return[$key] = $obj;
		} else {
			$return[$key] = $obj->$property;
		}
	}
	return $return;
}

/**
 * Pulls a named key value from each array in an array and returns an array of the values.
 * 
 * @param array $arrays Array of arrays.
 * @param string|int $index Index offset or key name to pull from each array.
 * @param string|null $key_index [Optional] Index/key to use for keys in returned array.
 * @return array Indexed array of the value pulled from each array.
 */
function array_kpull(array $arrays, $index, $key_index = null) {
	$return = array();
	foreach($arrays as $key => &$array) {
		if (null !== $key_index) {
			$key = $array[$key_index];
		}
		if (null === $index) {
			$return[$key] = $array;
		} else {
			$return[$key] = $array[$index];
		}
	}
	return $return;
}

/**
 * Filters an array of objects by method.
 * 
 * If object returns an empty value, the object is not included in the returned array.
 * To reverse this behavior (only include those which return empty), pass true
 * as the third parameter.
 * 
 * @author facebook/libphutil
 * 
 * @param array $objects Array of objects.
 * @param string $method Method to call on each object.
 * @param boolean $negate Whether to return objects which return empty. Default false.
 * @return array Objects which pass the filter.
 */
function array_mfilter(array $objects, $method, $negate = false) {
	$return = array();
	foreach($objects as $key => &$object) {
		$value = $object->$method();
		if (empty($value)) {
			$negate and $return[$key] = $object;
		} else if (! $negate) {
			$return[$key] = $object;
		}
	}
	return $return;
}

/**
 * Retrieves a key from an array given its position - "first", "last", or an integer.
 * 
 * If given a positive integer, returns the key in the given position.
 * e.g. 1 returns the first key, 2 the second, 3 the third, etc.
 * 
 * If given a negative integer, returns the key that would correspond to the absolute 
 * value of the given position working backwards in the array.
 * e.g. -1 returns the last key, -2 the second to last, -3 the third to last, etc.
 * 
 * Somewhat oddly, if given 0, the first key is returned (the same as passing 1 or "first").
 * 
 * @param array $array Associative array.
 * @param string|int $pos Position of key to return - "first", "last", or integer.
 * @return scalar Key in given position, if found, otherwise null.
 */
function array_key(array $array, $pos) {
		
	if (! is_int($pos)) {
		
		switch($pos) {
			case 'first' :
				reset($array);
				break;
			case 'last' :
				end($array);
				break;
			default :
				$msg = 'Key position expected to be "first", "last", or integer - given "'.gettype($pos).'".';
				throw new InvalidArgumentException($msg);
			}
		
		return key($array);
	}

	$test = array_keys($array);
	
	if ($pos <= 0) {
		$pos = abs($pos);
		$test = array_reverse($test, false);
	}
	
	return isset($test[$pos-1]) ? $test[$pos-1] : null;	
}

if (! function_exists('array_mergev')) :
	
	/**
	 * Merges a vector of arrays.
	 * 
	 * More performant than using array_merge in a loop.
	 * 
	 * @author facebook/libphutil
	 * 
	 * @param array $arrays Array of arrays to merge.
	 * @return array Merged arrays.
	 */
	function array_mergev(array $arrays) {
		if (! $arrays) {
			return array();
		}
		return call_user_func_array('array_merge', $arrays);
	}

endif;

/**
 * Merge arrays into an array by reference.
 * 
 * @example
 * $a = array('One', 'Two');
 * $b = array('Three', 'Four');
 * $c = array('Five', 'Six');
 * 
 * array_merge_ref($a, $b, $c);
 * 
 * $a is now: array('One', 'Two', 'Three', 'Four', 'Five', 'Six');
 * 
 * @param array &$array Array to merge other arrays into.
 * @param ... Arrays to merge.
 * @return array Given arrays merged into the first array.
 */
function array_merge_ref(array &$array /*, $array1 [, ...] */){
	return $array = call_user_func_array('array_merge', func_get_args());
}

/**
 * Filters an array by key.
 * 
 * Like array_filter(), except that it operates on keys rather than values.
 * 
 * @example
 * $array = array(1 => 'One', 2 => 'Two', '3' => 'Three', 'Four' => 'Four');
 * 
 * $newArray = array_filter_keys($array, 'is_numeric');
 * 
 * $newArray is now: array(1 => 'One', 2 => 'Two', '3' => 'Three');
 * 
 * @param array $input Array to filter by key.
 * @param callable|null $callback Callback filter. Default null (removes empty keys).
 * @return array Key/value pairs of $input having the filtered keys.
 */
function array_filter_keys(array $input, $callback = null) {
	$filtered = array_filter(array_keys($input), $callback);
	return empty($filtered) ? array() : array_intersect_key($input, array_flip($filtered));
}

/**
 * Applies a callback function to each key in an array.
 * 
 * @example
 * $array = array('first' => 1, 'second' => 2, 'third' => 3);
 * 
 * $newArray = array_map_keys('ucfirst', $array);
 * 
 * $newArray is now: array('First' => 1, 'Second' => 2, 'Third' => 3);
 * 
 * @param callable $callback Callback to apply to each array key.
 * @param array $array Associative array.
 * @return array A new array with the callback applied to each key.
 */
function array_map_keys($callback, array $array) {
	return array_combine(array_map($callback, array_keys($array)), array_values($array));
}

/**
 * Case-insensitive in_array().
 * 
 * @param string $needle Needle
 * @param array $haystack Haystack
 */
function in_arrayi($needle, $haystack) {
	return in_array(strtolower($needle), array_map('strtolower', $haystack));
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
 * Checks if all values of array are instances of the passed class.
 * Throws InvalidArgumentException if it isn't true for any value.
 *
 * @param array
 * @param string Name of the class.
 * @param boolean Whether to throw exceptions for invalid values. Default false.
 * @return boolean True if all objects are instances of given class, otherwise false.
 * @throws InvalidArgumentException
 */
function is_array_instances(array $arr, $class, $throw_exceptions = false) {
	foreach ( $arr as $key => $object ) {
		if (! $object instanceof $class) {
			if ($throw_exceptions) {
				$given = gettype($object);
				is_object($object) and $given = 'instance of '.get_class($object);
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
function is_array_arrays(array $arr, $throw_exceptions = false) {
	foreach($arr as $key => &$item) {
		if (! is_array($item)) {
			if ($throw_exceptions) {
				$msg = "Array item with key '{$key}' must be of type array, ".gettype($item).' given.';
				throw new InvalidArgumentException($msg);
			} else {
				return false;
			}
		}
	}
	return true;
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

/**
 * array_column() back-compat (PHP < 5.5)
 */
if (! function_exists('array_column')) :
	
	function array_column(array $arrays, $column_key, $index_key = null) {
		$return = array();
		foreach($arrays as &$arr) {
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
