<?php
/**
 * @package PHP-Util
 */

/** ================================
			Strings
================================= */

/**
 * If $val is a numeric string, converts to float or integer depending on 
 * whether a decimal point is present. Otherwise returns original.
 * 
 * @param string $val If numeric string, converted to integer or float.
 * @return scalar Value as string, integer, or float.
 */
function str_cast_numeric($val) {
	if (is_numeric($val) && is_string($val)) {
		return false === strpos($val, DECIMAL_POINT) 
			? intval($val)
			: floatval($val);
	}
	return $val;
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
function str_between($string, $sub_start, $sub_end) {
	$str1 = explode($sub_start, $string);
	$str2 = explode($sub_end, $str1[1]);
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
function str_sentences($string, $num, $strip = false) {
	$string = strip_tags($string);
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
		$string = str_replace(
			$replace_keys = array_keys($replace), 
			$replace_vals = array_values($replace), 
			$string
		);
	}
	// get given number of strings delimited by ".", "!", or "?"
	preg_match('/^([^.!?]*[\.!?]+){0,'.$num.'}/', $string, $match);
	// replace the placeholders with originals
	return $strip ? str_replace($replace_vals, $replace_keys, $match[0]) : $match[0];
}

/** ================================
		Scalar Formatting
================================= */

/**
 * Formats a string by injecting non-numeric characters into
 * the string in the positions they appear in the template.
 *
 * @param string $string The string to format
 * @param string $template String format to apply
 * @return string Formatted string.
 */
function str_format($string, $template) {

	$result = '';
	$fpos = $spos = 0;

	while ((strlen($template) - 1) >= $fpos) {
		if (ctype_alnum(substr($template, $fpos, 1))) {
			$result .= substr($string, $spos, 1);
			$spos++;
		} else {
			$result .= substr($template, $fpos, 1);
		}
		$fpos++;
	}
	
	return $result;
}

/**
 * Formats a phone number based on string lenth.
 */
function phone_format($phone) {
	
	// remove any pre-existing formatting characters
	$string = str_replace(array('(',')','+','-',' '), '', $phone);
	
	switch(strlen($string)) {
		case 7:
			$tmpl = '000-0000';
			break;
		case 10:
			$tmpl = '(000) 000-0000';
			break;
		case 11:
			$tmpl = '+0 (000) 000-0000';
			break;
		case 12:
			$tmpl = '+00 00 0000 0000';
			break;
	}
	
	return str_format($string, $tmpl);
}

/**
 * Formats a hash/digest based on string length.
 */
function hash_format($hash) {
		
	$string = str_replace(array('(',')','+','-',' '), '', $hash);
	
	switch(strlen($string)) {
		case 16:
			$tmpl = '00000000-0000-0000';
			break;
		case 24:
			$tmpl = '00000000-0000-0000-00000000';
			break;
		case 32:
			$tmpl = '00000000-0000-0000-0000-000000000000';
			break;
		case 40:
			$tmpl = '00000000-0000-0000-00000000-0000-0000-00000000';
			break;
		case 48:
			$tmpl = '00000000-0000-00000000-0000-0000-00000000-0000-00000000';
			break;
	}
	
	return str_format($string, $tmpl);
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
		Scalar Sanitization
================================= */

/**
 * Sanitizes a string using filter_var() with FILTER_SANITIZE_STRING.
 * 
 * @param scalar $val String to filter.
 * @param int $filter_flags Bitwise FILTER_FLAG_* flags. Default: 0
 * @return string Sanitized string.
 */
function esc_string($val, $flags = 0) {
	return filter_var($val, FILTER_SANITIZE_STRING, $flags);
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
	if (! isset($extra) && ctype_alnum($string)) {
		return $string;
	}
	$pattern = '/[^a-zA-Z0-9'. (isset($extra) ? $extra : '') .']/';
	return preg_replace($pattern, '', $string);
}

/**
 * Sanitizes a float using filter_var() with FILTER_SANITIZE_NUMBER_FLOAT.
 * 
 * @param scalar $val Value to filter.
 * @param int $filter_flags Bitwise FILTER_FLAG_* flags. Default: 0
 * @return float Sanitized float value.
 */
function esc_float($val, $flags = 0) {
	return filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, $flags);
}

/**
 * Sanitizes an integer using filter_var() with FILTER_SANITIZE_NUMBER_INT.
 * 
 * @param scalar $val Value to filter.
 * @param int $filter_flags Bitwise FILTER_FLAG_* flags. Default: 0
 * @return int Sanitized integer value.
 */
function esc_int($val, $flags = 0) {
	return filter_var($val, FILTER_SANITIZE_NUMBER_INT, $flags);
}

/** ================================
		Misc. Scalar Functions
================================= */

/**
 * Returns true if given string is valid JSON.
 * 
 * @param string $str String to test.
 * @return boolean True if string is JSON, otherwise false.
 */
function is_json($str) {
	$json = @json_decode($str, true);
	return is_array($json);
}

/**
 * Convert a human-readable time unit description to seconds.
 * 
 * COUNTEREXAMPLE
 *   $ttl = (60 * 60 * 24 * 32.5); // 32.5 days
 * becomes:
 *   $ttl = to_seconds('32.5 days');
 *
 * @author facebook/libphutil 
 * Edited by wells: always convert to seconds; allow decimals in units; use
 * substr() over preg_match(); add week conversions; return float.
 * 
 * @param string Human readable description of a time unit quantity.
 * @return float Given unit in number of seconds.
 */
function to_seconds($description) {
	
	if (false === $pos = strpos($description, ' ')) {
		$msg = "Unable to parse unit specification (expected a specification in the form '5 days'): given '$description'";
		throw new InvalidArgumentException($msg);
	}
	
	$qty = substr($description, 0, $pos);
	$unit = substr($description, $pos+1);
	
	if (! is_numeric($qty)) {
		throw new InvalidArgumentException("Unable to parse unit specification (expected numeric quantity): given '$qty'");
	}

	switch ($unit) {
		case 'second':
		case 'seconds':
			$factor = 1;
			break;
		case 'minute':
		case 'minutes':
			$factor = 60;
			break;
		case 'hour':
		case 'hours':
			$factor = 60 * 60;
			break;
		case 'day':
		case 'days':
			$factor = 60 * 60 * 24;
			break;
		case 'week':
		case 'weeks':
			$factor = 7 * 60 * 60 * 24;
			break;
		default:
			throw new InvalidArgumentException("Can not convert from the unit '$unit'.");
	}
	
	return floatval($qty)*$factor;
}

/** ================================
		  URL-Safe Base64
================================= */

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
		  Tokens/Nonces
================================= */

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
	empty($algo) and $algo = defined('PHPUTIL_TOKEN_ALGO') ? PHPUTIL_TOKEN_ALGO : 'sha1';
	$hmac_key = defined('PHPUTIL_TOKEN_HMAC_KEY') ? PHPUTIL_TOKEN_HMAC_KEY 
		: '3tj;,#K3+H%&a?*c*K8._O]~K_h%k &h3#I/pv#Rtoi,Iul84I/kg*J=Kk8fb0av';
	return hash_hmac($algo, $seed, $hmac_key);
}

/**
 * Verifies a token with seed.
 */
function verify_token($token, $seed, $algo = null) {
	return $token === generate_token($seed, $algo);
}
