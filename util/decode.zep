namespace Util;

class Decode
{

    /**
     * Decodes a JSON-encoded string into an object or array
     *
     * @param string $json A well-formed JSON string.
     * @param boolean $assoc [Optional] Whether to decode to an associative array. Default false.
     * @param int $depth [Optional] Depth to decode to. Default 512
     * @param int $flags [Optional] Bitwise flags for use in json_decode(). Default is 0
     * @return object|array|null JSON data decoded to object(s) or array(s).
     */
    public static function json(string! json, boolean assoc = false, int depth = 512, int flags = 0) -> object|array|null
    {
        if unlikely "" === json {
            return null;
        }

        if is_file(json) {
            let json = file_get_contents(json);
        }

        return json_decode(json, assoc, depth, flags);
    }

    /**
     * Decodes an XML string into an object or array.
     *
     * @param string $xml A well-formed XML string.
     * @param boolean $assoc [Optional] Decode to an associative array. Default false.
     * @return object|array|null XML data decoded to object(s) or array(s).
     */
    public static function xml(string! xml, boolean assoc = false) -> object|array|null
    {
        if unlikely "" === xml {
            return null;
        }

        if is_file(xml) {
            let xml = file_get_contents(xml);
        }

    	return json_decode(json_encode(simplexml_load_string(xml)), assoc);
    }

    /**
     * Returns an array or object of items from a CSV string.
     *
     * The string is written to a temporary stream so that fgetcsv() can be used.
     * This has the nice (side) effect of avoiding str_getcsv(), which is less
     * forgiving in its treatment of escape characters and delimeters.
     *
     * @param string $csv CSV string.
     * @param boolean $assoc [Optional] Return as associative arrays instead of objects. Default false.
     * @param mixed $headers [Optional] Array of headers, or boolean true if the first CSV row contains headers.
     * Headers will be used as the keys for the values in each row. Defaults to boolean true.
     * @return object|array|null
     */
    public static function csv(string! csv, boolean assoc = false, var headers = true) -> object|array|null
    {
        var data, handle, hasHeaders, numHeaders, line;

        if unlikely "" === csv {
            return null;
        }

        let data = [];
        let hasHeaders = headers ? true : false;

        if is_file(csv) {
            // Given a file, so just open read-only
            let handle = fopen(csv, "rb");
        } else {
        	// Open a temporary read/write stream using 2 MB of memory
        	let handle = fopen("php://temp/maxmemory=2097152", "wb+");
        	// Write the string to the temporary stream and rewind
        	fwrite(handle, csv);
        	rewind(handle);
        }

    	if hasHeaders {

            if typeof headers != "array" {
                let headers = fgetcsv(handle);
            }

            let numHeaders = count(headers);
        }

    	while ! feof(handle) {

            let line = fgetcsv(handle);

    		if hasHeaders {
    			// Pad row with empty strings in case end column(s) are blank
    			let line = array_combine(headers, array_pad(line, numHeaders, ""));
    		}

    		let data[] = assoc ? line : (object)line;
    	}

    	fclose(handle);

    	return assoc ? data : (object)data;
    }

}
