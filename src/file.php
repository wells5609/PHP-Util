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
