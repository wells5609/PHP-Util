<?php
/**
 * @package wells5609/php-util
 * 
 * URL-safe Base64 functions.
 */

/**
 * Base64 encode a string, safe for URL's.
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
	if ($m4 = (strlen($str) % 4)) {
		$str .= substr('====', $m4);
	}
	return base64_decode($str);
}
