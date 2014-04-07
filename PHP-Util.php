<?php

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
			FILESYSTEM
================================= */

/**
 * Converts backslashes ("\") to forward slashes ("/") and strips 
 * slashes from both ends of the given string.
 * 
 * Useful for normalizing Win-to-Unix filepaths, or converting 
 * class namespaces to filepaths.
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
 * @param string $path ... Paths to join
 * @return string Joined path.
 */
function joinpath($path1/* [, $path2 [, ...]] */) {
	return implode(DIRECTORY_SEPARATOR, array_map('unslash', func_get_args()));
}

/**
 * Returns true if given an absolute path, otherwise false.
 * 
 * @param string $path Filesystem path
 * @return boolean True if path is absolute, otherwise false.
 */
function is_abspath($path) {
	// Windows absolute paths like "C:\path" but not always "C:"
	// and backslash is default escape char in fnmatch()
	return ('\\' === DIRECTORY_SEPARATOR)
		? fnmatch('?:[/|\]*', $path, FNM_NOESCAPE)
		: file_exists($path);
}

/**
 * Registers an spl autoloader for given namespace and directory.
 * 
 * @param string $namespace Class namespace/prefix to catch.
 * @param string $directory Directory path to class files.
 * @return void
 */
function autoload_dir($namespace, $directory) {
	if (! is_dir($directory)) {
		throw new InvalidArgumentException("Cannot register autoloader - $directory is not a directory.");
	}
	spl_autoload_register(function($class) use ($namespace, $directory) {
		if (0 === strpos($class, $namespace)) {
			include rtrim($directory, '/\\').'/'.str_replace('\\', '/', $class).'.php';
		}
	});
}

/**
 * Returns files & directories in a given directory recursively.
 * 
 * Returned array is flattened - both keys and values are full filesystem paths.
 * 
 * This function is faster than using scan_recursive() with scan_flatten().
 * However, it can be slow if used excessively (with deep recursion).
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
 * @param int $levels Max directory depth level.
 * @param int $level Current depth level.
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
			scan_flatten($item, $all_dirs);
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
 * Useful for routing, where the order of route variables may
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
	} elseif (is_array($callback)) {
		$refl = new ReflectionMethod($callback[0], $callback[1]);
		$type = 'method';
	} elseif (is_object($callback)) {
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

/**
 * Redirects browser via Location header to given URL.
 * 
 * @param string $url	URL to redirect to. Used in "Location:" header.
 * @return void
 */
function http_redirect($url) {
	if (headers_sent($filename, $line)) {
		echo '<h1>Error Cannot redirect to <a href=\"$url\">$url</a></h1>'
			."<p>Output has already started in $filename on line $line</p>";
		exit;
	}
	header_remove('Last-Modified');
	header('Expires: Mon, 12 Dec 1982 06:00:00 GMT');
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	header('Pragma: no-cache');
	header("Location: $url"); // status sent automatically unless 201 or 3xx set
	exit;
}

function http_force_download($file) {
	if (headers_sent($filename, $line)) {
		$msg = "Cannot send file download - output already started in $filename on line $line.";
		throw new RuntimeException($msg);
	}
	header_remove('Last-Modified');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Type: '.mimetype('download'));
	header('Content-Length: '.filesize($file));
	header('Content-Disposition: attachment; filename="'.basename($file).'"');
	header('Content-Transfer-Encoding: binary');
	header('Connection: close');
	readfile($file);
	exit;
}

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
		
		// RFC2616 for PHP under CGI
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
		$misfits = array('CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5');
		foreach ( $server as $key => $value ) {
			if (0 === strpos($key, 'HTTP_')) {
				$_headers[$key] = $value;
			} elseif (in_array($key, $misfits, true)) {
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
	
	if (null === ($header = http_request_header($name))) {
		return null;
	}
	
	if (is_string($match)) {
		return (false !== stripos($header, $match));
	}
	
	foreach(explode(',', $header) as $hdr) {
		if (in_array($hdr, $match, true)) {
			return $hdr;
		}
	}
	
	return null;
}

function http_get_cache_headers($expires_offset = 86400) {

	$headers = array();

	if ('0' === $expires_offset || empty($expires_offset)) {
		$headers['Cache-Control'] = 'no-cache, must-revalidate, max-age=0';
		$headers['Expires'] = 'Thu, 19 Nov 1981 08:52:00 GMT';
		$headers['Pragma'] = 'no-cache';
	} else {
		$headers['Cache-Control'] = "Public, max-age=$expires_offset";
		$headers['Expires'] = gmdate('D, d M Y H:i:s', time() + $expires_offset).' GMT';
		$headers['Pragma'] = 'Public';
	}

	return $headers;
}

function http_is_ssl() {
	if (
		(isset($_SERVER['HTTPS']) && ('on' === strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS']))
		|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'])
		|| (isset($_SERVER['SERVER_PORT']) && '443' == $_SERVER['SERVER_PORT'])
	){
		return true;
	}
	return false;
}

function http_get_domain() {
	return rtrim($_SERVER['HTTP_HOST'], '/\\').rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
}

function http_get_url($path = '') {
	return 'http'.(http_is_ssl() ? 's' : '').'://'.http_get_domain().'/'.$path;
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
        'css'		=> 'text/css',
        'vcard'		=> 'text/vcard',
        'text'		=> 'text/plain',
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

/**
 * Retrieve the value of a cookie
 *
 * @author https://github.com/yeroon/codebase
 * 
 * @param string $name Name of cookie.
 * @return mixed Returns the value if cookie exists or false if no cookie was found
 */
function getcookie($name) {
	return (isset($_COOKIE[$name]) || array_key_exists($name, $_COOKIE))
		? $_COOKIE[$name]
    	: false;
}

/**
 * Encode (hex) an email address for display on the web to prevent spambots.
 *
 * @author https://github.com/yeroon/codebase
 * 
 * @param string $email
 * @return string
 */
function email_encode($email) {
    $return = '';
    for ($i = 0, $len = strlen($email); $i < $len; $i++) {
        $return .= '&#x'.bin2hex($email[$i]).';';
    }
    return $return;
}

/** ================================
		  TOKENS/NONCES
================================= */

/**
 * Generates a verifiable token from seed.
 */
function generate_token($seed, $algo = null) {
	
	if (null === $algo) {
		if (! defined('HASH_ALGO_DEFAULT')) {
			define('HASH_ALGO_DEFAULT', 'sha1');
		}
		$algo = HASH_ALGO_DEFAULT;
	}
	
	if (! defined('HASH_HMAC_KEY')) {
		define('HASH_HMAC_KEY', '1#Kjia6~?qxg*/!RIg>E!*TwB%yq)Fa77O:F))>%>Lp/vw-T1QF!Qm6rFWz1X3bQ');
	}
	
	return hash_hmac($algo, $seed, HASH_HMAC_KEY);
}

/**
 * Verifies a token with seed.
 */
function verify_token($token, $seed, $algo = null) {
	return $token === generate_token($seed, $algo);
}

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
		return (false === strpos($val, DECIMAL_POINT)) 
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
	return 0 === strcasecmp($needle, substr($haystack, -strlen($needle)));
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
	From facebook/libphutil
================================= */

if (! function_exists('id')) :
	
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
	 * @param wild Anything.
	 * @return wild Unmodified argument.
	 */
	function id($var) {
		return $var;
	}

endif;

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
