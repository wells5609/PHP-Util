<?php
/**
 * @package wells5609/php-util
 * 
 * PHP utility library.
 * 
 * @license MIT
 * @author wells5609
 * @version 0.3.5
 * 
 * @see changelog.txt for version changes.
 */

// Include all packages
require __DIR__.'/src/array.php';
require __DIR__.'/src/misc.php';
require __DIR__.'/src/str.php';
require __DIR__.'/src/format.php';
require __DIR__.'/src/input.php';
require __DIR__.'/src/callable.php';
require __DIR__.'/src/files-formats.php';

function phputil_use_function($name) {
	static $loaded = array();
	
	if (isset($loaded[$name])) {
		return $loaded[$name];
	}
	
	$file = __DIR__.'/src/fn/'.$name.'.php';
	
	if (file_exists($file)) {
		require $file;
		return $loaded[$name] = true;;
	}
	
	return $loaded[$name] = false;
}
