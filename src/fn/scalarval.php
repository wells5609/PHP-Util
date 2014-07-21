<?php

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
			return ($flags & SCALAR_CAST_NUMERIC) ? str_numeric($var) : $var;
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
