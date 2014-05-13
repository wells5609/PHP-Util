PHP-Util
========

Low-level PHP functions for various common operations.

###Features
 * No dependencies
 * Tested on Unix and Windows systems
 * PHP 5.3+ (although most is 5.2-compatible), some back-compat for < 5.5

The library is split into four sub-packages:
 * Scalar
 * Array
 * Filesystem
 * Miscellaneous

In addition, some less useful (but still useful) functions are available on-demand.

##Scalar
Functions that operate on scalar inputs.

Non-exaustive list of included functions:
 * **`str_cast_numeric()`** - Casts and returns a numeric string as a float or integer.
 * **`str_startswith()`** - Determine if a string starts with another string, optionally case-insensitive.
 * **`str_endswith()`** - Determine if a string ends with another string, optionally case-insensitive.
 * **`str_between()`** - Extracts and returns the portion of a string between two other strings.
 * **`str_sentences()`** - Extracts and returns a given number of sentences from a string.
 * **`str_format()`** - Returns a formatted string, given some raw data and a template string.
 * **`phone_format()`** - Format a phone number based on the number of characters.
 * **`hash_format()`** - Format a hash/digest based on the number of characters.
 * **`bytes_format()`** - Formats a number given in bytes or bits to an SI (base 1000) or IEC (base 1024) unit.
 * **`esc_string()`** - Escapes a string using `filter_var()` using `FILTER_SANITIZE_STRING`, plus optional flags.
 * **`esc_int()`** - Escapes an integer using `filter_var()` using `FILTER_SANITIZE_NUMBER_INT`, plus optional flags.
 * **`esc_float()`** - Escapes a string using `filter_var()` using `FILTER_SANITIZE_NUMBER_FLOAT`, plus optional flags.
 * **`esc_ascii()`** - Escapes a string using `filter_var()` using `FILTER_SANITIZE_STRING` with `FILTER_STRIP_HIGH` and `FILTER_STRIP_BACKTICK` flags.
 * **`esc_alnum()`** - Strips non-alphanumeric characters, as well as any additional characters specified, from a given string.
 * **`is_json()`** - Returns true if given value is a valid JSON string.
 * **`to_seconds()`** - Returns the number of seconds (as float) of a given human-readable string representation (e.g. "1 day" returns 86400).
 * **`base64_url_encode()`** - Base64-encodes a string safe for URLs.
 * **`base64_url_decode()`** - Decodes a URL-safe Base64-encoded string.
 * **`generate_token()`** - Generates a verifiable seed-based token using a given or default hashing algorithm.
 * **`verify_token()`** - Verifies a given seed and token generated with `generate_token()`.

##Miscellaneous

 * **`define_default()`** - Defines a constant only if it is undefined.
 * **`id()`** - Returns argument unmodified.
 * **`invoke()`** - Invokes an arbitrary callable using an array of arguments, which needn't be correctly ordered.
 * **`result()`** - If given a closure or invokable object, returns result, otherwise returns argument unmodified.
 * **`callable_uid()`** - Returns a human-readable unique identifier for a callable.
  * **`classinfo()`** - Retrieves information about a class, such as vendor, namespaces, class name, parents, and interfaces.
 * **`xml_write_document()`** - Creates and returns an XML document as string given an array of data.
 * **`xml_write_element()`** - Adds an element to an XMLWriter document given an array of element data.
