<?php

/** ================================
		  TOKENS/NONCES
================================= */

/**
 * Generates a verifiable token from seed.
 */
function generate_token($seed, $algo = null) {
	
	if (null === $algo) {
		define_default('HASH_ALGO_DEFAULT', 'sha1');
		$algo = HASH_ALGO_DEFAULT;
	}
	
	define_default('HASH_HMAC_KEY', '1<Kjia6~?qxg*|!RLg<E!*TwB%yq)Fa77O:F))>%>Lp/vw-T1QF!Qm6rFWz1X3bQ');
	
	return hash_hmac($algo, $seed, HASH_HMAC_KEY);
}

/**
 * Verifies a token using seed.
 */
function verify_token($token, $seed, $algo = null) {
	return $token === generate_token($seed, $algo);
}

/** ================================
			SCALAR TYPES
================================= */

/**
 * Can value be converted to a scalar value?
 *
 * @param string Value we'd like to be scalar.
 * @return boolean
 */
function can_be_scalar($var) {
	switch (gettype($var)) {
		case 'string' :
		case 'NULL' :
		case 'boolean' :
		case 'double' :
		case 'integer' :
			return true;
		case 'object' :
			if (method_exists($var, '__toString')) {
				return true;
			}
			return false;
		case 'array' :
		case 'resource' :
		case 'unknown type' :
		default :
			return false;
	}
}

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
 * Returns true if $email is a valid e-mail address, otherwise false.
 * @uses filter_var() with FILTER_VALIDATE_EMAIL
 * @param string $email Email to validate
 * @return boolean True if valid email, otherwise false.
 */
function validate_email($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);
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
 * &Alias of esc_int()
 */
function esc_integer($val, $flags = 0) {
	return esc_int($val, $flags);
}

/**
 * If $val is a numeric string, converts to float or integer depending on 
 * whether a decimal point is present. Otherwise returns original.
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
 * @return boolean 
 */
function startswith($haystack, $needle) {
	return 0 === strpos($haystack, $needle);
}

/**
 * Returns true if $haystack ends with $needle.
 * 
 * @param string $haystack String to search within.
 * @param string $needle String to find.
 * @return boolean 
 */
function endswith($haystack, $needle) {
	return $needle === substr($haystack, -strlen($needle));
}

/**
 * Strips "/" and "\" from end of string and appends a slash.
 * 
 * @param string $str Path
 * @return string Path with trailing slash
 */
function rslash($str) {
	return rtrim($str, '/\\').'/';
}

/**
 * Strips "/" and "\" from beginning of string and prepends a slash.
 * 
 * @param string $str Path
 * @return string Path with prepended slash
 */
