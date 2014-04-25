<?php
/**
 * PHP-Util - PHP utility library.
 * 
 * @license MIT
 * @author wells
 * @version 0.2.6
 * 
 * ------------------
 * Changelog:
 * 	0.2.6 (4/25/14)
 * 		- Move scalar, array, and filesystem funcs to separate files.
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
 
require __DIR__ . '/src/scalar.php';
require __DIR__ . '/src/array.php';
require __DIR__ . '/src/file.php';

/** ================================
 		  MISCELLANEOUS
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

/**
 * Returns true if server OS is Windows.
 * 
 * @return boolean True if server OS is Windows, otherwise false.
 */
function is_win() {
	return '\\' === DIRECTORY_SEPARATOR;
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

/**
 * Generates a verifiable token from seed.
 * 
 * Uses PHPUTIL_TOKEN_ALGO constant, if defined, as default hash algorithm.
 * Uses PHPUTIL_TOKEN_HMAC_KEY constant, if defined, as hash hmac key.
 * 
 * @param string $seed String used to create hash token.
 * @param string $algo Hash algorithm to use (default 'sha1').
 * @return string Token using hash_hmac().
 */
function generate_token($seed, $algo = null) {
	
	if (null === $algo) {
		$algo = defined('PHPUTIL_TOKEN_ALGO') ? PHPUTIL_TOKEN_ALGO : 'sha1';
	}
	
	$hmac_key = defined('PHPUTIL_TOKEN_HMAC_KEY') 
		? PHPUTIL_TOKEN_HMAC_KEY 
		: '4tj;,#K3+H%&a?*c*K8._O]~K_~XD &h3#I/pv#Rtoi,Iul84I/kg*J=Kk8sbGIa';
	
	return hash_hmac($algo, $seed, $hmac_key);
}

/**
 * Verifies a token with seed.
 */
function verify_token($token, $seed, $algo = null) {
	return $token === generate_token($seed, $algo);
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
