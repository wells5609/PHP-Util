<?php
/**
 * PHP-Util - PHP utility library.
 * 
 * @license MIT
 * @author wells
 * @version 0.2.7
 * 
 * ------------------
 * Changelog:
 *  0.2.7 (5/10/14)
 * 		- Add a bunch of array functions [array.php]
 * 		- Add is_json(), to_seconds() [scalar.php]
 * 		- Add is_hhvm() [misc.php]
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

/**
 * Loader function for the various function packages.
 * 
 * @param string $package File name of package w/o extension.
 * @return void
 */
function php_util_use($package) {
	
	$file = __DIR__.'/src/'.$package.'.php';
	
	if (! file_exists($file)) {
		trigger_error("Missing PHP-Util library '$package'.", E_USER_WARNING);
		return null;
	}
	
	require_once $file;
}

function php_util_use_function($function) {
	return php_util_use('fn/'.$function);
}

/** ================================
			Misc.
================================= */
php_util_use('miscellaneous');

/** ================================
			Strings
================================= */
php_util_use('str');

/** ================================
		Scalar Formatting
================================= */
php_util_use('format');

/** ================================
		  URL-Safe Base64
================================= */
php_util_use('base64-url');

/** ================================
		Misc. Functions
================================= */
php_util_use_function('to_seconds');

/** ================================
		Scalar Sanitization
================================= */
php_util_use('esc');
php_util_use('sanitize-validate');

/** ================================
			Arrays
================================= */
php_util_use('array');

/** ================================
			Filesystem
================================= */
php_util_use('path');
php_util_use('dir');
php_util_use('file');

/** ================================
			Callable
================================= */
php_util_use('callable');

/** ================================
			XML
================================= */
php_util_use('xml');
