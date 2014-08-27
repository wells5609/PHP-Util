<?php

const INFO_VENDOR = 4;
const INFO_NAMESPACES = 8;
const INFO_CLASSNAME = 16;
const INFO_BASIC = 28;
const INFO_PARENTS = 32;
const INFO_INTERFACES = 64;
const INFO_TRAITS = 128;

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
 * @param int $flag Bitwise INFO_* flags. Default INFO_BASIC.
 * @return string|array String if flag given and found, otherwise array of info.
 */
function class_info($class, $flag = INFO_BASIC) {
	
	$info = array();
	
	if (! is_string($class)) {
		$class = get_class($class);	
	} else {
		$class = trim($class, '\\');
	}
	
	if (false === strpos($class, '\\')) {
		if ($flag === INFO_CLASSNAME) {
			return $class;
		}
		$info['class'] = $class;
		if ((! INFO_INTERFACES & $flag) && (! INFO_PARENTS & $flag)) {
			return $info;
		}
	}
	
	$parts = explode('\\', $class);
	$num = count($parts);
	
	if ($flag === INFO_CLASSNAME) {
		return $parts[$num-1];
	}
	
	if ($flag === INFO_VENDOR) {
		return $parts[0];
	} 
	
	if ($flag & INFO_VENDOR) {
		$info['vendor'] = $parts[0];
	}
	
	if ($num > 2 && ($flag & INFO_NAMESPACES)) {
		$info['namespaces'] = array();
		for ($i=1; $i < $num-1; $i++) {
			$info['namespaces'][] = $parts[$i];
		}
	}
	
	if ($flag === INFO_NAMESPACES) {
		return isset($info['namespaces']) ? $info['namespaces'] : null;
	}
	
	if ($flag & INFO_CLASSNAME) {
		$info['class'] = $parts[$num-1];
	}
	
	if ($flag & INFO_PARENTS) {
		$info['parents'] = array_values(class_parents($class));
		if ($flag === INFO_PARENTS) {
			return $info['parents'];
		}
	}
	
	if ($flag & INFO_INTERFACES) {
		$info['interfaces'] = array_values(class_implements($class));
		if ($flag === INFO_INTERFACES) {
			return $info['interfaces'];
		}
	}
	
	if ($flag & INFO_TRAITS) {
		$info['traits'] = array_values(class_uses($class));
		if ($flag === INFO_TRAITS) {
			return $info['traits'];
		}
	}
	
	return $info;
}
