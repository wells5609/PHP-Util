<?php

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
