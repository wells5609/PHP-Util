PHP-Util
========

Zero-dependency PHP functions for various common tasks.

####About
 * No dependencies
 * Windows compatible
 * Requires PHP 5.3+
 * Back-compat for PHP < 5.5

**Sections**
 1. Miscellaneous
 2. Scalar handling
 3. Array handling
 4. Filesystem
 5. Callables
 6. XML
 
##Miscellaneous
**Functions:**
 * `classinfo()` - Retrieves information about a class, such as 'vendor', 'namespaces', and 'class' (the base class name).
 * `define_default()` - Defines a constant only if undefined. A shortcut for `if (! defined('CONST')) define('CONST', '')`.
 * `id()` - Returns argument unmodified - mostly for calling methods on a newly instantiated object without setting to a variable.

##Scalar handling
 * `scalarval()` - Returns the scalar value of a variable, if possible, optionally with some typecasting flags.
 * many more
 
##Array handling
 * `is_iterable()` - Returns true if variable can be used in a `foreach()` loop.
 * `is_arraylike()` - Returns true if variable can be accessed as an array.
 * a bunch more
 
##Filesystem
 * `fwritecsv()` - Writes an array of data as rows to a CSV file.
 * `cleanpath()` - Normalizes Windows filepaths and removes beginning and ending slashes.
 * `joinpath()` - Joins given path segments into one filepath.
 * `is_abspath()` - Returns true if given filepath is absolute.
 * `glob_recursive()` - Get files and directories in a given directory recursively to a given depth.
 * some more
 
##Callables
 * `invoke()` - Invokes an arbitrary callable using an array of arguments, which needn't be correctly ordered.
 * `result()` - If given a closure or invokable object, returns result, otherwise returns argument unmodified.
 * `callable_uid()` - Returns a human-readable unique identifier for a callable.
 
##XML
 * `xml_write_document()` - Creates and returns an XML document as string given an array of data.
 * `xml_write_element()` - Adds an element to an XMLWriter document given an array of element data.
