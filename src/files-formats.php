<?php
/**
 * @package wells5609/php-util
 */

/**
 * @subpackage Filesystem
 * 
 *  * unslash
 *  * joinpath
 *  * is_abspath
 *  * add_include_path
 *  * remove_include_path
 *  * globr
 *  * scandirr
 * 
 */

/**
 * Strips "/" and "\" from beginning and end of string.
 * 
 * @param string $str Path
 * @return string Path with no slashes before or after.
 */
function unslash($str) {
	return trim($str, '/\\');
}

/**
 * Joins given filepaths into one concatenated string.
 * 
 * @param string ... Paths to join
 * @return string Joined path.
 */
function joinpath(/* $path1, $path2 [, $path3 [, ...]]] */) {
	return implode(DIRECTORY_SEPARATOR, array_map('unslash', func_get_args()));
}

/**
 * Returns true if given an absolute path, otherwise false.
 * 
 * @param string $path Filesystem path
 * @return boolean True if path is absolute, otherwise false.
 */
function is_abspath($path) {
	if ('\\' !== DIRECTORY_SEPARATOR) {
		return '/' === $path[0];
	}
	// Absolute paths on Windows must start with letter (local) 
	// or a double backslash (network)
	return (0 === strpos($path, '\\\\')) ? true : fnmatch('?:[/|\]*', $path, FNM_NOESCAPE);
}

/**
 * Adds a directory path to the include path.
 * 
 * @param string Directory path - will be passed through realpath()
 */
function add_include_path($path) {
	set_include_path(get_include_path().PATH_SEPARATOR.realpath($path).DIRECTORY_SEPARATOR);
}

/**
 * Removes a directory from the include path.
 * 
 * @param string Directory path - will be passed through realpath()
 */
function remove_include_path($path) {
	set_include_path(str_replace(PATH_SEPARATOR.realpath($path).DIRECTORY_SEPARATOR, '', get_include_path()));
}

/**
 * Returns files & directories in a given directory recursively.
 * 
 * Returned array is flattened - both keys and values are full filesystem paths.
 * 
 * Faster than scandirr(), but can consume lots of resources if used 
 * excessively with deep recursion.
 * 
 * Uses glob marking with substr() to check for subdirectories, which runs 
 * about twice as fast as the same function using is_dir()
 * 
 * @param string $dir Directory to scan.
 * @param int $levels Max directory depth level.
 * @param array &$glob The glob of flattend paths.
 * @param int $level Current directory level. Used interally.
 * @return array Flattened assoc. array of filepaths.
 */
function globr($dir, $levels = 5, array &$glob = array(), $level=0) {
	$dir = rtrim($dir, '/\\').'/*';
	foreach(glob($dir, GLOB_MARK) as $item) {
		if ($level < $levels && DIRECTORY_SEPARATOR === substr($item, -1)) {
			$level++;
			globr($item, $levels, $glob, $level);
		} else {
			$glob[$item] = $item;
		}
	}
	return $glob;
}

/**
 * Returns files & directories in a given directory recursively.
 *
 * Returned array is multi-dimensional with directory/file names used as keys.
 * 
 * @param string $dir Directory to scan.
 * @param int $levels Max directory depth level.
 * @param int $level Current depth level.
 * @return array Multi-dimensional array of files and directories.
 */
function scandirr($dir, $levels = 5, $level=0) {
	$dir = rtrim($dir, '/\\').'/';
	$dirs = array();
	foreach(scandir($dir) as $item) {
		if ('.' !== $item && '..' !== $item) {
			if ($level < $levels && is_dir($dir.$item)) {
				$level++;
				$dirs[$item] = scandirr($dir.$item, $levels, $level);
			} else {
				$dirs[$item] = $dir.$item;
			}
		}
	}
	return $dirs;
}


/** --------------------------------
 * @subpackage XML
 * 
 *  * xml_write_document()
 *  * xml_write_element()
 *  * xml_decode()
 * 
 * ------------------------------ */

/**
 * Creates and returns a new XML document as string.
 * 
 * @param array $data Data to format as XML. Nested arrays are preferred.
 * @param string $root_tag Tag to place at root of document. Default 'document'.
 * @param string $version XML version to use. Default '1.0'.
 * @param string $encoding XML encoding to use. Default 'UTF-8'.
 * @return string XML
 */
