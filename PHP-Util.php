<?php
/**
 * PHP-Util - PHP utility library.
 * 
 * @license MIT
 * @author wells
 * @version 0.2.6
 * 
 * Sections:
 * 	- Miscellaneous
 * 	- Scalar handling
 * 	- Array handling
 *  - Network
 * 	- Filesystem
 * 	- Callables
 * 	- XML
 * ------------------
 * Changelog:
 * 	0.2.6 (4/25/14)
 * 		- Add str_between()
 * 		- Add str_sentences()
 * 		- Add esc_alnum()
 * 		- Add in_arrayi()
 *	0.2.5 (4/20/14)
 * 		- Add getcookie()
 * 		- Add base64_url_encode()
 * 		- Add base64_url_decode()
 *	0.2.4 (4/16/14)
 * 		- Add parents and interfaces options to classinfo()
 * 		- Change 'endswith()' to 'str_endswith()'
 * 		- Change 'startswith()' to 'str_startswith()'
 * 	0.2.3 (4/14/14)
 * 		- Add file_extension()
 * 		- Change fwritecsv() to file_put_csv()
 * 		- Add file_get_csv()
 * 	0.2.2 (4/12/14) 
 * 		- Fix classinfo() bitwise
 * 		- Add is_win()
 * 		- Add optional row callback param to fwritecsv()
 * 	0.2.1 (4/11/14) 
 * 		- Add XML functions; create package
 */

/** ================================
 		  MISCELLANEOUS
================================= */

define('CLASSINFO_VENDOR', 4);
define('CLASSINFO_NAMESPACES', 8);
define('CLASSINFO_CLASSNAME', 16);
define('CLASSINFO_BASIC', CLASSINFO_VENDOR | CLASSINFO_NAMESPACES | CLASSINFO_CLASSNAME);

define('CLASSINFO_PARENTS', 32);
define('CLASSINFO_INTERFACES', 64);
define('CLASSINFO_ALL', CLASSINFO_BASIC | CLASSINFO_PARENTS | CLASSINFO_INTERFACES);

/**
 * Retrieve information about a class.
 * 
 * Much like pathinfo(), it will return only information that 
 * is available, unless a particular item(s) is specified.
 * 
 * Returns, if available, as part of the array:
 * 	1. 'vendor' (string) the top-level namespace name.
 *  2. 'namespaces' (array) the "middle" namespaces, 0-indexed.
 *  3. 'class' (string) the base classname, always available. 
 * 
 * @param string|object $class Classname or object to get info on.
 * @param int $flag Bitwise CLASSINFO_* flags. Default CLASSINFO_BASIC.
 * @return string|array String if flag given and found, otherwise array of info.
 */
function classinfo($class, $flag = CLASSINFO_BASIC) {
	
	$info = array();
	
	if (! is_string($class)) {
		$class = get_class($class);	
	} else {
		$class = trim($class, '\\');
	}
	
	if (false === strpos($class, '\\')) {
		if ($flag === CLASSINFO_CLASSNAME) {
			return $class;
		}
		$info['class'] = $class;
		if ((! CLASSINFO_INTERFACES & $flag) && (! CLASSINFO_PARENTS & $flag)) {
			return $info;
		}
	}
	
	$parts = explode('\\', $class);
	$num = count($parts);
	
	if ($flag === CLASSINFO_CLASSNAME) {
		return $parts[$num-1];
	}
	
	if ($flag === CLASSINFO_VENDOR) {
		return $parts[0];
	} 
	
	if ($flag & CLASSINFO_VENDOR) {
		$info['vendor'] = $parts[0];
	}
	
	if ($num > 2 && ($flag & CLASSINFO_NAMESPACES)) {
		$info['namespaces'] = array();
		for ($i=1; $i < $num-1; $i++) {
			$info['namespaces'][] = $parts[$i];
		}
	}
	
	if ($flag === CLASSINFO_NAMESPACES) {
		return isset($info['namespaces']) ? $info['namespaces'] : null;
	}
	
	if ($flag & CLASSINFO_CLASSNAME) {
		$info['class'] = $parts[$num-1];
	}
	
	if ($flag & CLASSINFO_PARENTS) {
		$info['parents'] = array_values(class_parents($class));
		if ($flag === CLASSINFO_PARENTS) {
			return $info['parents'];
		}
	}
	
	if ($flag & CLASSINFO_INTERFACES) {
		$info['interfaces'] = array_values(class_implements($class));
		if ($flag === CLASSINFO_INTERFACES) {
			return $info['interfaces'];
		}
	}
	
	return $info;
}

