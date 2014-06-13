<?php
/**
 * @package wells5609/php-util
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
