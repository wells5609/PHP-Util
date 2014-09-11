<?php
/**
 * @package wells5609\php-util
 * 
 * @subpackage Array
 * 
 *  * object_to_array
 *  * array_to_object
 *  * array_get
 *  * array_set
 *  * array_unset
 *  * array_isset
 *  * array_filter_keys
 *  * array_map_keys
 *  * array_map_assoc
 *  * array_validate
 *  * array_where
 *  * array_select
 *  * array_key
 *  * array_pull
 *  * array_mpull
 *  * array_ppull
 *  * array_mfilter
 *  * array_pfilter
 *  * array_kfilter
 *  * array_mergev
 *  * array_merge_ref
 *  * array_build
 *  * to_array
 *  * in_arrayi
 *  * is_iterable
 *  * implode_nl
 */

if (! function_exists('object_to_array')) :
	
	/**
	 * Returns an associative array from an object.
	 * 
	 * @param object $object
	 * @return array
	 */
	function object_to_array($object) {
		
		if (method_exists($object, 'toArray')) {
			return $object->toArray();
		}
		
		if ($object instanceof \Traversable) {
			return iterator_to_array($object);
		}
		
		return get_object_vars($object);
	}

endif;

if (! function_exists('array_to_object')) :
	
	/**
	 * Converts an array to an object (stdClass), optionally recursively.
	 * 
	 * @param array $array Array to convert to object.
	 * @param boolean $recursive [Optional] Whether to convert recursively. Default false.
	 * @return object Object containing the array's keys/values as properties.
	 */
	function array_to_object(array $array, $recusive = false) {
		
		$object = new \stdClass;
		
		foreach($array as $key => $value) {
			
			if (is_array($value)) {
				$value = $recusive ? array_to_object($value, $recusive) : (object)$value;
			
			} else if (is_object($value) && ! $value instanceof \stdClass) {
				$value = object_to_array($value);
				$value = $recusive ? array_to_object($value, $recusive) : (object)$value;
			}
			
			$object->$key = $value;
		}
		
		return $object;
	}

endif;

if (! function_exists('array_get')) :
	
	/**
	 * Retrieves a value from an array given its path in dot notation.
	 * 
	 * @param array &$array Associative array.
	 * @param string $dotpath Item path given in dot-notation (e.g. "some.nested.item")
	 * @return mixed Value of item if found, otherwise null.
	 */
	function array_get(array &$array, $key) {
		
		if (false === strpos($key, '.')) {
			return isset($array[$key]) ? $array[$key] : null;
		}
		
		$a = &$array;
		
		foreach(explode('.', $key) as $segment) {
		
			if (! isset($a[$segment])) {
				return null;
			}
		
			$a = &$a[$segment];
		}
		
		return $a;
	}

endif;

if (! function_exists('array_set')) :
	
	/**
	 * Sets an array value given its path in dot notation.
	 * 
	 * @param array &$array
	 * @param string $key
	 * @param mixed $value
	 * @return array
	 */
	function array_set(array &$array, $key, $value) {
		
		if (false === strpos($key, '.')) {
			$array[$key] = $value;
			return $array;
		}
		
		$a =& $array;
		
		foreach(explode('.', $key) as $segment) {
			
			isset($a[$segment]) or $a[$segment] = array();
			
			$a =& $a[$segment];
		}
		
		$a = $value;
		
		return $array;
	}
	
endif;

if (! function_exists('array_unset')) :
	
	/**
	 * Unsets an array item given its path in dot notation.
	 * 
	 * @param array &$array Array to search within.
	 * @param string $key Dot-notated path.
	 * @return void
	 */
	function array_unset(&$array, $key) {
		
		if (false === strpos($key, '.')) {
			unset($array[$key]);
			return $array;
		}
		
		$a =& $array;
		
		$segments = explode('.', $key);
		$n = count($segments);
		$i = 1;
		
		foreach($segments as $segment) {
			
			if (! array_key_exists($segment, $a)) {
				return;
			}
			
			if ($i !== $n) {
				$a =& $a[$segment];
				$i++;
			} else {
				unset($a[$segment]);
			}
		}
		
		return $array;
	}

endif;