/**
 * Defines a constant if not yet defined.
 * 
 * @param string $const Name of constant
 * @param scalar $value Value to define constant if undefined.
 * @return void
 */
function define_default($const, $value) {
	if (! defined($const)) {
		define($const, $value);
	}
}

/**
 * Returns true if server OS is Windows.
 * 
 * @return boolean True if server OS is Windows, otherwise false.
 */
function is_win() {
	return '\\' === DIRECTORY_SEPARATOR;
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

/** ================================
			SCALAR TYPES
================================= */

define('SCALAR_FORCE_STRING', 1);
define('SCALAR_CAST_NUMERIC', 2);
define('SCALAR_IGNORE_ERR', 4);

/**
 * Convert value to a scalar value.
 *
 * @param string Value we'd like to be scalar.
 * @param int $flags SCALARVAL_* flag bitwise mask.
 * @return string
 * @throws InvalidArgumentException if value can not be scalarized.
 */
function scalarval($var, $flags = 0) {
	
	switch (gettype($var)) {
		case 'string' :
			return ($flags & SCALAR_CAST_NUMERIC) ? cast_numeric($var) : $var;
		case 'double' :
		case 'integer' :
			return ($flags & SCALAR_FORCE_STRING) ? strval($var) : $var;
		case 'NULL' :
			return '';
		case 'boolean' :
			return ($flags & SCALAR_FORCE_STRING) ? ($var ? '1' : '0') : ($var ? 1 : 0);
		case 'object' :
			if (method_exists($var, '__toString')) {
				return $var->__toString();
			}
	}
	
	if ($flags & SCALAR_IGNORE_ERR) {
		return '';
	}
	
	throw new InvalidArgumentException('Value can not be scalar - given '.gettype($var));
}

/**
 * Sanitizes a string using filter_var() with FILTER_SANITIZE_STRING.
 * 
 * @param scalar $val String to filter.
 * @param int $filter_flags Bitwise FILTER_FLAG_* flags. Default: 0
 * @return string Sanitized string.
 */
function esc_string($val, $filter_flags = 0) {
	return filter_var($val, FILTER_SANITIZE_STRING, $filter_flags);
}

/**
 * Sanitizes a string using filter_var(), stripping non-ASCII characters (>127).
 * 
 * @param scalar $val Scalar value to escape.
 * @param string String containing only ASCII chars.
 */
function esc_ascii($val) {
	return filter_var($val, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
}

/**
 * Strips a string of non-alphanumeric characters.
 * 
 * @param string $string String to sanitize
 * @param string|null $extras Characters to allow in addition to alnum chars.
 * @return string Sanitized string containing only alnum (and any extra) characters.
 */
function esc_alnum($string, $extras = null) {
	if (! isset($extras) && ctype_alnum($string)) {
		return $string;
	}
	$pattern = '/[^a-zA-Z0-9'. (isset($extras) ? $extras : '') .']/';
	return preg_replace($pattern, '', $string);
}

/**
 * Sanitizes a float using filter_var() with FILTER_SANITIZE_NUMBER_FLOAT.
 * 
 * @param scalar $val Value to filter.
 * @param int $filter_flags Bitwise FILTER_FLAG_* flags. Default: 0
 * @return float Sanitized float value.
 */
function esc_float($val, $filter_flags = 0) {
	return filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, $filter_flags);
}

/**
 * Sanitizes an integer using filter_var() with FILTER_SANITIZE_NUMBER_INT.
 * 
 * @param scalar $val Value to filter.
 * @param int $filter_flags Bitwise FILTER_FLAG_* flags. Default: 0
 * @return int Sanitized integer value.
 */
function esc_int($val, $filter_flags = 0) {
	return filter_var($val, FILTER_SANITIZE_NUMBER_INT, $filter_flags);
}

/**
 * If $val is a numeric string, converts to float or integer depending on 
 * whether a decimal point is present. Otherwise returns original.
 * 
 * @param string $val If numeric string, converted to integer or float.
 * @return scalar Value as string, integer, or float.
 */
function cast_numeric($val) {
	if (is_numeric($val) && is_string($val)) {
		return false === strpos($val, DECIMAL_POINT) 
			? intval($val)
			: floatval($val);
	}
	return $val;
}

/**
 * Removes all found instances of a string from a string.
 * 
 * Note the function uses str_replace(), so an array may be
 * passed for the charlist parameter.
 * 
 * @param string|array $char Char(s) to search and destroy.
 * @param string $subject String to search within.
 * @return string String with chars removed.
 */
function str_strip($char, $subject) {
	return str_replace($char, '', $subject);
}

/**
 * Returns true if $haystack starts with $needle.
 * 
 * @param string $haystack String to search within.
 * @param string $needle String to find.
 * @param boolean $casei True for case-insensitive search. 
 * @return boolean 
 */
function str_startswith($haystack, $needle, $match_case = true) {
	return $match_case
		? 0 === strpos($haystack, $needle)
		: 0 === stripos($haystack, $needle);
}

/**
 * Returns true if $haystack ends with $needle.
 * 
 * @param string $haystack String to search within.
 * @param string $needle String to find.
 * @return boolean 
 */
function str_endswith($haystack, $needle, $match_case = true) {
	return $match_case
		? 0 === strcmp($needle, substr($haystack, -strlen($needle)))
		: 0 === strcasecmp($needle, substr($haystack, -strlen($needle)));
}

/** 
 * Returns 1st occurance of text between two strings.
 * 
 * The "between" strings are not included in output.
 * 
 * @param string $string The string in which to search.
 * @param string $substr_start The starting string.
 * @param string $substr_end The ending string.
 * @return string Text between $start and $end. 
 */
function str_between($string, $substr_start, $substr_end) {
	$str1 = explode($substr_start, $string);
	$str2 = explode($substr_end, $str1[1]);
	return trim($str2[0]);	
}

/**
 * Get a given number of sentences from a string.
 *
 * @param string $text The full string of sentences.
 * @param integer $num Number of sentences to return.
 * @param boolean|array $strip Whether to strip abbreviations (they break the function).
 * Pass an array to account for those abbreviations as well. See function body.
 * @return string Given number of sentences.
 */
function str_sentences($text, $num, $strip = false) {
	$text = strip_tags($text);
	// shall we strip?
	if ($strip) {
		// brackets are for uniqueness - if we just removed the 
		// dots, then "Mr" would match "Mrs" when we reconvert.
		$replace = array(
			'Dr.' => '<Dr>',
			'Mrs.' => '<Mrs>',
			'Mr.' => '<Mr>',
			'Ms.' => '<Ms>',
			'Co.' => '<Co>',
			'Ltd.' => '<Ltd>',
			'Inc.' => '<Inc>',
		);
		// add extra strings to strip
		if (is_array($strip)) {
			foreach($strip as $s) {
				$replace[$s] = '<'.str_replace('.', '', $s).'>';	
			}
		}
		// replace with placeholders and set the key/value vars
		$text = str_replace(
			$replace_keys = array_keys($replace), 
			$replace_vals = array_values($replace), 
			$text
		);
	}
	// get given number of strings delimited by ".", "!", or "?"
	preg_match('/^([^.!?]*[\.!?]+){0,'.$num.'}/', $text, $match);
	// replace the placeholders with originals
	return $strip ? str_replace($replace_vals, $replace_keys, $match[0]) : $match[0];
}

/**
 * Strips "/" and "\" from beginning and end of string.
 * 
 * @param string $str Path
 * @return string Path with no slashes before or after.
 */
function unslash($str) {
	return trim($str, '/\\');
}

/**
 * Format bytes to SI or binary (IEC) units.
 * 
 * @param int $bytes Number of bytes.
 * @param boolean $binary Whether to use binary (IEC) units. Default false.
 * @return string Formatted bytes with abbreviated unit.
 */
function bytes_format($bytes, $binary = false) {
	
	if ($binary) {
		$prefix = array('B', 'Ki', 'Mi', 'Gi', 'Ti', 'Ei', 'Zi', 'Yi');
		$base = 1024;
	} else {
		$prefix = array('B', 'k', 'M', 'G', 'T', 'E', 'Z', 'Y');
		$base = 1000;
	}

	$idx = min(intval(log($bytes, $base)), count($prefix)-1);
    
	return sprintf('%1.4f', $bytes/pow($base, $idx)) .' '. $prefix[$idx].'B';
}

/** ================================
		  TOKENS/NONCES
================================= */

/**
 * Generates a verifiable token from seed.
 * 
 * Uses HASH_ALGO_DEFAULT constant, if defined, as default hash algorithm.
 * Uses HASH_HMAC_KEY constant, if defined, as hash hmac key.
 * 
 * @param string $seed String used to create hash token.
 * @param string $algo Hash algorithm to use (default 'sha1').
 * @return string Token using hash_hmac().
 */
function generate_token($seed, $algo = null) {
	
	if (null === $algo) {
		if (defined('TOKEN_DEFAULT_ALGO')) {
			$algo = TOKEN_DEFAULT_ALGO;
		} else {
			$algo = 'sha1';
		}
	}
	
	if (defined('TOKEN_HMAC_KEY')) {
		$hmac_key = TOKEN_HMAC_KEY;
	} else {
		$hmac_key = '4tj;,#K3+H%&a?*c*K8._O]~K_~XD &h3#I/pv#Rtoi,Iul84I/kg*J=Kk8sbGIa';
	}
	
	return hash_hmac($algo, $seed, $hmac_key);
}

/**
 * Verifies a token with seed.
 */
function verify_token($token, $seed, $algo = null) {
	return $token === generate_token($seed, $algo);
}

/** ================================
		  NETWORK
================================= */

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
 * Base64 encode a string, safe for URL's.
 * 
 * Designed to work with other language's implementations
 * of Base64-url. Bug fixes appreciated.
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
	if ($m4 = strlen($str) % 4) {
		$str .= substr('====', $m4);
	}
	return base64_decode($str);
}

