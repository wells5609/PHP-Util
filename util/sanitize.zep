namespace Util;

/**
 * Sanitize provides utilities to sanitize PHP variables.
 */
class Sanitize
{

	/**
	 * Sanitizes a string value
	 *
	 * @param string str
	 * @param int flags [Optional] Sanitize flags
	 * @return string
	 */
	public static function str(string str, int flags = 0) -> string
	{
		return filter_var(str, FILTER_SANITIZE_STRING, flags);
	}

	/**
	* Sanitizes an integer value
	*
	* @param scalar value
	* @param int flags [Optional] Sanitize flags
	* @return int
	*/
	public static function numInt(var value, int flags = 0) -> int
	{
		return filter_var(value, FILTER_SANITIZE_NUMBER_INT, flags);
	}

	/**
	 * Sanitizes a float value
	 *
	 * @param scalar value
	 * @param int flags [Optional] Sanitize flags
	 * @return float
	 */
	public static function numFloat(var value, int flags = 0) -> float
	{
		return filter_var(value, FILTER_SANITIZE_NUMBER_FLOAT, flags);
	}

	/**
	 * Sanitizes a string to contain only ASCII characters
	 *
	 * @param string $str
	 * @return string
	 */
	public static function ascii(string str) -> string
	{
		return filter_var(
			preg_replace("/[\x01-\x08\x0B-\x1F]/", "", html_entity_decode(str, ENT_QUOTES, "UTF-8")),
			FILTER_SANITIZE_STRING,
			FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK
		);
	}

	/**
	 * Sanitizes a URL, decoding using rawurldecode() and filter_var().
	 *
	 * @param string url URL, possibly containing encoded characters
	 * @param int flags [Optional] Optional filter_var() flags
	 * @return string Sanitized URL with "%##" characters translated
	 */
	public static function url(string url, int flags = 0) -> string
	{
		return filter_var(rawurldecode(url), FILTER_SANITIZE_URL, flags);
	}

	/**
	 * Strips non-alphabetic characters from a string.
	 *
	 * @param string
	 * @return string
	 */
	public static function alpha(string str) -> string
	{
		return ctype_alpha(str) ? str : preg_replace("/[^a-zA-Z]/", "", str);
	}

	/**
	 * Strips non-alphanumeric characters from a string.
	 *
	 * @param string
	 * @return string
	 */
	public static function alnum(string str) -> string
	{
		return ctype_alnum(str) ? str : preg_replace("/[^a-zA-Z0-9]/", "", str);
	}

	/**
	 * Strips invalid unicode from a string.
	 *
	 * @param string
	 * @return string
	 */
	public static function unicode(string str) -> string
	{
		if function_exists("mb_convert_encoding") {

			var encoding;
			let encoding = mb_detect_encoding(str);

			if "ASCII" !== encoding {

				var subChr;
				let subChr = ini_set("mbstring.substitute_character", "none");

				let str = mb_convert_encoding(str, "UTF-8", "UTF-8");

				ini_set("mbstring.substitute_character", subChr);
			}
		}

		return stripcslashes(preg_replace("/\\\\u([0-9a-f]{4})/i", "", str));
	}

	/**
	 * Sanitizes a string to a "slug" format: lowercase alphanumeric string with given separator.
	 *
	 * @param string $string Dirty string.
	 * @param string $separator [Optional] Character used to replace non-alphanumeric characters. Default "-".
	 * @return string Slugified string.
	 */
	public static function slug(string str, string separator = "-") -> string
	{
		var slug;
		let slug = preg_replace("#[\"\'\’\x01-\x08\x0B-\x1F]#", "", Sanitize::ascii(str));
		let slug = preg_replace("#[/_|+ -]+#u", separator, preg_replace("#[^a-z0-9]#i", separator, slug));

		return strtolower(trim(slug, separator));
	}

	/**
	 * Removes single and double quotes from a string.
	 *
	 * @param string
	 * @return string
	 */
	public static function stripQuotes(string str) -> string
	{
		return preg_replace("/[\"\'\’]/", "", str);
	}

	/**
	 * Removes non-printing ASCII control characters from a string.
	 *
	 * @param string
	 * @return string
	 */
	public static function stripControl(string str) -> string
	{
		return preg_replace("/[\x01-\x08\x0B-\x1F]/", "", str);
	}

	/**
	 * Escapes text for SQL LIKE special characters % and _.
	 *
	 * @param string $text The text to be escaped.
	 * @return string text, safe for inclusion in LIKE query.
	 */
	public static function sqlLike(string str) {
		return str_replace(["%", "_"], ["\\%", "\\_"], str);
	}

}
