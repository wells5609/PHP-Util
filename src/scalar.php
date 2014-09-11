<?php
/** ----------------------------------------------------------------
 * @package wells5609/php-util
 * @subpackage String
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
 * -------------------------------------------------------------- */

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


/** ----------------------------------------------------------------
 * @subpackage Formatting
 * 
 *  * to_seconds()
 *  * str_format()
 *  * phone_format()
 *  * hash_format()
 *  * bytes_format()
 *
 * -------------------------------------------------------------- */

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
 * explode() instead of preg_match(); add week conversions; return float.
 * 
 * @param string Human readable description of a time unit quantity.
 * @return float Given unit in number of seconds.
 */
function to_seconds($description) {
	
	if (false === strpos($description, ' ')) {
		$msg = "Unable to parse unit specification (expected in the form '5 days'): given '$description'";
		throw new InvalidArgumentException($msg);
	}
	
	list($qty, $unit) = explode(' ', $description, 2);
	
	if (! is_numeric($qty)) {
		throw new RuntimeException("Unable to parse unit specification (expected numeric quantity): given '$qty'");
	}
	
	switch (substr($unit, 0, 3)) {
		case 'sec':
			$factor = 1;
			break;
		case 'min':
			$factor = 60;
			break;
		case 'hou': // hour
			$factor = 60 * 60;
			break;
		case 'day':
			$factor = 60 * 60 * 24;
			break;
		case 'wee': // week
			$factor = 7 * 60 * 60 * 24;
			break;
		default:
			throw new RuntimeException("Can not convert from the unit '$unit'.");
			return null;
	}
	
	return floatval($qty)*$factor;
}

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
 * Formats a phone number based on string length.
 * 
 * @param string $phone Unformatted phone number.
 * @return string Formatted phone number based on number of characters.
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
 * 
 * @param string $hash Hash/digest string.
 * @return string Formatted hash string.
 */
function hash_format($hash) {
		
	$string = str_replace(array('+','-',' '), '', $hash);
	
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
		default:
			return $string;
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

/** ----------------------------------------------------------------
 * @subpackage Filter
 * 
 * * sanitize()
 * * validate()
 * 
 * -------------------------------------------------------------- */

const INT = 'int';
const FLOAT = 'float';
const BOOL = 'bool';
const STRING = 'string';
const ASCII = 'ascii';
const URL = 'url';
const EMAIL = 'email';
const REGEX = 'regex';
const IP = 'ip';

/**
 * Sanitizes a variable against a given type.
 * 
 * @param mixed $variable Variable to sanitize.
 * @param int $type Variable type constant. Default 'STRING'.
 * @param int $flags Optional filter_var() flags. Default 0.
 * @return mixed Sanitized variable, or NULL with error if invalid type given.
 */
function sanitize($variable, $type = STRING, $flags = 0) {
	
	switch($type) {
		case STRING:
			$filter = FILTER_SANITIZE_STRING;
			break;
		case ASCII:
			$filter = FILTER_SANITIZE_STRING;
			$flags |= FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_BACKTICK;
			break;
		case INT:
			$filter = FILTER_SANITIZE_NUMBER_INT;
			break;
		case FLOAT:
			$filter = FILTER_SANITIZE_NUMBER_FLOAT;
			break;
		case URL:
			$filter = FILTER_SANITIZE_URL;
			break;
		case EMAIL:
			$filter = FILTER_SANITIZE_EMAIL;
			break;
		default:
			trigger_error("Unknown sanitize filter type '$type'.");
			return null;
	}
	
	return filter_var($variable, $filter, $flags);
}

/**
 * Validate a variable against a given type.
 * 
 * @param mixed $variable Input to validate.
 * @param int $type Variable type constant. Default STRING
 * @param int $flags Optional filter_var() flags. Default 0.
 * @return boolean True if input validates, otherwise false.
 */
function validate($variable, $type = STRING, $flags = 0) {
	
	switch($type) {
			
		case STRING:
			return (strlen($variable) === strlen(sanitize($variable)));
		
		case ASCII:
			return (strlen($variable) === strlen(sanitize($variable, ASCII)));
		
		case INT:
			if (is_int($variable)) {
				return true;
			}
			$filter = FILTER_VALIDATE_INT;
			break;
		
		case FLOAT:
			if (is_float($variable)) {
				return true;
			}
			$filter = FILTER_VALIDATE_FLOAT;
			break;
		
		case BOOL:
			if (is_bool($variable)) {
				return true;
			}
			$filter = FILTER_VALIDATE_BOOLEAN;
			break;
		
		case URL:
			$filter = FILTER_VALIDATE_URL;
			break;
		
		case EMAIL:
			$filter = FILTER_VALIDATE_EMAIL;
			break;
		
		case REGEX:
			$filter = FILTER_VALIDATE_REGEXP;
			break;
		
		case IP:
			$filter = FILTER_VALIDATE_IP;
			break;
		
		default:
			trigger_error("Unknown validate filter type '$type'.");
			return null;
	}
	
	return false !== filter_var($variable, $filter, $flags);
}
 