/** ================================
			FILESYSTEM
================================= */

/**
 * Converts backslashes ("\") to forward slashes ("/") and strips 
 * slashes from both ends of the given string.
 * 
 * Useful for normalizing Windows filepaths, or converting class 
 * namespaces to filepaths.
 * 
 * @param string $path String path (usually a filesystem path).
 * @return string Clean path.
 */
function cleanpath($path) {
	return trim(str_replace('\\', '/', $path), '/');
}

/**
 * Joins given filepaths into one concatenated string.
 * 
 * @param string ... Paths to join
 * @return string Joined path.
 */
function joinpath($path1/* [, $path2 [, ...]] */) {
	return implode(DIRECTORY_SEPARATOR, array_map('unslash', func_get_args()));
}

/**
 * Returns the full file extension for a given file path.
 * 
 * "Fixes" the behavior of pathinfo(), which returns only the last extension.
 * 
 * Example:
 * 
 * 	pathinfo("somefile.tar.gz", PATHINFO_EXTENSION); 
 * 		returns "gz"
 * 
 * 	file_extension("somefile.tar.gz");
 * 		returns "tar.gz"
 * 
 * @param string $path Filepath (does not need to exist on filesystem).
 * @return string File extension.
 */
function file_extension($path) {
	if (2 > substr_count($path, '.')) {
		return substr(strrchr($path, '.'), 1); // faster than pathinfo() with const
	}
	$info = pathinfo($path, PATHINFO_FILENAME|PATHINFO_EXTENSION); // filename minus name plus extension
	return substr($info['filename'], strpos($info['filename'], '.')+1).'.'.$info['extension'];
}

