<?php

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
	return filter_var($val, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_BACKTICK);
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
