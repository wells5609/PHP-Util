<?php
/**
 * @package wells5609/php-util
 * 
 * File and directory functions:
 * 
 *  * unslash
 *  * cleanpath
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
 * @package wells5609\php-util
 * 
 * CSV functions:
 *  * file_put_csv
 *  * file_get_csv
 *  * csv2array
 * 
 * ------------------------------ */

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

/**
 * Reads a CSV file and returns rows as an array.
 * 
 * @param string|resource $file			File path or handle opened with read capabilities.
 * @param boolean $first_row_is_data	Whether the first row is data (otherwise, used
 * 										as header names). Default false.
 * @param string|null $col_key_index	Column index to use as row array key. Default null.
 * @return array Array of rows, associative if $col_key_index given, otherwise indexed.
 */
function file_get_csv($file, $first_row_is_data = false, $col_key_index = null) {
	
	if (! is_resource($file)) {
			
		if (! is_readable($file)) {
			throw new InvalidArgumentException("Cannot read CSV from unreadable file '$file'.");
		}
		
		$file = fopen($file, 'rb');
	}
	
	$rows = array();
	
	if (! $first_row_is_data) {
		$headers = fgetcsv($file);
	}
	
	while ($line = fgetcsv($file)) {
	
		if (isset($headers)) {
			$data = array_combine($headers, $line);
		} else {
			$data =& $line;
		}
	
		if (isset($col_key_index) && isset($line[$col_key_index])) {
			$rows[$line[$col_key_index]] = $data;
		} else {
			$rows[] = $data;
		}
	}
	
	fclose($file);
	
	return $rows;
}


/**
 * Returns an array of items from a CSV string, file, or file handle.
 * 
 * @param string|resource $csv CSV string, file path, or file handle with read capability.
 * @param boolean $has_headers [Optional] Whether the first row of data is headers. Default true.
 * @return array Array of items with keys set to headers if $has_headers = true.
 */
function csv2array($csv, $has_headers = true) {
	
	if (is_resource($csv)) {
		$fh =& $csv;
	
	} else if (! is_file($csv)) {
		// Given string - write to temporary stream (2MB memory, then file)
		$fh = fopen('php://temp/maxmemory='.(2*1024*1024), 'wb+');
		fwrite($fh, $csv);
	
	} else if (! $fh = fopen($csv, 'rb')) {
		trigger_error("Could not open CSV file stream.", E_USER_NOTICE);
		return null;
	}
	
	rewind($fh);
	$data = array();
	
	if ($has_headers) {
		$headers = fgetcsv($fh);
		$num_headers = count($headers);
	}
	
	while($line = fgetcsv($fh)) {
	
		if ($has_headers) {
			// pad the values so array_combine doesnt choke
			$values = array_pad($line, $num_headers, '');
			$data[] = array_combine($headers, $values);
		
		} else {
			$data[] = $line;
		}
	}
	
	fclose($fh);
	
	return $data;
}


/** --------------------------------
 * @package wells5609\php-util
 * 
 * XML functions:
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
	phputil_use_class('Xml');
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
	phputil_use_class('Xml');
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