if (! function_exists('array_isset')) :
		
	/**
	 * Checks whether an array item exists with the given path.
	 * 
	 * Same as: `(bool) array_get($array, $key)`
	 * 
	 * @param array &$array
	 * @param string $key
	 * @return boolean
	 */
	function array_isset(array &$array, $key) {
		
		if (false === strpos($key, '.')) {
			return array_key_exists($key, $array);
		}
		
		$a =& $array;
		
		foreach(explode('.', $key) as $segment) {
		
			if (! array_key_exists($segment, $a)) {
				return false;
			}
		
			$a = &$a[$segment];
		}
		
		return true;
	}

endif;

if (! function_exists('array_filter_keys')) :
		
	/**
	 * Filters an array by key.
	 * 
	 * Like array_filter(), except operates on keys rather than values.
	 * 
	 * @example
	 * 
	 * $a = array(
	 * 		0 => 0, 
	 * 		1 => 1, 
	 * 		"2" => 2, 
	 * 		"Three" => 3
	 * );
	 * 
	 * $a2 = array_filter_keys($a, 'is_numeric');
	 * $a3 = array_filter_keys($a, 'is_numeric', true);
	 * 
	 * // $a2 = array(0 => 0, 1 => 1, "2" => 2);
	 * // $a3 = array("Three" => 3)
	 * 
	 * @param array $input Array to filter by key.
	 * @param callable|null $callback Callback filter. Default null (removes empty keys).
	 * @param boolean $negate Whether to values != $callback(). Default false.
	 * @return array Key/value pairs of $input having the filtered keys.
	 */
	function array_filter_keys(array $input, $callback = null, $negate = false) {
			
		$filtered = array_filter(array_keys($input), $callback);
		
		if ($negate) {
			return empty($filtered) ? $input : array_diff_key($input, array_flip($filtered));
		}
		
		return empty($filtered) ? array() : array_intersect_key($input, array_flip($filtered));
	}
	
endif;

if (! function_exists('array_map_keys')) :
		
	/**
	 * Applies a callback function to each key in an array.
	 * 
	 * @example
	 * $array = array('first' => 1, 'second' => 2, 'third' => 3);
	 * 
	 * $newArray = array_map_keys('ucfirst', $array);
	 * 
	 * $newArray is: array('First' => 1, 'Second' => 2, 'Third' => 3);
	 * 
	 * @param callable $callback Callback to apply to each array key.
	 * @param array $array Associative array.
	 * @return array A new array with the callback applied to each key.
	 */
	function array_map_keys($callback, array $array) {
		return array_combine(array_map($callback, array_keys($array)), array_values($array));
	}
	
endif;

if (! function_exists('array_map_assoc')) :
	
	/**
	 * Applies the callback function to each key/value pair in the array.
	 *
	 * The key and value are passed to the callback as the first and
	 * second arguments, respectively.
	 *
	 * @param callable $callback Callback accepting 2 args: key and value. Returns new value.
	 * @param array $array Associative array to apply callback.
	 * @return array Array with new values, keys preserved.
	 */
	function array_map_assoc($callback, array $array) {
		$map = array();
		foreach ($array as $key => $value) {
			$map[$key] = $callback($key, $value);
		}
		return $map;
	}
	
endif;

if (! function_exists('array_validate')) :
		
	/**
	 * Whether every array value passes the test.
	 *
	 * @param array $array Array.
	 * @param callable $test Callback - must return true or false.
	 * @return boolean True if all values passed the test, otherwise false.
	 */
	function array_validate(array $array, $test) {
		foreach($array as $key => $value) {
			if (! $test($value)) {
				return false;
			}
		}
		return true;
	}
	
endif;

if (! function_exists('array_where')) :
	
	/**
	 * Returns a new array containing only those items which pass the test.
	 * 
	 * @param array $array Array.
	 * @param callable $test Callback passed key and value; must return boolean.
	 * @return array Array of items that passed the test with keys preserved.
	 */
	function array_where(array $array, $test) {
		$match = array();
		foreach($array as $key => $value) {
			if ($test($key, $value)) {
				$match[$key] = $value;
			}
		}
		return $match;
	}

endif;

