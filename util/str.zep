namespace Util;

class Str
{

	/**
	 * Last error
	 * @var string
	 */
	public static lastError;

    /**
     * Returns true if $haystack starts with $needle.
     *
     * @param string $haystack String to search within.
     * @param string $needle String to find.
     * @param boolean $case_sensitive Whether to check case-sensitive. Default true.
     * @return boolean
     */
    public static function startsWith(string haystack, string needle, boolean case_sensitive = true) {
    	return starts_with(haystack, needle, case_sensitive);
    }

    /**
     * Returns true if $haystack ends with $needle.
     *
     * @param string $haystack String to search within.
     * @param string $needle String to find.
     * @param boolean $case_sensitive Whether to check case-sensitive. Default true.
     * @return boolean
     */
    public static function endsWith(string haystack, string needle, boolean case_sensitive = true) {
    	return ends_with(haystack, needle, case_sensitive);
    }

	/**
	 * Converts a string to a PEAR-like class name. (e.g. "Http_Request2_Response")
	 *
	 * @param string
	 * @return string
	 */
	public static function pearCase(string str) -> string
	{
		var pearStr;

		let pearStr = preg_replace("/[A-Z]/", " $0", str);
		let pearStr = preg_replace("/[^a-zA-Z0-9]/", " ", trim(pearStr));
		let pearStr = preg_replace("/[_]{2,}/", "_", str_replace(" ", "_", ucwords(pearStr)));

		return pearStr;
	}

	/**
	 * Converts a string to "snake_case"
	 *
	 * @param string
	 * @return string
	 */
	public static function snakeCase(string str) -> string
	{
		return strtolower(Str::pearCase(str));
	}

	/**
	 * Converts a string to "StudlyCaps"
	 *
	 * @param string
	 * @return string
	 */
	public static function studlyCase(string str) -> string
	{
		return str_replace(" ", "", ucwords(trim(preg_replace("/[^a-zA-Z0-9]/", " ", str))));
	}

	/**
	 * Converts a string to "camelCase"
	 *
	 * @param string
	 * @return string
	 */
	public static function camelCase(string str) -> string
	{
		return lcfirst(Str::studlyCase(str));
	}

	/**
	 * Returns whether the given variable is a valid JSON string.
	 *
	 * @param string
	 * @return boolean
	 */
	public static function isJson(string str) -> boolean
	{
		if likely "" !== str {

			json_decode(str);

			if json_last_error() === JSON_ERROR_NONE {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks whether the given value is a valid XML string.
	 *
	 * @param string $arg Value to check if XML.
	 * @return boolean TRUE if value is a valid XML string, otherwise false.
	 */
	public static function isXml(string str) -> boolean
	{
        var xmlErrors, boolVal;

        if ! starts_with(str, "<?xml ") {
            return false;
        }

		let xmlErrors = libxml_use_internal_errors(true);
		let boolVal = (simplexml_load_string(str) instanceof \SimpleXMLElement) && false === libxml_get_last_error();

		libxml_use_internal_errors(xmlErrors);

		return boolVal;
	}

	/**
	 * Checks whether the given value is a valid serialized string.
	 *
	 * @param string $data Value to check if serialized
	 * @return boolean TRUE If value is a valid serialized string, otherwise false.
	 */
	public static function isSerialized(string str) -> boolean
	{
		if unlikely "" === str {
			return false;
		}

		if "N;" === str {
			return true;
		}

		// Reset last error
		let self::lastError = null;

		set_error_handler("Utils\\Str::handleError");
		unserialize(str);
		restore_error_handler();

		if typeof self::lastError == "null" {
			// No error occurred, so string is serial
			return true;
		}

		return false;
	}

	/**
	 * Formats a string by injecting non-numeric characters into
	 * the string in the positions they appear in the template.
	 *
	 * @param string $string The string to format
	 * @param string $template String format to apply
	 * @return string Formatted string.
	 */
	public static function format(string str, string template) -> string
	{
		var result, chr;
		int fpos, spos;

		let result = "", fpos = 0, spos = 0;

		while (strlen(template) - 1) >= fpos {

			let chr = substr(template, fpos, 1);

			if ! ctype_alnum(chr) {
				let result .= chr;
			} else {
				let result .= substr(str, spos, 1);
				let spos++;
			}

			let fpos++;
		}

		return result;
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
    public static function between(string! str, string! substr_start, string! substr_end) -> string|null
    {
        var str1, str2;

    	let str1 = explode(substr_start, str);

        if ! isset str1[1] {
            return null;
        }

    	let str2 = explode(substr_end, str1[1]);

        return trim(str2[0]);
    }

    /**
     * Get a given number of sentences from a string.
     *
     * @param string $text The full string of sentences.
     * @param int $num Number of sentences to return. Default 1.
     * @param boolean|array $strip_abbr Whether to strip abbreviations (they break the function).
     * Pass an array to account for those abbreviations as well.
     * @return string Given number of sentences.
     */
    public static function sentences(string str, int num = 1, var strip_abbr = false) -> string|null
    {
    	var replacements, strip, replace_keys, replace_vals, matches;

    	let str = strip_tags(str);
		let matches = [];

    	if strip_abbr {

            // brackets are for uniqueness - if we just removed the
    		// dots, then "Mr" would match "Mrs" when we reconvert.
    		let replacements = [
    			"Dr.": "<Dr>",
    			"Mrs.": "<Mrs>",
    			"Mr.": "<Mr>",
    			"Ms.": "<Ms>",
    			"Co.": "<Co>",
    			"Ltd.": "<Ltd>",
    			"Inc.": "<Inc>"
    		];

    		// add extra strings to strip
    		if typeof strip_abbr == "array" {

                for strip in strip_abbr {
    				let replacements[strip] = "<" . str_replace(".", "", strip) . ">";
    			}
    		}

    		// set the key/value vars and replace with placeholders
            let replace_keys = array_keys(replacements);
            let replace_vals = array_values(replacements);

    		let str = str_replace(replace_keys, replace_vals, str);
    	}

    	// get num number of sentences delimited by ".", "!", or "?"
    	if ! preg_match("/^([^.!?]*[\.!?]+){0," . num . "}/", str, matches) {
            return null;
        }

        // replace the placeholders with originals
        return strip_abbr ? str_replace(replace_vals, replace_keys, matches[0]) : matches[0];
    }

    /**
     * Class error handler
     *
     * @param int errorNum
     * @param string errorMsg
     */
	public static function handleError(int errorNum, string errorMsg) {
		let self::lastError = errorNum . ": " . errorMsg;
	}

}
