PHP-Util
========

PHP utility function library.

##About
 * No userland dependencies
 * Windows and developer friendly


###Why
I found myself re-implementing the same helper functions/methods over and over again. I decided to stop doing that and created this library instead.


##Packages
The "packages" are just groupings of alike functions. 

A non-exhaustive listing:

####**Arrays**
Many `array_*()` functions, e.g.:
 * `array_pull()`
 * `array_key()` (get a key by relative position)
 * `array_map_keys()` (like `array_map()` but for keys)
 * `array_filter_keys()` (like `array_filter()` but for keys)

Array "dot-notation":
 * `array_get()`
 * `array_set()`
 * `array_unset()`
 * `array_isset()`

e.g.
```php
$a = array("some" => array("item" => array("key" => 1)));
array_get($a, "some.item.key") // returns "1"
```

####**String**
Several `str_*()` functions, such as: 
 * `str_endswith()` and `str_startswith()` (with case-sensitive option)
 * `str_sentences()` (sentence extractor)
 * `str_alnum()` (strip non-alphanumeric characters)
 * `str_numeric()` (detects and casts numeric strings to float or int)

URL-safe Base64 encoding and decoding: 
 * `base64_url_encode()`
 * `base64_url_decode()`

Simple common inflectors:
 * `str_pear_case()` (e.g. "Some_String_Like_This")
 * `str_snake_case()` (e.g. "some_string_like_this")
 * `str_studly_case()` (e.g. "SomeStringLikeThis")
 * `str_camel_case()` (e.g. "someStringLikeThis")

####**Format**
String formatting functions:
 * `str_format()` (generic string formatter)
 * `hash_format()` (e.g. for inserting "-" into UUIDs)
 * `phone_format()` (detects format based on length)
 * `bytes_format()` (option for SI or IEC)

####**Sanitize/Validate**
Wrappers for common uses of `filter_var()`:
 * `sanitize()`
 * `validate()`

####**Filesystem**
 * `is_abspath()`
 * `joinpath()`
 * `globr()` (recursive `glob()`)
 * `scandirr()` (recursive `scandir()`)

####**CSV**
 * `file_get_csv()` 
 * `file_put_csv()` 
 * `csv2array()`

####**XML**
 * `xml_write_document()`
 * `xml_write_element()`
 * `xml2array()`

####**Callables**
 * `result()`
 * `invoke()` (matches named or ordered parameters to a callable function signature)
 * `callable_id()` (human-readable callable identifiers)

####**Misc**
 * `is_xml()`
 * `is_json()`
 * `is_serialized()`
 * `object_to_array()` (no more `*prop => ugly` from casting to array)
 * `define_safe()` (define a constant only if undefined)
 * `pdo_dsn()` (creates a DSN string for PDO drivers)