if (! function_exists('array_select')) :
	
	/**
	 * Returns an array of elements that satisfy the given conditions.
	 * 
	 * @param array $array Array of arrays or objects.
	 * @param array $conditions Associative array of keys/properties and values.
	 * @param string $operator One of 'AND', 'OR', or 'NOT'. Default 'AND'.
	 * @return array Array elements that satisfy the conditions.
	 */
	function array_select(array $array, array $conditions, $operator = 'AND') {
		
		if (empty($conditions)) {
			return $array;
		}
		
		$filtered = array();
		$oper = strtoupper($operator);
		$n = count($conditions);
		
		foreach ($array as $key => $obj) {
			
			$matches = 0;
			
			if (is_array($obj)) {
				foreach($conditions as $mKey => $mVal) {
					if (array_key_exists($mKey, $obj) && $mVal == $obj[$mKey]) {
						$matches++;
					}
				}
			} else if (is_object($obj)) {
				foreach($conditions as $mKey => $mVal) {
					if (isset($obj->$mKey) && $mVal == $obj->$mKey) {
						$matches++;
					}
				}
			}
			
			if (('AND' === $oper && $matches == $n) 
				|| ('OR' === $oper && $matches > 0) 
				|| ('NOT' === $oper && 0 == $matches) 
			) {
				$filtered[$key] = $obj;
	        }
		}
		
		return $filtered;
	}
	
endif;

if (! function_exists('array_key')) :
		
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
	 * If given 0, null is returned.
	 * 
	 * @param array $array Associative array.
	 * @param string|int $pos Position of key to return - "first", "last", or non-zero integer.
	 * @return scalar Key in given position, if found, otherwise null.
	 */
	function array_key(array $array, $pos) {
		
		if ("0" == $pos) {
			return null;
		}
		
		if ('first' === $pos) {
			reset($array);
			return key($array);
		} else if ('last' === $pos) {
			end($array);
			return key($array);
		} else if (! is_numeric($pos)) {
			throw new InvalidArgumentException('Position must be "first", "last", or int, given: '.gettype($pos));
		}
		
		$pos = (int) $pos;
		$keys = array_keys($array);
		
		if ($pos < 0) {
			$pos = abs($pos);
			$keys = array_reverse($keys, false);
		}
		
		return isset($keys[$pos-1]) ? $keys[$pos-1] : null;	
	}
	
endif;

if (! function_exists('array_pull')) :
		
	/**
	 * Pulls a value from each array in an array by key/index and returns an array of the values.
	 * 
	 * @param array $arrays Array of arrays.
	 * @param string|int $index Index offset or key name to pull from each array.
	 * @param string|null $key_index [Optional] Index/key to use for keys in returned array.
	 * @return array Indexed array of the value pulled from each array.
	 */
	function array_pull(array $arrays, $index, $key_index = null) {
		$return = array();
		foreach($arrays as $key => $array) {
			if (null !== $key_index) {
				$key = $array[$key_index];
			}
			$return[$key] = (null === $index) ? $array : $array[$index];
		}
		return $return;
	}
	
endif;

if (! function_exists('array_mpull')) :
	
	/**
	 * Calls a method on each object in an array and returns an array of the results.
	 * 
	 * @param array $objects Array of objects.
	 * @param string|null $method Method to call on each object, or null to return whole object.
	 * @param string|null $key_method [Optional] Method used to get keys used in returned array.
	 * @return array Indexed array of values returned from each object.
	 */
	function array_mpull(array $objects, $method, $key_method = null) {
		$return = array();
		foreach($objects as $key => &$obj) {
			if (null !== $key_method) {
				$key = $obj->$key_method();
			}
			$return[$key] = (null === $method) ? $obj : $obj->$method();
		}
		return $return;
	}
	
endif;

if (! function_exists('array_ppull')) :

	/**
	 * Pulls a property from each object in an array and returns an array of the values.
	 * 
	 * @param array $objects Array of objects.
	 * @param string $property Name of property to get from each object, or null for whole object.
	 * @param string|null $key_prop [Optional] Property to use for keys in returned array.
	 * @return array Indexed array of property value from each object.
	 */
	function array_ppull(array $objects, $property, $key_prop = null) {
		$return = array();
		foreach($objects as $key => $obj) {
			if (null !== $key_prop) {
				$key = $obj->$key_prop;
			}
			$return[$key] = (null === $property) ? $obj : $obj->$property;
		}
		return $return;
	}

