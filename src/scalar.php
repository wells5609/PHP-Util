<?php
/**
 * @package PHP-Util\Scalar
 */

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
 * Strips a string of non-alphanumeric characters.
 * 
 * @param string $string String to sanitize
 * @param string|null $extras Characters to allow in addition to alnum chars.
 * @return string Sanitized string containing only alnum (and any extra) characters.
 */
function esc_alnum($string, $extras = null) {
	if (! isset($extras) && ctype_alnum($string)) {
		return $string;
	}
	$pattern = '/[^a-zA-Z0-9'. (isset($extras) ? $extras : '') .']/';
	return preg_replace($pattern, '', $string);
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
 * If $val is a numeric string, converts to float or integer depending on 
 * whether a decimal point is present. Otherwise returns original.
 * 
 * @param string $val If numeric string, converted to integer or float.
 * @return scalar Value as string, integer, or float.
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
function str_between($string, $substr_start, $substr_end) {
	$str1 = explode($substr_start, $string);
	$str2 = explode($substr_end, $str1[1]);
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
function str_sentences($text, $num, $strip = false) {
	$text = strip_tags($text);
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
		$text = str_replace(
			$replace_keys = array_keys($replace), 
			$replace_vals = array_values($replace), 
			$text
		);
	}
	// get given number of strings delimited by ".", "!", or "?"
	preg_match('/^([^.!?]*[\.!?]+){0,'.$num.'}/', $text, $match);
	// replace the placeholders with originals
	return $strip ? str_replace($replace_vals, $replace_keys, $match[0]) : $match[0];
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
