<?php
/**
 * @package wells5609/php-util
 * 
 * CSV functions:
 * 
 *  * file_put_csv
 *  * file_get_csv
 *  * csv2array
 * 
 */

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
		// use 4MB of memory then file
		$fh = fopen('php://temp/maxmemory='.(4*1024*1024), 'wb+');
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

