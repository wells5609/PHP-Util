<?php
/**
 * @package wells5609/php-util
 */

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
			return null;
	}
	
	return floatval($qty)*$factor;
}
