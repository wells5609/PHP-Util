<?php
/**
 * @package wells5609/php-util
 * 
 * String functions:
 * 
 *  * str_startswith()
 *  * str_endswith()
 *  * str_alnum()
 *  * str_pear_case()
 *  * str_snake_case()
 *  * str_studly_case
 *  * str_camel_case()
 *  * str_numeric()
 *  * str_between()
 *  * str_sentences()
 *  * str_clean_unicode()
 *  * sql_escape_like()
 * 
 */

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
 * Strips non-alphanumeric characters from a string.
 * Add characters to $extras to preserve those as well.
 * Extra chars should be escaped for use in preg_*() functions.
 */
function str_alnum($string, $extras = null) {
	if (empty($extras) && ctype_alnum($string)) {
		return $string;
	}
	$pattern = '/[^a-zA-Z0-9'. (isset($extras) ? $extras : '') .']/';
	return preg_replace($pattern, '', $string);
}

/**
 * Converts a string to a PEAR-like class name. (e.g. "Http_Request2_Response")
 */
function str_pear_case($str) {
	$with_spaces = preg_replace('/[^a-zA-Z0-9]/', '_', trim(preg_replace('/[A-Z]/', ' $0', $str)));
	return preg_replace('/[_]{2,}/', '_', str_replace(' ', '_', ucwords($with_spaces)));
}

/**
 * Converts a string to "snake_case"
 */
function str_snake_case($str) {
	return strtolower(str_pear_case($str));
}

/**
 * Converts a string to "StudlyCaps"
 */
function str_studly_case($str) {
	return str_replace(' ', '', ucwords(trim(preg_replace('/[^a-zA-Z0-9]/', ' ', $str))));
}

/**
 * Converts a string to "camelCase"
 */
function str_camel_case($str) {
	return lcfirst(str_studly_case($str));
}

/**
 * If $val is a numeric string, converts to float or integer depending on 
 * whether a decimal point is present. Otherwise returns original.
 * 
 * @param string $val If numeric string, converted to integer or float.
 * @return scalar Value as string, integer, or float.
 */
function str_numeric($val) {
	
	if (! is_numeric($val) || ! is_string($val)) {
		return $val;
	}
	
	static $decimal;
	
	if (! isset($decimal)) {
		$loc = localeconv();
		$decimal = empty($loc['decimal_point']) ? '.' : $loc['decimal_point'];
	}
	
	return (false === strpos($val, $decimal)) ? intval($val) : floatval($val);
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

/**
 * Strips invalid unicode from a string.
 * 
 * @param string $str String.
 * @return string String stripped of invalid unicode.
 */
function str_clean_unicode($str) {
	static $mb;
	isset($mb) OR $mb = extension_loaded('mbstring');
	
	if ($mb) {		
		$encoding = mb_detect_encoding($str);
		if ('UTF-8' !== $encoding && 'ASCII' !== $encoding) {
			// temporarily unset mb substitute character and convert
			$mbsub = ini_set('mbstring.substitute_character', "none");
			$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
			ini_set('mbstring.substitute_character', $mbsub);
		}
	}
	
	return stripcslashes(preg_replace('/\\\\u([0-9a-f]{4})/i', '', $str));
}

/**
 * Escapes text for SQL LIKE special characters % and _.
 *
 * @param string $text The text to be escaped.
 * @return string text, safe for inclusion in LIKE query.
 */
function sql_like_escape($string) {
	return str_replace(array("%", "_"), array("\\%", "\\_"), $string);
}
