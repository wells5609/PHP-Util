<?php
/**
 * @package wells5609/php-util
 * 
 * str_*() functions
 */
 
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
	
	$loc = localeconv();
	$decimal = empty($loc['decimal_point']) ? '.' : $loc['decimal_point'];
	
	return (false === strpos($val, $decimal)) ? intval($val) : floatval($val);
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

/**
 * Strips invalid unicode from a using.
 * 
 * @param string $str String to strip.
 * @return string String stripped of invalid unicode.
 */
function str_clean_unicode($str) {
	static $mb;
	
	if (! isset($mb)) {
		$mb = extension_loaded('mbstring');
	}
	
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
