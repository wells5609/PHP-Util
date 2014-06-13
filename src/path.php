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
