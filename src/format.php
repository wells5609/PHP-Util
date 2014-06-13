<?php
/**
 * @package wells5609/php-util
 * 
 * *_format() functions
 */

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
