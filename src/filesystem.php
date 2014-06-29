<?php

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
 * Converts backslashes ("\") to forward slashes ("/") and strips 
 * slashes from the end of given string.
 * 
 * Useful for normalizing Windows filepaths, or converting class 
 * namespaces to filepaths.
 * 
 * @param string $path String path (usually a filesystem path).
 * @return string Clean path.
 */
function cleanpath($path) {
	return rtrim(str_replace('\\', '/', $path), '/');
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
		return file_exists($path);
	}
	// Absolute paths on Windows must start with letter (local) 
	// or a double backslash (network)
	return 0 === strpos($path, '\\\\') 
		? true 
		: fnmatch('?:[/|\]*', $path, FNM_NOESCAPE);
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
	
	if (! $first_row_is_data) {
		$headers = fgetcsv($file);
	}
	
	$rows = array();
	
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
 * Returns files & directories in a given directory recursively.
 * 
 * Returned array is flattened - both keys and values are full filesystem paths.
 * 
 * Faster than scandir_recursive(), but can consume lots of resources if used 
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