endif;

if (! function_exists('array_mfilter')) :
		
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
				
			$val = $object->$method();
			
			if (empty($val)) {
				$negate and $return[$key] = $object;
		
			} else if (! $negate) {
				$return[$key] = $object;
			}
		}
		
		return $return;
	}
	
endif;

if (! function_exists('array_pfilter')) :
	
	/**
	 * Array property filter
	 */
	function array_pfilter(array $objects, $property, $negate = false) {
			
		$return = array();
		
		foreach($objects as $key => &$object) {
		
			if (! isset($object->$property)) {
				$negate and $return[$key] = $object;
		
			} else if (! $negate) {
				$return[$key] = $object;
			}
		}
		
		return $return;
	}
	
endif;

if (! function_exists('array_kfilter')) :
		
	/**
	 * Array key filter
	 */
	function array_kfilter(array $arrays, $key, $negate = false) {
			
		$return = array();
		
		foreach($arrays as $index => &$array) {
		
			if (! isset($array[$key])) {
				$negate and $return[$index] = $array;
		
			} else if (! $negate) {
				$return[$index] = $array;
			}
		}
		
		return $return;
	}

endif;

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
		return empty($arrays) ? array() : call_user_func_array('array_merge', $arrays);
	}

endif;

if (! function_exists('array_merge_ref')) :
		
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
	
endif;

if (! function_exists('array_build')) :
	
	/**
	 * Builds an array from an array or object so that all non-scalar items are arrays.
	 * 
	 * @param mixed $thing
	 * @return array
	 */
	function array_build($thing) {
		
		$array = array();
		
		foreach($thing as $key => $value) {
			
			if (is_array($value) || is_object($value)) {
				$array[$key] = array_build($value);
				
			} else {
				
				if (! is_scalar($value)) {
					$array[$key] = is_bool($value) ? $value : (string)$value;
				
				} else if (is_numeric($value)) {
					$array[$key] = (false === strpos($value, '.')) ? (int)$value : (float)$value;
			
				} else if ($value === 'true' || $value === 'false') {
					$array[$key] = ($value === 'true');
			
				} else {
					$array[$key] = (string)$value;
				}
			}
		}
	
		return $array;
	}

endif;

if (! function_exists('to_array')) :
		
	/**
	 * Converts an array, object, or string to an array.
	 * 
	 * @param mixed $thing Array, object, or string (JSON, serialized, or XML).
	 * @return array
	 */
	function to_array($thing) {
		
		if (is_array($thing)) {
			return array_build($thing);
		}
		
		if (is_object($thing)) {
			return array_build(object_to_array($thing));
		}
		
		if (is_string($thing)) {
				
			if (is_json($thing)) {
				return json_decode($thing, true);
			}
			
			if (is_serialized($thing)) {
				return to_array(unserialize($thing));
			}
			
			if (is_xml($thing)) {
				return xml_decode($thing, true);
			}
		}
		
		return (array) $thing;
	}
	
endif;

if (! function_exists('in_arrayi')) :
	
	/**
	 * Case-insensitive in_array().
	 * 
	 * @param string $needle Needle
	 * @param array $haystack Haystack
	 */
	function in_arrayi($needle, $haystack) {
		return in_array(strtolower($needle), array_map('strtolower', $haystack));
	}

endif;

if (! function_exists('is_iterable')) :
	
	/**
	 * Returns true if value can be used in a foreach() loop.
	 * 
	 * @param wil $var Thing to check if iterable.
	 * @return boolean True if var is array or Traversable, otherwise false.
	 */
	function is_iterable($var) {
		return (is_array($var) || $var instanceof \Traversable || $var instanceof \stdClass);
	}
	
endif;

if (! function_exists('implode_nl')) :
	
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
	function implode_nl(array $array, $separator = ", ", $last_separator = ", and ") {
		
		if (1 === count($array)) {
			return reset($array);
		}
		
		$last_value = array_pop($array);
		
		return implode($separator, $array).$last_separator.$last_value;
	}
	
endif;