/**
 * Returns true if given an absolute path, otherwise false.
 * 
 * @param string $path Filesystem path
 * @return boolean True if path is absolute, otherwise false.
 */
function is_abspath($path) {
	// Absolute paths on Windows must start with letter (local) 
	// or a double backslash (network)
	if ('\\' === DIRECTORY_SEPARATOR) {
		return 0 === strpos($path, '\\\\')
			? true
			// backslash is default escape char in fnmatch()
			: fnmatch('?:[/|\]*', $path, FNM_NOESCAPE);
	} else {
		return file_exists($path);
	}
}

/**
 * Writes an array of data as rows to a CSV file.
 * 
 * @param string|resource	Writable filepath, or a file resource with write access.
 * @param array				Array of data to write as CSV to file.
 * @param callable $row_cb	[Optional] Callback run for each row; Callback is passed 
 * 							each row data array. If modification is desired, define
 * 							the first callback parameter by reference.
 * @return boolean			True if success, false and error if unwritable file.
 */
function file_put_csv($file, array $data, $row_callback = null) {
	if (! is_resource($file)) {
		if (! is_writable($file)) {
			trigger_error("Cannot write CSV to unwritable file '$file'.");
			return false;
		}
		$file = fopen($file, 'wb');
	}
    foreach ($data as $i => $row) {
    	if (isset($row_callback)) {
    		$row_callback($row, $i);
    	}
        fputcsv($file, $row);
	}
    fclose($file);
	return true;
}

/**
 * Reads a CSV file and returns rows as an array.
 * 
 * @param string|resource $file			File path or handle opened with read capabilities.
 * @param boolean $first_row_is_data	Whether the first row is data (otherwise, used
 * 										as header names). Default false.
 * @param string|null $column_as_key	Column index to use as row array key. Default null.
 * @return array Array of rows, associative if $column_as_key given, otherwise indexed.
 */