function lslash($str) {
	return '/'.ltrim($str, '/\\');
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
 * Converts backslashes ("\") to forward slashes ("/") and strips 
 * slashes from both ends of the given string.
 * 
 * Useful for normalizing Win/Unix filepaths, or converting class
 * namespaces to filepaths.
 * 
 * @param string $path Path (not necessarily a filesystem path).
 * @return string Clean path.
 */
function cleanpath($path) {
	return trim(str_replace('\\', '/', $path), '/');
}

/** ================================
			FILESYSTEM
================================= */

/**
 * Registers an spl autoloader for given namespace and directory.
 * 
 * @param string $namespace Class namespace/prefix to catch.
 * @param string $directory Directory path to class files.
 * @return void
 */
function autoload_dir($namespace, $directory) {
	if (! is_dir($directory)) {
		trigger_error("Cannot register autoloader - $directory is not a directory.");
		return null;
	}
	spl_autoload_register(function($class) use ($namespace, $directory) {
		if (0 === strpos($class, $namespace)) {
			include realpath(rslash($directory).str_replace('\\', '/', $class).'.php');
		}
	});
}

/**
 * Returns files & directories in a given directory recursively.
 * 
 * Returned array is flattened - both keys and values are full filesystem paths.
 * 
 * This function is faster than using scan_recursive() with scan_flatten().
 * However, it can be unperformant if used excessively (particularly with deep recursion).
 * 
 * @param string $dir Directory to scan.
 * @param int $levels Max directory depth level.
 * @param array &$glob The glob of flattend paths.
 * @return array Flattened assoc. array of filepaths.
 */
function glob_recursive($dir, $levels = 5, array &$glob = array(), $level = 1) {
	$dir = rtrim($dir, '/\\').'/*';
	foreach( glob($dir) as $item ) {
		if ($level <= $levels && is_dir($item)) {
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
 * @param boolean|int $recursive Whether to recurse (also used internally)
 * @param int $levels Max directory depth level.
 * @return array Multi-dimensional array of files and directories.
 */
function scan_recursive($dir, $levels = 5, $level = 1) {
	$dir = rtrim($dir, '/\\').'/';
	$dirs = array();
	foreach( scandir($dir) as $item ) {
		if ('.' !== $item && '..' !== $item) {
			if ($level <= $levels && is_dir($dir.$item)) {
				$level++;
				$dirs[$item] = scan_recursive($dir.$item, $levels, $level);
			} else {
				$dirs[$item] = $dir.$item;
			}
		}
	}
	return $dirs;
}

/**
 * Flattens an array of files and directories returned from scan_recursive().
 * 
 * @param array $dirs Multi-dimensional array from scan().
 * @param array &$all_dirs The flattened filesystem array.
 * @return array The flattened filesystem array.
 */
function scan_flatten($dirs, array &$all_dirs = array()) {
	foreach( $dirs as $item ) {
		if (is_array($item)) {
			scan_flatten($item, $all_dirs, $strip_pre);
		} else {
			$all_dirs[$item] = $item;
		}
	}
	return $all_dirs;
}
	
/**
 * Returns file contents string, using $data as (the only) local PHP variables.
 *
 * @uses extract()
 *
 * @param string $file Path to file
 * @param array $data Assoc. array of variables to localize.
 * @return string File contents.
 */
function include_safe($file, array $data = array()) {
	$include = function ($__FILE__, array $__DATA__ = array()) {
		extract($__DATA__, EXTR_REFS);
		ob_start();
		include $__FILE__;
		return ob_get_clean();
	};
	return $include($file, $data);
}

/** ================================
 		FUNCTION CALLING
================================= */

/**
 * Invokes an invokable callback given array of arguments.
 * 
 * @param wild $var Anything - if Closure or object with __invoke() method, gets called with $args.
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
 * Useful for routing, where the order of route variables may
 * create an "unordered" array of callback parameters.
 * 
 * @param callable $callback Callable callback.
 * @param array $args Array of callback parameters.
 * @return mixed Result of callback.
 */
function invoke($callback, array $args = array()) {
	
	$type = null;
	
	if ($callback instanceof Closure || is_string($callback)) {
		$refl = new ReflectionFunction($callback);
		$type = 'func';
	} elseif (is_array($callback)) {
		$refl = new ReflectionMethod($callback[0], $callback[1]);
		$type = 'method';
	} else {
		$refl = new ReflectionMethod(get_class($callback), '__invoke');
		$type = 'object';
	}
	
	$params = array();
	
	foreach($refl->getParameters() as $i => $param) {
		
		$name = $param->getName();
		
		if (isset($args[$name])) {
			$params[$name] = $args[$name];
		} elseif (isset($args[$i])) {
			$params[$name] = $args[$i];
		} elseif ($param->isDefaultValueAvailable()) {
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
	
	throw new LogicException("Unknown callback.");
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
		return get_class($fn) . '::__invoke';
	}
	if (is_array($fn)) {
		if (is_object($fn[0])){
			return get_class($fn[0]).'->'.$fn[1];
		}
		return $fn[0].'::'.$fn[1];
	}
}

/** ================================
			  HTTP
================================= */

if (! function_exists('redirect')) : // common name
	
	/**
	 * Redirects browser via Location header to given URL.
	 * 
	 * @param string $url	URL to redirect to. Used in "Location:" header.
	 * @return void
	 */
	function redirect($url) {
		if (headers_sent($filename, $line)) {
			echo '<h1>Error Cannot redirect to <a href=\"$url\">$url</a></h1>'
				."<p>Output has already started in $filename on line $line</p>";
			exit;
		}
		header_remove('Last-Modified');
		header('Expires: Mon, 12 Dec 1982 06:00:00 GMT');
		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		// don't set status - sent automatically unless 201 or 3xx set
		header("Location: $url");
		exit;
	}

endif;

if (! function_exists('http_response_code')) :
	
	/**
	 * Returns/sends HTTP response status code.
	 * Back-compat PHP < 5.4 (and actually works...)
	 * 
	 * @param null|int $code	HTTP response status code.
	 * @return int				Current response code.
	 */
	function http_response_code($code = null) {
		
		if (! isset($code)) {
			return isset($GLOBALS['HTTP_RESPONSE_CODE']) 
				? $GLOBALS['HTTP_RESPONSE_CODE'] 
				: 200;
		}
		
		$code = intval($code);
		$description = http_response_code_desc($code);
		
		if (empty($description)) {
			$msg = "Invalid HTTP response status code given: '$code'.";
			throw new InvalidArgumentException($msg);
		}
		
		// Respect RFC2616 for PHP under CGI
		// @see {@link http://us3.php.net/manual/en/ini.core.php#ini.cgi.rfc2616-headers}
		if (1 === ini_get('cgi.rfc2616_headers')) {
			$protocol = 'Status:';
		} else {
			$protocol = isset($_SERVER['SERVER_PROTOCOL']) 
				? $_SERVER['SERVER_PROTOCOL'] 
				: 'HTTP/1.0';
		}
		
		header("$protocol $code $description", true, $code);
		
		return $GLOBALS['HTTP_RESPONSE_CODE'] = $code;
	}
	
endif;

/**
 * Returns HTTP status header description.
 * 
 * @param int $code		HTTP response status code.
 * @return string		Status description string, or empty if invalid.
 */
function http_response_code_desc($code) {
	$code = abs(intval($code));
	$header_desc = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Reserved',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		510 => 'Not Extended'
	);
	return isset($header_desc[$code]) ? $header_desc[$code] : '';
}

/**
 * Returns array of HTTP request headers.
 * 
 * Provides a server- and extension-agnostic function to 
 * access the HTTP headers sent by the current request.
 * 
 * @param array|null $server	Array or null to use $_SERVER
 * @return array 				HTTP request headers, keys stripped of "HTTP_" and lowercase.
 */
function http_request_headers(array $server = null) {
	static $headers;

	if (empty($server) || $server === $_SERVER) {
		// get once per request
		if (isset($headers)) {
			return $headers;
		}
		$server = &$_SERVER;
	}

	if (function_exists('apache_request_headers')) {
		$_headers = apache_request_headers();
	} elseif (extension_loaded('http')) {
		$_headers = http_get_request_headers();
	} else {
		$_headers = array();
		$misfits = array('CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5', 'AUTH_TYPE');
		foreach ( $server as $key => $value ) {
			if (0 === strpos($key, 'HTTP_')) {
				$_headers[$key] = $value;
			} elseif (in_array($key, $misfits)) {
				$_headers[$key] = $value;
			}
		}
	}

	// Normalize header keys
	$headers = array();
	foreach ( $_headers as $key => $value ) {
		$key = str_replace('_', '-', strtolower($key));
		if (0 === strpos($key, 'http-')) {
			$key = str_replace('http-', '', $key);
		}
		$headers[$key] = $value;
	}

	return $headers;
}

/**
 * Fetches a single HTTP request header.
 * 
 * @param string $name		Header name, lowercase, without 'HTTP_' prefix.
 * @return string|null		Header value, if set, otherwise null.
 */
function http_request_header($name) {
	$headers = http_request_headers();
	$name = strtolower($name);
	return isset($headers[$name]) ? $headers[$name] : null;
}

/**
 * Matches the contents of a given HTTP request header.
 * 
 * @param string $name			Header name, lowercase, without 'HTTP_'.
 * @param string|array $match	If string, will stripos() check header value 
 * 								and return boolean if string is found. If array,
 * 								will find the first matching item from the 
 * 								comma-exploded header value.
 * @return boolean|string		String if $match is array and item found, else bool.
 */
function http_request_header_match($name, $match) {
	$header = http_request_header($name);
	if (null === $header) {
		return null;
	}
	return is_string($match) 
		? false !== stripos($header, $match) 
		: array_match_csv($match, $header);
}

/**
 * Returns Internet Media Type (MIME) for given filetype.
 * 
 * @param string $filetype	Filetype.
 * @return string			MIME if found, otherwise null.
 */
function mimetype($filetype) {
	$filetype = strtolower($filetype);
	$mimes = array(
        'json'		=> 'application/json',
        'jsonp'		=> 'text/javascript',
        'js'		=> 'text/javascript',
        'html'		=> 'text/html',
        'xml'		=> 'text/xml',
        'csv'		=> 'text/csv',
        'plain'		=> 'text/plain',
        'text'		=> 'text/plain',
        'css'		=> 'text/css',
        'vcard'		=> 'text/vcard',
        'xhtml'		=> 'application/html+xml',
        'rss'		=> 'application/rss+xml',
        'atom'		=> 'application/atom+xml',
        'rdf' 		=> 'application/rdf+xml',
        'dtd'		=> 'application/xml-dtd',
        'zip'		=> 'application/zip',
        'gzip'		=> 'application/gzip',
        'woff'		=> 'application/font-woff',
        'soap'		=> 'application/soap+xml',
        'pdf'		=> 'application/pdf',
        'download'	=> 'application/octet-stream',
        'upload'	=> 'multipart/form-data',
        'form'		=> 'application/x-www-form-urlencoded',
        'xls'		=> 'application/vnd.ms-excel',
        'xlxs'		=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt'		=> 'application/vnd.ms-powerpoint',
        'pptx'		=> 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'docx'		=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'odt'		=> 'application/vnd.oasis.opendocument.text',
        'odp'		=> 'application/vnd.oasis.opendocument.presentation',
        'ods'		=> 'application/vnd.oasis.opendocument.spreadsheet',
        'xps'		=> 'application/vnd.ms-xpsdocument',
        'kml'		=> 'application/vnd.google-earth.kml+xml',
        'flash'		=> 'application/x-shockwave-flash',
        'swf'		=> 'application/x-shockwave-flash',
        'dart'		=> 'application/dart',
        'gif'		=> 'image/gif',
        'jpeg'		=> 'image/jpeg',
        'png'		=> 'image/png',
        'svg'		=> 'image/svg+xml',
        'mp4'		=> 'audio/mp4',
        'mp3'		=> 'audio/mpeg',
        'mpeg'		=> 'audio/mpeg',
        'ogg'		=> 'audio/ogg',
        'flac'		=> 'audio/ogg',
        'wav'		=> 'audio/vnd.wave',
        'md'		=> 'text/x-markdown',
        'message'	=> 'message/http',
    );
	return isset($mimes[$filetype]) ? $mimes[$filetype] : null;
}

/** ================================
 		    CONSTANTS
================================= */

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
 * Returns first item from $array found in comma-exploded $csv.
 * 
 * @param array $array 		Indexed array of items to find, in order.
 * @param string $csv		Comma-separated string. Gets exploded to find array items.
 * @return string|boolean	String value if found, otherwise false.
 */
function array_match_csv(array $array, $csv) {
	foreach ( explode(',', $csv) as $match ) {
		if (isset($array[$match]) || in_array($match, $array, true))
			return $match;
	}
	return false;
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
 * Explodes a string into an array and trims whitespace from each item.
 */
function explode_trim($delim, $str, $charlist = '\t\r\n ') {
	return array_map('trim', explode($delim, $str), $charlist);
}

/**
 * Implode an array into a list of items separated by $separator.
 * Use $last_separator for the last list item.
 *
 * Useful for natural language lists (e.g first, second & third).
 *
 * Graciously stolen from humanmade hm-core:
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

/** ================================
	Modified facebook/libphutil
================================= */

/**
 * Identity function, returns its argument unmodified.
 *
 * This is useful almost exclusively as a workaround to an oddity in the PHP
 * grammar -- this is a syntax error:
 *
 * COUNTEREXAMPLE
 * new Thing()->doStuff();
 *
 * ...but this works fine:
 *
 * id(new Thing())->doStuff();
 *
 * @author facebook/libphutil
 * 
 * @param wild Anything.
 * @return wild Unmodified argument.
 */
function id($var) {
	return $var;
}

/**
 * Access an array index, retrieving the value stored there if it exists or
 * a default if it does not. This function allows you to concisely access an
 * index which may or may not exist without raising a warning.
 *
 * @param array Array to access.
 * @param scalar Index to access in the array.
 * @param wild Default value to return if the key is not present in the
 * array.
 * @return wild If $array[$key] exists, that value is returned. If not,
 * $default is returned without raising a warning.
 */
function index(array $array, $key, $default = null) {
	// isset() is a micro-optimization - it is fast but fails for null values.
	if (isset($array[$key])) {
		return $array[$key];
	}

	// Comparing $default is also a micro-optimization.
	if ($default === null || array_key_exists($key, $array)) {
		return null;
	}

	return $default;
}

/**
 * Call a method on a list of objects. Short for "method pull", this function
 * works just like @{function:ipull}, except that it operates on a list of
 * objects instead of a list of arrays. This function simplifies a common type
 * of mapping operation:
 *
 * COUNTEREXAMPLE
 * $names = array();
 * foreach ($objects as $key => $object) {
 * $names[$key] = $object->getName();
 * }
 *
 * You can express this more concisely with mpull():
 *
 * $names = mpull($objects, 'getName');
 *
 * mpull() takes a third argument, which allows you to do the same but for
 * the array's keys:
 *
 * COUNTEREXAMPLE
 * $names = array();
 * foreach ($objects as $object) {
 * $names[$object->getID()] = $object->getName();
 * }
 *
 * This is the mpull version():
 *
 * $names = mpull($objects, 'getName', 'getID');
 *
 * If you pass ##null## as the second argument, the objects will be preserved:
 *
 * COUNTEREXAMPLE
 * $id_map = array();
 * foreach ($objects as $object) {
 * $id_map[$object->getID()] = $object;
 * }
 *
 * With mpull():
 *
 * $id_map = mpull($objects, null, 'getID');
 *
 * See also @{function:ipull}, which works similarly but accesses array indexes
 * instead of calling methods.
 *
 * @param list Some list of objects.
 * @param string|null Determines which **values** will appear in the result
 * array. Use a string like 'getName' to store the
 * value of calling the named method in each value, or
 * ##null## to preserve the original objects.
 * @param string|null Determines how **keys** will be assigned in the result
 * array. Use a string like 'getID' to use the result
 * of calling the named method as each object's key, or
 * ##null## to preserve the original keys.
 * @return dict A dictionary with keys and values derived according
 * to whatever you passed as $method and $key_method.
 */
function list_mpull(array $list, $method, $key_method = null) {
	$result = array();
	foreach ( $list as $key => $object ) {
		if ($key_method !== null) {
			$key = $object->$key_method();
		}
		if ($method !== null) {
			$value = $object->$method();
		} else {
			$value = $object;
		}
		$result[$key] = $value;
	}
	return $result;
}

/**
 * Access a property on a list of objects. Short for "property pull", this
 * function works just like @{function:mpull}, except that it accesses object
 * properties instead of methods. This function simplifies a common type of
 * mapping operation:
 *
 * COUNTEREXAMPLE
 * $names = array();
 * foreach ($objects as $key => $object) {
 * $names[$key] = $object->name;
 * }
 *
 * You can express this more concisely with ppull():
 *
 * $names = ppull($objects, 'name');
 *
 * ppull() takes a third argument, which allows you to do the same but for
 * the array's keys:
 *
 * COUNTEREXAMPLE
 * $names = array();
 * foreach ($objects as $object) {
 * $names[$object->id] = $object->name;
 * }
 *
 * This is the ppull version():
 *
 * $names = ppull($objects, 'name', 'id');
 *
 * If you pass ##null## as the second argument, the objects will be preserved:
 *
 * COUNTEREXAMPLE
 * $id_map = array();
 * foreach ($objects as $object) {
 * $id_map[$object->id] = $object;
 * }
 *
 * With ppull():
 *
 * $id_map = ppull($objects, null, 'id');
 *
 * See also @{function:mpull}, which works similarly but calls object methods
 * instead of accessing object properties.
 *
 * @param list Some list of objects.
 * @param string|null Determines which **values** will appear in the result
 * array. Use a string like 'name' to store the value of
 * accessing the named property in each value, or
 * ##null## to preserve the original objects.
 * @param string|null Determines how **keys** will be assigned in the result
 * array. Use a string like 'id' to use the result of
 * accessing the named property as each object's key, or
 * ##null## to preserve the original keys.
 * @return dict A dictionary with keys and values derived according
 * to whatever you passed as $property and $key_property.
 */
function list_ppull(array $list, $property, $key_property = null) {
	$result = array();
	foreach ( $list as $key => $object ) {
		if ($key_property !== null) {
			$key = $object->$key_property;
		}
		if ($property !== null) {
			$value = $object->$property;
		} else {
			$value = $object;
		}
		$result[$key] = $value;
	}
	return $result;
}

/**
 * Choose an index from a list of arrays. Short for "index pull", this function
 * works just like @{function:mpull}, except that it operates on a list of
 * arrays and selects an index from them instead of operating on a list of
 * objects and calling a method on them.
 *
 * This function simplifies a common type of mapping operation:
 *
 * COUNTEREXAMPLE
 * $names = array();
 * foreach ($list as $key => $dict) {
 * $names[$key] = $dict['name'];
 * }
 *
 * With ipull():
 *
 * $names = ipull($list, 'name');
 *
 * See @{function:mpull} for more usage examples.
 *
 * @param list Some list of arrays.
 * @param scalar|null Determines which **values** will appear in the result
 * array. Use a scalar to select that index from each
 * array, or null to preserve the arrays unmodified as
 * values.
 * @param scalar|null Determines which **keys** will appear in the result
 * array. Use a scalar to select that index from each
 * array, or null to preserve the array keys.
 * @return dict A dictionary with keys and values derived according
 * to whatever you passed for $index and $key_index.
 */
function list_ipull(array $list, $index, $key_index = null) {
	$result = array();
	foreach ( $list as $key => $array ) {
		if ($key_index !== null) {
			$key = $array[$key_index];
		}
		if ($index !== null) {
			$value = $array[$index];
		} else {
			$value = $array;
		}
		$result[$key] = $value;
	}
	return $result;
}

/**
 * Group a list of objects by the result of some method, similar to how
 * GROUP BY works in an SQL query. This function simplifies grouping objects
 * by some property:
 *
 * COUNTEREXAMPLE
 * $animals_by_species = array();
 * foreach ($animals as $animal) {
 * $animals_by_species[$animal->getSpecies()][] = $animal;
 * }
 *
 * This can be expressed more tersely with mgroup():
 *
 * $animals_by_species = mgroup($animals, 'getSpecies');
 *
 * In either case, the result is a dictionary which maps species (e.g., like
 * "dog") to lists of animals with that property, so all the dogs are grouped
 * together and all the cats are grouped together, or whatever super
 * businessesey thing is actually happening in your problem domain.
 *
 * See also @{function:igroup}, which works the same way but operates on
 * array indexes.
 *
 * @param list List of objects to group by some property.
 * @param string Name of a method, like 'getType', to call on each object
 * in order to determine which group it should be placed into.
 * @param ... Zero or more additional method names, to subgroup the
 * groups.
 * @return dict Dictionary mapping distinct method returns to lists of
 * all objects which returned that value.
 */
function list_mgroup(array $list, $by /* , ... */) {
	$map = list_mpull($list, $by);

	$groups = array();
	foreach ( $map as $group ) {
		// Can't array_fill_keys() here because 'false' gets encoded wrong.
		$groups[$group] = array();
	}

	foreach ( $map as $key => $group ) {
		$groups[$group][$key] = $list[$key];
	}

	$args = func_get_args();
	$args = array_slice($args, 2);
	if ($args) {
		array_unshift($args, null);
		foreach ( $groups as $group_key => $grouped ) {
			$args[0] = $grouped;
			$groups[$group_key] = call_user_func_array('mgroup', $args);
		}
	}

	return $groups;
}

/**
 * Group a list of arrays by the value of some index. This function is the same
 * as @{function:mgroup}, except it operates on the values of array indexes
 * rather than the return values of method calls.
 *
 * @param list List of arrays to group by some index value.
 * @param string Name of an index to select from each array in order to
 * determine which group it should be placed into.
 * @param ... Zero or more additional indexes names, to subgroup the
 * groups.
 * @return dict Dictionary mapping distinct index values to lists of
 * all objects which had that value at the index.
 */
function list_igroup(array $list, $by /* , ... */) {
	$map = list_ipull($list, $by);

	$groups = array();
	foreach ( $map as $group ) {
		$groups[$group] = array();
	}

	foreach ( $map as $key => $group ) {
		$groups[$group][$key] = $list[$key];
	}

	$args = func_get_args();
	$args = array_slice($args, 2);
	if ($args) {
		array_unshift($args, null);
		foreach ( $groups as $group_key => $grouped ) {
			$args[0] = $grouped;
			$groups[$group_key] = call_user_func_array('igroup', $args);
		}
	}

	return $groups;
}

/**
 * Sort a list of objects by the return value of some method. In PHP, this is
 * often vastly more efficient than ##usort()## and similar.
 *
 * // Sort a list of Duck objects by name.
 * $sorted = msort($ducks, 'getName');
 *
 * It is usually significantly more efficient to define an ordering method
 * on objects and call ##msort()## than to write a comparator. It is often more
 * convenient, as well.
 *
 * NOTE: This method does not take the list by reference; it returns a new list.
 *
 * @param list List of objects to sort by some property.
 * @param string Name of a method to call on each object; the return values
 * will be used to sort the list.
 * @return list Objects ordered by the return values of the method calls.
 */
function list_msort(array $list, $method) {
	$surrogate = list_mpull($list, $method);

	asort($surrogate);

	$result = array();
	foreach ( $surrogate as $key => $value ) {
		$result[$key] = $list[$key];
	}

	return $result;
}

/**
 * Sort a list of arrays by the value of some index. This method is identical to
 * @{function:msort}, but operates on a list of arrays instead of a list of
 * objects.
 *
 * @param list List of arrays to sort by some index value.
 * @param string Index to access on each object; the return values
 * will be used to sort the list.
 * @return list Arrays ordered by the index values.
 */
function list_isort(array $list, $index) {
	$surrogate = list_ipull($list, $index);

	asort($surrogate);

	$result = array();
	foreach ( $surrogate as $key => $value ) {
		$result[$key] = $list[$key];
	}

	return $result;
}

/**
 * Filter a list of objects by executing a method across all the objects and
 * filter out the ones wth empty() results. this function works just like
 * @{function:ifilter}, except that it operates on a list of objects instead
 * of a list of arrays.
 *
 * For example, to remove all objects with no children from a list, where
 * 'hasChildren' is a method name, do this:
 *
 * mfilter($list, 'hasChildren');
 *
 * The optional third parameter allows you to negate the operation and filter
 * out nonempty objects. To remove all objects that DO have children, do this:
 *
 * mfilter($list, 'hasChildren', true);
 *
 * @param array List of objects to filter.
 * @param string A method name.
 * @param bool Optionally, pass true to drop objects which pass the
 * filter instead of keeping them.
 *
 * @return array List of objects which pass the filter.
 */
function list_mfilter(array $list, $method, $negate = false) {
		
	if (! is_string($method)) {
		throw new InvalidArgumentException('Argument method is not a string.');
	}

	$result = array();
	foreach ( $list as $key => $object ) {
		$value = $object->$method();

		if (! $negate) {
			if (! empty($value)) {
				$result[$key] = $object;
			}
		} else {
			if (empty($value)) {
				$result[$key] = $object;
			}
		}
	}

	return $result;
}

/**
 * Filter a list of arrays by removing the ones with an empty() value for some
 * index. This function works just like @{function:mfilter}, except that it
 * operates on a list of arrays instead of a list of objects.
 *
 * For example, to remove all arrays without value for key 'username', do this:
 *
 * ifilter($list, 'username');
 *
 * The optional third parameter allows you to negate the operation and filter
 * out nonempty arrays. To remove all arrays that DO have value for key
 * 'username', do this:
 *
 * ifilter($list, 'username', true);
 *
 * @param array List of arrays to filter.
 * @param scalar The index.
 * @param bool Optionally, pass true to drop arrays which pass the
 * filter instead of keeping them.
 *
 * @return array List of arrays which pass the filter.
 */
function list_ifilter(array $list, $index, $negate = false) {
	
	if (! is_scalar($index)) {
		throw new InvalidArgumentException('Argument index is not a scalar.');
	}

	$result = array();
	if (! $negate) {
		foreach ( $list as $key => $array ) {
			if (! empty($array[$index])) {
				$result[$key] = $array;
			}
		}
	} else {
		foreach ( $list as $key => $array ) {
			if (empty($array[$index])) {
				$result[$key] = $array;
			}
		}
	}

	return $result;
}

/**
 * Selects a list of keys from an array, returning a new array with only the
 * key-value pairs identified by the selected keys, in the specified order.
 *
 * Note that since this function orders keys in the result according to the
 * order they appear in the list of keys, there are effectively two common
 * uses: either reducing a large dictionary to a smaller one, or changing the
 * key order on an existing dictionary.
 *
 * @param dict Dictionary of key-value pairs to select from.
 * @param list List of keys to select.
 * @return dict Dictionary of only those key-value pairs where the key was
 * present in the list of keys to select. Ordering is
 * determined by the list order.
 */
function array_select_keys(array $dict, array $keys) {
	$result = array();
	foreach ( $keys as $key ) {
		if (array_key_exists($key, $dict)) {
			$result[$key] = $dict[$key];
		}
	}
	return $result;
}

/**
 * Returns the first key of an array.
 *
 * @param array Array to retrieve the first key from.
 * @return int|string The first key of the array.
 */
function array_first_key(array $arr) {
	reset($arr);
	return key($arr);
}

/**
 * Returns the last key of an array.
 *
 * @param array Array to retrieve the last key from.
 * @return int|string The last key of the array.
 */
function array_last_key(array $arr) {
	end($arr);
	return key($arr);
}

/**
 * Merge a vector of arrays performantly. This has the same semantics as
 * array_merge(), so these calls are equivalent:
 *
 * array_merge($a, $b, $c);
 * array_mergev(array($a, $b, $c));
 *
 * However, when you have a vector of arrays, it is vastly more performant to
 * merge them with this function than by calling array_merge() in a loop,
 * because using a loop generates an intermediary array on each iteration.
 *
 * @param list Vector of arrays to merge.
 * @return list Arrays, merged with array_merge() semantics.
 */
function array_mergev(array $arrayv) {
	if (! $arrayv) {
		return array();
	}

	return call_user_func_array('array_merge', $arrayv);
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
