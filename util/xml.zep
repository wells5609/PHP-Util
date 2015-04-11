namespace Util;

use XMLWriter;

class Xml
{

	/**
	 * Returns an XML document as a string.
	 *
	 * @param array $data Document data.
	 * @param string $root_tag Root document element. Default "XML"
	 * @param string $version XML version. Default "1.0"
	 * @param string $encoding XML encoding. Default "UTF-8"
	 * @return string XML document as string
	 */
	public static function writeDocument(array data, string root_tag = "XML", string version = "1.0", string encoding = "UTF-8") -> string
	{
        var writer;
        let writer = new XMLWriter();

        writer->openMemory();

		writer->startDocument(version, encoding);
		writer->startElement(root_tag);

		Xml::writeElement(writer, data);

        writer->endElement();
		writer->endDocument();

		return writer->outputMemory(true);
	}

	/**
	 * Writes an element.
	 *
	 * @param \XMLWriter $writer An XMLWriter instance.
	 * @param array $data Data to write to the document.
	 * @return void
	 */
	public static function writeElement(<XMLWriter> writer, array data) -> void
	{
		var key, value;

		for key, value in data {

			let key = Sanitize::alnum(key);

			if is_numeric(key) {
				let key = "Item_" . key;
			}

			if typeof value == "object" {
				let value = Typecast::toArray(value);
			}

			if typeof value == "array" {

				if isset value["@tag"] {
					let key = strval(value["@tag"]);
					unset value["@tag"];
				}

				writer->startElement(key);

				if isset value["@attributes"] && typeof value["@attributes"] == "array" {

					var k, v;

					for k, v in array_unique(value["@attributes"]) {
						writer->writeAttribute(k, v);
					}

					unset value["@attributes"];
				}

				Xml::writeElement(writer, value);

				writer->endElement();

			} elseif is_scalar(value) {
				writer->writeElement(key, htmlentities(html_entity_decode(value), ENT_XML1|ENT_DISALLOWED));
			}
		}
	}

}
