<?php

define('INT', 1);
define('FLOAT', 2);
define('BOOL', 4);
define('STRING', 8);
define('ASCII', 16);
define('URL', 32);
define('EMAIL', 64);
define('REGEX', 128);
define('IP', 256);

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

