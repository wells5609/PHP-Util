PHP-Util
========

<<<<<<< HEAD
The missing utility functions for PHP.

##About
 * No dependencies
 * Windows compatible
 * For PHP 5.3+ with some back-compat for PHP < 5.5
 
##Packages
 * **Miscellaneous** - random functions that didn't fit anywhere else
 * **String** - Extra `str_*()` functions, such as `str_endswith()`, `str_sentences()`, and `str_format()`.
 * **Format** - String formatting functions: `hash_format()`, `phone_format()` and `bytes_format()`
 * **Sanitize/Validate** - `sanitize()` and `validate()` helper functions based off `filter_var()`.
 * **URL-safe Base64** - URL-safe Base64 encoding and decoding: `base64_url_encode()` and `base64_url_decode()`
 * **Filesystem paths** - `is_abspath()`, `joinpath()`, and `cleanpath()`
 * **Files** - `file_get_csv()` and `file_put_csv()`
 * **Directories** - Recursive directory functions: `globr()` and `scandirr()`
 * **XML** - `xml_write_document()`, `xml_write_element()` and `xml2array()`
 * **Arrays** - Many `array_*()` functions, like `array_pull()`, `array_key()`, `implode_nice` and `array_map_keys()`.
 * **Callables** - `result()`, `invoke()` and `callable_id()`

=======
Low-level PHP functions for various common operations.

###Features
 * No dependencies
 * Tested on Unix and Windows systems
 * PHP 5.3+ (although most functions are 5.2-compatible), some back-compat for < 5.5

The library is split into four sub-packages:
 * [Scalar](#scalar)
 * [Array](#array)
 * [Filesystem](#filesystem)
 * [Miscellaneous](#miscellaneous)

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

##Array
Functions that operate on arrays (some operate on iterable objects as well).

 * **`array_mpull()`** - Given an array of objects, create a new array with the value returned from a method call on each object.
 * **`array_ppull()`** - Same as above, except pull values from a property rather than a method.
 * **`array_kpull()`** Given an array of arrays, create a new array using a value from each array identified by its key/index.
 * **`array_mfilter()`** - Return a modified list of objects which return true for a given method. Optionally reverse the behavior.
 * **`array_key()`** - Get a key from an array given its position as expressed by an integer (positive or negative), or one of "first" or "last".
 * **`array_mergev()`** - Merge a vector (indexed array) of arrays.
 * **`array_merge_ref()`** - Merge arrays into the first by reference.
 * **`is_array_instance()`** - Returns true if all the objects in an array are an instance of a given class.
 * **`is_array_arrays()`** - Returns true if all the items in an array are arrays.
 * **`explode_trim()`** - Explodes a string into an array, trimming whitespace (or given chars) from each item.
 * **`implode_nice()`** - Implementation of `implode()` that allows you to use a different separator for the last item; good for natural lanugage lists.
 * **`array_column()`** - Simple back-compat for < 5.5


##Filesystem
Functions that operate on the filesystem or its components.


##Miscellaneous
Functions that didn't fit anywhere else.

 * **`define_default()`** - Defines a constant only if it is undefined.
 * **`id()`** - Returns argument unmodified.
 * **`invoke()`** - Invokes an arbitrary callable using an array of arguments, which needn't be correctly ordered.
 * **`result()`** - If given a closure or invokable object, returns result, otherwise returns argument unmodified.
 * **`callable_uid()`** - Returns a human-readable unique identifier for a callable.
  * **`classinfo()`** - Retrieves information about a class, such as vendor, namespaces, class name, parents, and interfaces.
 * **`xml_write_document()`** - Creates and returns an XML document as string given an array of data.
 * **`xml_write_element()`** - Adds an element to an XMLWriter document given an array of element data.
>>>>>>> FETCH_HEAD
