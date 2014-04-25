<?php
/**
 * @package PHP-Util\File
 */

/**
 * Converts backslashes ("\") to forward slashes ("/") and strips 
 * slashes from both ends of the given string.
 * 
 * Useful for normalizing Windows filepaths, or converting class 
 * namespaces to filepaths.
 * 
 * @param string $path String path (usually a filesystem path).
 * @return string Clean path.
 */
function cleanpath($path) {
	return trim(str_replace('\\', '/', $path), '/');
}

/**
 * Joins given filepaths into one concatenated string.
 * 
 * @param string ... Paths to join
 * @return string Joined path.
 */
function joinpath($path1/* [, $path2 [, ...]] */) {
	return implode(DIRECTORY_SEPARATOR, array_map('unslash', func_get_args()));
}

/**
 * Returns the full file extension for a given file path.
 * 
 * "Fixes" the behavior of pathinfo(), which returns only the last extension.
 * 
 * Example:
 * 
 * 	pathinfo("somefile.tar.gz", PATHINFO_EXTENSION); 
 * 		returns "gz"
 * 
 * 	file_extension("somefile.tar.gz");
 * 		returns "tar.gz"
 * 
 * @param string $path Filepath (does not need to exist on filesystem).
 * @return string File extension.
 */
function file_extension($path) {
	if (2 > substr_count($path, '.')) {
		return substr(strrchr($path, '.'), 1); // faster than pathinfo() with const
	}
	$info = pathinfo($path, PATHINFO_FILENAME|PATHINFO_EXTENSION); // filename minus name plus extension
	return substr($info['filename'], strpos($info['filename'], '.')+1).'.'.$info['extension'];
}

/**
 * Returns true if given an absolute path, otherwise false.
 * 
 * @param string $path Filesystem path
 * @return boolean True if path is absolute, otherwise false.
 */
function is_abspath($path) {
	// Absolute paths on Windows must start with letter (local) 
	// or a double backslash (network)
	if ('\\' === DIRECTORY_SEPARATOR) {
		return 0 === strpos($path, '\\\\')
			? true
			// backslash is default escape char in fnmatch()
			: fnmatch('?:[/|\]*', $path, FNM_NOESCAPE);
	} else {
		return file_exists($path);
	}
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
			trigger_error("Cannot write CSV to unwritable file '$file'.");
			return false;
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
 * @param string|null $column_as_key	Column index to use as row array key. Default null.
 * @return array Array of rows, associative if $column_as_key given, otherwise indexed.
 */
function file_get_csv($file, $first_row_is_data = false, $column_as_key = null) {
	if (! is_resource($file)) {
		if (! is_readable($file)) {
			trigger_error("Cannot read CSV from unreadable file '$file'.");
			return false;
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
		if (isset($column_as_key) && isset($line[$column_as_key])) {
			$rows[$line[$column_as_key]] = $data;
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
function glob_recursive($dir, $levels = 5, array &$glob = array(), $level=1) {
	$dir = rtrim($dir, '/\\').'/*';
	foreach( glob($dir, GLOB_MARK) as $item ) {
		// glob with GLOB_MARK uses system dir sep
		if ($level <= $levels && DIRECTORY_SEPARATOR === substr($item, -1)) {
			$level++;
			glob_recursive($item, $levels, $glob, $level);
		} else {
			$glob[$item] = $item;
		}
	}
	return $glob;
}

/**
 * Returns files & directories in a given directory, optionally recursive.
 *
 * Returned array is multi-dimensional with directory/file names used as keys.
 * 
 * @param string $dir Directory to scan.
 * @param int $levels Max directory depth level.
 * @param int $level Current depth level.
 * @return array Multi-dimensional array of files and directories.
 */
function scandir_recursive($dir, $levels = 5, $level=1) {
	$dir = rtrim($dir, '/\\').'/';
	$dirs = array();
	foreach( scandir($dir) as $item ) {
		if ('.' !== $item && '..' !== $item) {
			if ($level <= $levels && is_dir($dir.$item)) {
				$level++;
				$dirs[$item] = scandir_recursive($dir.$item, $levels, $level);
			} else {
				$dirs[$item] = $dir.$item;
			}
		}
	}
	return $dirs;
}
	
/**
 * Includes a file using include().
 * 
 * Useful for classes to include files in isolated scope
 * without resorting to closures.
 * 
 * @param string $file Path to file.
 * @param array $localize [Optional] Associative array of variables
 * to localize using extract(). Default null.
 * @return void
 */
function include_file($file, array $localize = null) {
	if (isset($localize)) {
		extract($localize, EXTR_REFS);
	}
	include $file;
}