function xml_write_document(array $data, $root_tag = 'document', $version = '1.0', $encoding = 'UTF-8') {
	return \phputil\Xml::writeDocument($data, $root_tag, $version, $encoding);
}

/**
 * Adds an XML element to the given XMLWriter object.
 * 
 * @param XMLWriter $xml XMLWriter instance.
 * @param array $data Associative array of data.
 * @return void
 */
function xml_write_element(\XMLWriter $xml, array $data) {
	return \phputil\Xml::writeElement($xml, $data);
}

/**
 * Decodes an XML string into an object or array.
 * 
 * @param string $xml A well-formed XML string.
 * @param boolean $assoc [Optional] Decode as an associative array. Default false.
 * @return object|array XML data decoded as object(s) or array(s).
 */
function xml_decode($xml, $assoc = false) {
	return json_decode(json_encode(simplexml_load_string($xml)), $assoc);
}


/** --------------------------------
 * @subpackage CSV
 * 
 *  * csv_decode
 *  * file_get_csv
 *  * file_put_csv
 * 
 * ------------------------------ */

/**
 * Returns an array or object of items from a CSV string.
 * 
 * The string is written to a temporary stream so that fgetcsv() can be used. 
 * This has the nice (side) effect of avoiding str_getcsv(), which is less
 * forgiving in its treatment of escape characters and delimeters (and is 5.3+).
 * 
 * @param string $csv CSV string.
 * @param boolean $assoc [Optional] Whether to return as associative arrays rather 
 * than objects. Default false.
 * @param boolean $has_headers [Optional] Whether the first row contains header data. 
 * Headers will be used as the keys for the values in each subsequent row. Default true.
 * @return array Array of items with keys set to headers if $has_headers = true.
 */
function csv_decode($string, $assoc = false, $has_headers = true) {
	
	$fh = fopen('php://temp/maxmemory='.(2*1024*1024), 'wb+');
	
	fwrite($fh, $string);
	rewind($fh);
	
	$data = array();
	
	if ($has_headers) {
		$headers = fgetcsv($fh);
		$num_headers = count($headers);
	}
	
	while($line = fgetcsv($fh)) {
	
		if ($has_headers) {
			$line = array_combine($headers, array_pad($line, $num_headers, ''));
		}
		
		if (! $assoc) {
			$line = (object)$line;
		}
		
		$data[] = $line;
	}
	
	fclose($fh);
	
	return $data;
}

/**
 * Reads a CSV file and returns rows as an array.
 * 
 * @param string $file Filepath to CSV file.
 * @param boolean $has_headers Whether the first row is headers. Default true.
 * @return array Array of rows.
 */
function file_get_csv($file, $assoc = false, $has_headers = true) {
	
	if (! is_readable($file)) {
		trigger_error('Cannot read CSV from unreadable file: "'.$file.'".');
		return null;
	}
	
	$fh = fopen($file, 'rb');
	
	$rows = array();
	
	if ($has_headers) {
		$headers = fgetcsv($fh);
		$num_headers = count($headers);
	}
	
	while ($line = fgetcsv($fh)) {
	
		if ($has_headers) {
			// pad the values so array_combine doesnt choke
			$line = array_combine($headers, array_pad($line, $num_headers, ''));
		}
		
		if (! $assoc) {
			$line = (object)$line;
		}
		
		$rows[] = $line;
	}
	
	fclose($fh);
	
	return $rows;
}

/**
 * Writes an array of data as rows to a CSV file.
 * 
 * @param string|resource	Writable filepath, or a file resource with write access.
 * @param array				Array of data to write as CSV to file.
 * @param callable $row_cb	[Optional] Callback run for each row; Callback is passed 
 * 							each row data array. If modification is desired, define
 * 							the first callback parameter by reference.
 * @return boolean			True if success, false and error if unwritable file.
 */
function file_put_csv($file, array $data, $row_callback = null) {
	
	if (! is_resource($file)) {
	
		if (! is_writable($file)) {
			throw new InvalidArgumentException("Cannot write CSV to unwritable file '$file'.");
		}
	
		$file = fopen($file, 'wb');
	}
    
    foreach ($data as $i => $row) {
    
		if (isset($row_callback)) {
    		$row_callback($row, $i);
    	}
	
		fputcsv($file, $row);
	}
    
    fclose($file);
	
	return true;
}
