<?php
/**
 * @package wells5609/php-util
 * 
 * Miscellaneous
 * 
 *  * is_windows
 *  * is_url
 *  * is_json
 *  * is_serialized
 *  * is_xml
 *  * base64_url_encode
 *  * base64_url_decode
 *  * mysql_datetime
 *  * mysql_date
 *  * pdo_dsn
 *  * antispam_email
 *  * getcookie
 *  * objectid
 *  * define_safe
 *  * id
 * 
 */

if (! function_exists('is_windows')) :
	 
	/**
	 * Returns true if server OS is Windows.
	 * 
	 * @return boolean True if server OS is Windows, otherwise false.
	 */
	function is_windows() {
		return '\\' === DIRECTORY_SEPARATOR;
	}

endif;

if (! function_exists('is_url')) :
		
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

endif;

if (! function_exists('is_json')) :
	
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

endif;

if (! function_exists('is_serialized')) :
	
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

endif;

if (! function_exists('is_xml')) :
	
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

endif;

if (! function_exists('base64_url_encode')) :
	
	/**
	 * Base64 encode a string, safe for URL's.
	 * 
	 * @param string $str Data to encode.
	 * @return string URL-safe Base64-encoded string.
	 */
	function base64_url_encode($str) {
	    return str_replace(array('+','/','=','\r','\n'), array('-','_'), base64_encode($str));
	}
	
endif;

if (! function_exists('base64_url_decode')) :
	
	/**
	 * Decodes a URL-safe Base64-encoded string.
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

endif;

if (! function_exists('mysql_datetime')) :
	
	/**
	 * Returns a MySQL DATETIME string.
	 * 
	 * @param int|null $time Unix time, or current time if NULL.
	 * @return string MySQL DATETIME representation of given time ("Y-m-d H:i:s").
	 */
	function mysql_datetime($time = null) {
		return date('Y-m-d H:i:s', $time ?: time());
	}

endif;

if (! function_exists('mysql_date')) :
	
	/**
	 * Returns a MySQL DATE string.
	 * 
	 * @param int|null $time Unix time, or current time if NULL.
	 * @return string MySQL DATE representation of given time ("Y-m-d").
	 */
	function mysql_date($time = null) {
		return date('Y-m-d', $time ?: time());
	}

endif;

if (! function_exists('udate')) :
	
	/**
	 * An implementation of date() that allows for microseconds via the 'u' format flag.
	 * 
	 * @param string $format A date()-type format, including "u".
	 * @param float $utime [Optional] A timestamp with microseconds like that returned 
	 * from microtime(), or null to use the current time. Default null.
	 * @return string Date formatted with microseconds.
	 */
	function udate($format = 'u', $utime = null) {
		
		if (null === $utime) {
			$utime = microtime(true);
		}
		
		$time = floor($utime);
		$milliseconds = round(($utime - $time)*1000000);
		
		return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $time);
	}

endif;

if (! function_exists('pdo_dsn')) :
	
	/**
	 * Returns a DSN string for use with PDO drivers.
	 *
	 * @param string $driver PDO driver (e.g. "mysql", "sqlite", etc.)
	 * @param string $host DB host (or path if file-based, e.g. sqlite).
	 * @param string $name [Optional] Database name.
	 * @param string $port [Optional] Database port.
	 * @param string $user [Optional] User name (only for pgsql).
	 * @param string $password [Optional] User password (only for pgsql).
	 * @return string DSN for use in a PDO driver connection.
	 */
	function pdo_dsn($driver, $host, $name = null, $port = null, $user = null, $password = null) {
		
		switch(strtolower($driver)) {
			
			case 'sqlite' :
				return "sqlite:{$host}";
			
			case 'pgsql':
				return "pgsql:host={$host}"
					.(isset($port) ? ";port={$port}" : "")
					.(isset($name) ? ";dbname={$name}" : "")
					.(isset($user) ? ";user={$user}" : "")
					.(isset($password) ? ";password={$password}" : "");
			
			case 'mysql' :
				if (! isset($name)) {
					throw new RuntimeException("MySQL DSN requires 'name' (database name).");
				}
				// allow pass thru to default
				
			default:
				return "{$driver}:host={$host}"
					.(isset($port) ? ";port={$port}" : '')
					.(isset($name) ? ";dbname={$name}" : '');
		}
	}
	
endif;

if (! function_exists('antispam_email')) :
	
	/**
	 * Obfuscate an email address to prevent spam-bot harvesting.
	 * 
	 * @author wordpress
	 * 
	 * @param string $email Email address.
	 * @param boolean $hex_encode Whether to hex encode some letters. Default false.
	 * @return string Obfuscated email address.
	 */
	function antispam_email($email, $hex_encode = false) {
		$email_address = '';
		$hex_encoding = 1 + (int)(bool)$hex_encode;
		foreach(str_split($email) as $letter) {
			$j = mt_rand(0, $hex_encoding);
			if ($j == 0) {
				$email_address .= '&#'.ord($letter).';';
			} else if ($j == 1) {
				$email_address .= $letter;
			} else if ($j == 2) {
				$email_address .= '%'.sprintf('%02s', dechex(ord($letter)));
			}
		}
		return str_replace('@', '@', $email_address);
	}

endif;

if (! function_exists('getcookie')) :
	
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
	
endif;

if (! function_exists('objectid')) :
	
	/**
	 * Returns a unique ID for an object.
	 * 
	 * This function uses the object's class and SHA-1 hash of its serialized 
	 * value to create the ID. Therefore, two objects of the same class with 
	 * the exact same data will generate the same ID (if $unique is left false).
	 * 
	 * Why not use spl_object_hash()? Because the values are only unique for 
	 * objects that exist at the time of hashing.
	 * 
	 * Raises an error if given a non-object.
	 * 
	 * @param object $object An object
	 * @param boolean $unique Pass true to get a unique ID. Default false.
	 * @return string
	 */
	function objectid($object, $unique = false) {
		if (! is_object($object)) {
			trigger_error('Must pass object to object_id(): given '.gettype($object));
			return null;
		}
		return get_class($object).sha1(serialize($object).($unique ? microtime(true) : ''));
	}
	
endif;

if (! function_exists('define_safe')) :
		
	/**
	 * Defines a constant if not yet defined.
	 * 
	 * @param string $const Name of constant
	 * @param scalar $value Value to define constant if undefined.
	 * @return boolean True if constant successfully defined, otherwise false.
	 */
	function define_safe($const, $value) {
		return defined($const) ? false : define($const, $value);
	}

endif;

if (! function_exists('id')) :
	
	/**
	 * Identity function, returns its argument unmodified.
	 * @author facebook/libphutil
	 * @param mixed Anything.
	 * @return mixed Unmodified argument.
	 */
	function id($var) {
		return $var;
	}

endif;
