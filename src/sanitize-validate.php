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

function validate($variable, $type = STRING, $flags = 0) {
	
	switch($type) {
		case STRING:
			return strlen($variable) === strlen(sanitize($variable, STRING));
		case ASCII:
			return strlen($variable) === strlen(sanitize($variable, ASCII));
		case INT:
			$filter = FILTER_VALIDATE_INT;
			break;
		case FLOAT:
			$filter = FILTER_VALIDATE_FLOAT;
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
		case BOOL:
			$filter = FILTER_VALIDATE_BOOLEAN;
			break;
		default:
			trigger_error("Unknown validate filter type '$type'.");
			return null;
	}
	
	return filter_var($variable, $filter, $flags);
}
