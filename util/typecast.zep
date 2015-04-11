namespace Util;

use InvalidArgumentException;

/**
 * Typecast provides utilities to cast PHP variables to other types.
 */
class Typecast
{

	const CAST_NUMERIC = 0;
	const FORCE_STRING = 1;
	const IGNORE_ERROR = 5;

	/**
	 * The decimal point for the current locale
	 * @var string
	 */
	protected static decimalPoint;

	/**
	 * Converts a value to an array
	 *
	 * @param mixed
	 * @return array
	 */
	public static function toArray(var arg) -> array
	{

		if typeof arg == "array" {
			return arg;
		}

		if typeof arg == "object" {

			if method_exists(arg, "toArray") {
				return arg->toArray();
			}

			if (arg instanceof \Traversable) {
				return iterator_to_array(arg);
			}

			return get_object_vars(arg);
		}

		return (array)arg;
	}

	/**
	 * Converts a value to an object
	 *
	 * @param mixed
	 * @return object
	 */
	public static function toObject(var arg) -> object
	{

		if typeof arg == "object" {

			if (arg instanceof \stdClass) {
				return arg;
			}

			return (object)Typecast::toArray(arg);
		}

		return (object)arg;
	}

	/**
	 * Converts a value to arrays recursively.
	 *
	 * @param mixed
	 * @return array
	 */
	public static function toArrays(var arg) -> array
	{
		var arr, key, value;

		let arr = [];

		for key, value in Typecast::toArray(arg) {

			if (typeof value == "array" || typeof value == "object") {
				let arr[key] = Typecast::toArrays(value);
			} else {
				let arr[key] = value;
			}
		}

		return arr;
	}

	/**
	 * Converts a value to objects recursively.
	 *
	 * Objects are converted to instances of stdClass
	 *
	 * @param mixed
	 * @return object
	 */
	public static function toObjects(var arg) -> object
	{
		var arr, key, value;

		let arr = [];

		for key, value in Typecast::toArray(arg) {

			if (typeof value == "array" || typeof value == "object") {
				let arr[key] = Typecast::toObjects(value);
			} else {
				let arr[key] = value;
			}
		}

		return (object)arr;
	}

	/**
	 * Converts a variable to a boolean value
	 *
	 * @param mixed arg
	 * @return boolean
	 */
	public static function toBool(var arg) -> boolean
	{
		if typeof arg == "boolean" {
			return arg;
		}

		if is_numeric(arg) {
			return arg > 0;
		}

		if typeof arg == "string" {

			let arg = strtolower(arg);

			if "y" === arg || "yes" === arg || "true" === arg {
				return true;
			}

			if "n" === arg || "no" === arg || "false" === arg {
				return false;
			}
		}

		return (boolean)arg;
	}

	/**
	 * Convert value to a scalar value.
	 *
	 * @param string Value we"d like to be scalar.
	 * @param int $flags SCALAR_* flag bitwise mask.
	 * @return string
	 * @throws InvalidArgumentException if value can not be scalarized.
	 */
	public static function toScalar(var arg, int flags = 0) {

		switch gettype(arg) {

			case "string" :
				return (flags & self::CAST_NUMERIC) ? Typecast::strnum(arg) : arg;

			case "double" :
			case "integer" :
				return (flags & self::FORCE_STRING) ? (string)arg : arg;

			case "boolean" :
				return (flags & self::FORCE_STRING) ? (arg ? "1" : "0") : (arg ? 1 : 0);

			case "NULL" :
				return "";

			case "object" :
				if method_exists(arg, "__toString") {
					return arg->__toString();
				}
				// allow pass-thru
		}

		if (flags & self::IGNORE_ERROR) {
			return "";
		}

		throw new InvalidArgumentException("Value can not be scalar, given: '" . gettype(arg) . "'.");
	}

	/**
	 * If $val is a numeric string, converts to float or integer depending on
	 * whether a decimal point is present. Otherwise returns original.
	 *
	 * @param string $value If numeric string, converted to integer or float.
	 * @return scalar Value as string, integer, or float.
	 */
	public static function strnum(var value)
	{
		if typeof value == "string" && is_numeric(value) {
			return memstr(value, self::getDecimalPoint()) ? (float)value : (int)value;
		}

		return value;
	}

	/**
	 * Returns the decimal point for the current locale
	 *
	 * @return string
	 */
	public static function getDecimalPoint() -> string
	{
		if typeof self::decimalPoint == "null" {

			var loc;
			let loc = localeconv();

			let self::decimalPoint = isset loc["decimal_point"] ? loc["decimal_point"] : ".";
		}

		return self::decimalPoint;
	}

}
