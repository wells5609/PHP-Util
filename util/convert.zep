namespace Util;

use RuntimeException;
use DomainException;

class Convert
{

	/**
	 * Convert a human-readable time unit description to seconds.
	 *
	 * COUNTEREXAMPLE
	 *   ttl = (60 * 60 * 24 * 32.5); // 32.5 days
	 * becomes:
	 *   ttl = Format::seconds("32.5 days");
	 *
	 * @author facebook/libphutil
	 * Edited by wells: always convert to seconds; allow decimals in units; use
	 * explode() instead of preg_match(); add week conversions; return float.
	 *
	 * @param string Human readable description of a time unit quantity.
	 * @return float Given unit in number of seconds.
	 */
	public static function toSeconds(string! arg) -> float
	{
		if unlikely ! memstr(arg, " ") {
			throw new RuntimeException("Unable to parse unit specification (expected in the form '5 days')");
		}

		var parts, qty, unit;
		int factor;

		let parts = explode(" ", arg, 2);
		let qty = parts[0],
			unit = parts[1];

		if ! is_numeric(qty) {
			throw new RuntimeException("Unable to parse unit specification (expected numeric quantity)");
		}

		switch substr(unit, 0, 3) {

			case "sec":
				let factor = 1;
				break;

			case "min":
				let factor = 60;
				break;

			case "hou":
				let factor = 3600;
				break;

			case "day":
				let factor = 86400;
				break;

			case "wee":
				let factor = 604800;
				break;

			default:
				throw new DomainException("Invalid unit given");
		}

		return strval(round(bcmul(qty, factor, "10"), 8));
	}

    /**
	 * Convert a temperature to another unit
	 *
	 * @param number quantity Temperature to convert given in degrees
	 * @param string from Given temperature unit: one of "C", "F", or "K"
	 * @param string to Temperature unit to convert to
	 * @return string Temperature in new unit
	 * @throws InvalidArgumentException if quantity is not a number
	 * @throws DomainException if either temperature unit is unknown
	 */
	public static function temp(string quantity, string! from, string! to)
    {
        var temp;

		if unlikely ! is_numeric(quantity) {
			throw new InvalidArgumentException("Quantity must be a number");
		}

		let to = strtoupper(to);

		switch strtoupper(from) {

			case "F":

				switch to {
					case "C" :
						// (quantity - 32) * (5/9);
						let temp = bcdiv(bcmul(bcsub(quantity, "32", "10"), "5", "10"), "9", "10");
                        break;
					case "K" :
						// (quantity - 32) * (5/9) + 273.15;
						let temp = bcadd(bcdiv(bcmul(bcsub(quantity, "32", "10"), "5", "10"), "9", "10"), "273.15", "10");
                        break;
					default:
						break;
				}

			case "C":

				switch to {
					case "F" :
						// (quantity * (9/5)) + 32;
						let temp = bcadd(bcdiv(bcmul(quantity, "9", "10"), "5", "10"), "32", "10");
                        break;
					case "K" :
						// quantity + 273.15;
						let temp = bcadd(quantity, "273.15", "10");
                        break;
					default:
						break;
				}

			case "K" :

				switch to {
					case "C" :
						// quantity - 273.15;
						let temp = bcsub(quantity, "273.15", "10");
                        break;
					case "F" :
						// (quantity - 273.15) * (9/5) + 32;
						let temp = bcadd(bcmul(bcsub(quantity, "273.15", "10"), bcdiv("9", "5", "10"), "10"), "32", "10");
                        break;
					default:
						break;
				}

			default :
				break;
		}

        if typeof temp == "null" {
            throw new DomainException("Unknown temperature unit");
        }

        return strval(round(temp, 8));
	}

}