function file_get_csv($file, $first_row_is_data = false, $column_as_key = null) {
	if (! is_resource($file)) {
		if (! is_readable($file)) {
			trigger_error("Cannot read CSV from unreadable file '$file'.");
			return false;
		}
		$file = fopen($file, 'rb');
	}
	if (! $first_row_is_data) {
		$headers = fgetcsv($file);
	}
	$rows = array();
	while ($line = fgetcsv($file)) {
		if (isset($headers)) {
			$data = array_combine($headers, $line);
		} else {
			$data =& $line;
		}
		if (isset($column_as_key) && isset($line[$column_as_key])) {
			$rows[$line[$column_as_key]] = $data;
		} else {
			$rows[] = $data;
		}
	}
	fclose($file);
	return $rows;
}

/**
 * Returns files & directories in a given directory recursively.
 * 
 * Returned array is flattened - both keys and values are full filesystem paths.
 * 
 * Faster than scandir_recursive(), but can consume lots of resources if used 
 * excessively with deep recursion.
 * 
 * Uses glob marking with substr() to check for subdirectories, which runs 
 * about twice as fast as the same function using is_dir()
 * 
 * @param string $dir Directory to scan.
 * @param int $levels Max directory depth level.
 * @param array &$glob The glob of flattend paths.
 * @param int $level Current directory level. Used interally.
 * @return array Flattened assoc. array of filepaths.
 */
function glob_recursive($dir, $levels = 5, array &$glob = array(), $level=1) {
	$dir = rtrim($dir, '/\\').'/*';
	foreach( glob($dir, GLOB_MARK) as $item ) {
		// glob with GLOB_MARK uses system dir sep
		if ($level <= $levels && DIRECTORY_SEPARATOR === substr($item, -1)) {
			$level++;
			glob_recursive($item, $levels, $glob, $level);
		} else {
			$glob[$item] = $item;
		}
	}
	return $glob;
}

/**
 * Returns files & directories in a given directory, optionally recursive.
 *
 * Returned array is multi-dimensional with directory/file names used as keys.
 * 
 * @param string $dir Directory to scan.
 * @param int $levels Max directory depth level.
 * @param int $level Current depth level.
 * @return array Multi-dimensional array of files and directories.
 */
function scandir_recursive($dir, $levels = 5, $level=1) {
	$dir = rtrim($dir, '/\\').'/';
	$dirs = array();
	foreach( scandir($dir) as $item ) {
		if ('.' !== $item && '..' !== $item) {
			if ($level <= $levels && is_dir($dir.$item)) {
				$level++;
				$dirs[$item] = scandir_recursive($dir.$item, $levels, $level);
			} else {
				$dirs[$item] = $dir.$item;
			}
		}
	}
	return $dirs;
}
	
/**
 * Includes a file using include().
 * 
 * Useful for classes to include files in isolated scope
 * without resorting to closures.
 * 
 * @param string $file Path to file.
 * @param array $localize [Optional] Associative array of variables
 * to localize using extract(). Default null.
 * @return void
 */
function include_file($file, array $localize = null) {
	if (isset($localize)) {
		extract($localize, EXTR_REFS);
	}
	include $file;
}

/** ================================
 		  ARRAYS/ITERATION
================================= */

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

/**
 * array_column() back-compat (PHP < 5.5)
 */
if (! function_exists('array_column')) {
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
}

/** ================================
 		  CALLABLES
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
	
	if ($callback instanceof Closure || is_string($callback)) {
		$refl = new ReflectionFunction($callback);
		$type = 'func';
	} else if (is_array($callback)) {
		$refl = new ReflectionMethod($callback[0], $callback[1]);
		$type = 'method';
	} else if (is_object($callback)) {
		$refl = new ReflectionMethod(get_class($callback), '__invoke');
		$type = 'object';
	} else {
		throw new LogicException("Unknown callback type, given ".gettype($callback));
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
			throw new RuntimeException("Missing parameter '$param'.");
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
	$xml = new XMLWriter();
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
function xml_write_element(XMLWriter $xml, array $data) {

	foreach ( $data as $key => $value ) {
		
		if (! ctype_alnum($key)) {
			$key = strip_tags(str_replace(array(' ', '-', '/', '\\'), '_', $key));
		}
		
		if (is_numeric($key)) {
			$key = 'key_'. (int)$key;
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
