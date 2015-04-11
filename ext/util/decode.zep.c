
#ifdef HAVE_CONFIG_H
#include "../ext_config.h"
#endif

#include <php.h>
#include "../php_ext.h"
#include "../ext.h"

#include <Zend/zend_operators.h>
#include <Zend/zend_exceptions.h>
#include <Zend/zend_interfaces.h>

#include "kernel/main.h"
#include "kernel/memory.h"
#include "kernel/operators.h"
#include "kernel/fcall.h"
#include "kernel/file.h"
#include "kernel/string.h"
#include "ext/spl/spl_exceptions.h"
#include "kernel/exception.h"
#include "kernel/array.h"


ZEPHIR_INIT_CLASS(Util_Decode) {

	ZEPHIR_REGISTER_CLASS(Util, Decode, util, decode, util_decode_method_entry, 0);

	return SUCCESS;

}

/**
 * Decodes a JSON-encoded string into an object or array
 *
 * @param string $json A well-formed JSON string.
 * @param boolean $assoc [Optional] Whether to decode to an associative array. Default false.
 * @param int $depth [Optional] Depth to decode to. Default 512
 * @param int $flags [Optional] Bitwise flags for use in json_decode(). Default is 0
 * @return object|array|null JSON data decoded to object(s) or array(s).
 */
PHP_METHOD(Util_Decode, json) {

	zephir_nts_static zephir_fcall_cache_entry *_2 = NULL;
	int depth, flags, ZEPHIR_LAST_CALL_STATUS;
	zend_bool assoc;
	zval *json_param = NULL, *assoc_param = NULL, *depth_param = NULL, *flags_param = NULL, _0, *_1 = NULL, *_3, _4, _5;
	zval *json = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 3, &json_param, &assoc_param, &depth_param, &flags_param);

	if (unlikely(Z_TYPE_P(json_param) != IS_STRING && Z_TYPE_P(json_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'json' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(json_param) == IS_STRING)) {
		zephir_get_strval(json, json_param);
	} else {
		ZEPHIR_INIT_VAR(json);
		ZVAL_EMPTY_STRING(json);
	}
	if (!assoc_param) {
		assoc = 0;
	} else {
		assoc = zephir_get_boolval(assoc_param);
	}
	if (!depth_param) {
		depth = 512;
	} else {
		depth = zephir_get_intval(depth_param);
	}
	if (!flags_param) {
		flags = 0;
	} else {
		flags = zephir_get_intval(flags_param);
	}


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_STRING(&_0, "", 0);
	if (unlikely(ZEPHIR_IS_IDENTICAL(&_0, json))) {
		RETURN_MM_NULL();
	}
	ZEPHIR_CALL_FUNCTION(&_1, "is_file", &_2, json);
	zephir_check_call_status();
	if (zephir_is_true(_1)) {
		ZEPHIR_INIT_VAR(_3);
		zephir_file_get_contents(_3, json TSRMLS_CC);
		zephir_get_strval(json, _3);
	}
	ZEPHIR_SINIT_VAR(_4);
	ZVAL_LONG(&_4, depth);
	ZEPHIR_SINIT_VAR(_5);
	ZVAL_LONG(&_5, flags);
	zephir_json_decode(return_value, &(return_value), json, zephir_get_intval((assoc ? ZEPHIR_GLOBAL(global_true) : ZEPHIR_GLOBAL(global_false)))  TSRMLS_CC);
	RETURN_MM();

}

/**
 * Decodes an XML string into an object or array.
 *
 * @param string $xml A well-formed XML string.
 * @param boolean $assoc [Optional] Decode to an associative array. Default false.
 * @return object|array|null XML data decoded to object(s) or array(s).
 */
PHP_METHOD(Util_Decode, xml) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_2 = NULL;
	zend_bool assoc;
	zval *xml_param = NULL, *assoc_param = NULL, _0, *_1 = NULL, *_3 = NULL, *_4 = NULL;
	zval *xml = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &xml_param, &assoc_param);

	if (unlikely(Z_TYPE_P(xml_param) != IS_STRING && Z_TYPE_P(xml_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'xml' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(xml_param) == IS_STRING)) {
		zephir_get_strval(xml, xml_param);
	} else {
		ZEPHIR_INIT_VAR(xml);
		ZVAL_EMPTY_STRING(xml);
	}
	if (!assoc_param) {
		assoc = 0;
	} else {
		assoc = zephir_get_boolval(assoc_param);
	}


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_STRING(&_0, "", 0);
	if (unlikely(ZEPHIR_IS_IDENTICAL(&_0, xml))) {
		RETURN_MM_NULL();
	}
	ZEPHIR_CALL_FUNCTION(&_1, "is_file", &_2, xml);
	zephir_check_call_status();
	if (zephir_is_true(_1)) {
		ZEPHIR_INIT_VAR(_3);
		zephir_file_get_contents(_3, xml TSRMLS_CC);
		zephir_get_strval(xml, _3);
	}
	ZEPHIR_INIT_NVAR(_3);
	ZEPHIR_CALL_FUNCTION(&_4, "simplexml_load_string", NULL, xml);
	zephir_check_call_status();
	zephir_json_encode(_3, &(_3), _4, 0  TSRMLS_CC);
	zephir_json_decode(return_value, &(return_value), _3, zephir_get_intval((assoc ? ZEPHIR_GLOBAL(global_true) : ZEPHIR_GLOBAL(global_false)))  TSRMLS_CC);
	RETURN_MM();

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
PHP_METHOD(Util_Decode, csv) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_2 = NULL, *_4 = NULL, *_6 = NULL, *_7 = NULL, *_9 = NULL, *_10 = NULL;
	zend_bool assoc;
	zval *csv_param = NULL, *assoc_param = NULL, *headers = NULL, *data, *handle = NULL, *hasHeaders = NULL, *numHeaders, *line = NULL, _0, *_1 = NULL, _3 = zval_used_for_init, _5, *_8 = NULL, *_11 = NULL, *_12 = NULL;
	zval *csv = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 2, &csv_param, &assoc_param, &headers);

	if (unlikely(Z_TYPE_P(csv_param) != IS_STRING && Z_TYPE_P(csv_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'csv' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(csv_param) == IS_STRING)) {
		zephir_get_strval(csv, csv_param);
	} else {
		ZEPHIR_INIT_VAR(csv);
		ZVAL_EMPTY_STRING(csv);
	}
	if (!assoc_param) {
		assoc = 0;
	} else {
		assoc = zephir_get_boolval(assoc_param);
	}
	if (!headers) {
		ZEPHIR_CPY_WRT(headers, ZEPHIR_GLOBAL(global_true));
	} else {
		ZEPHIR_SEPARATE_PARAM(headers);
	}


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_STRING(&_0, "", 0);
	if (unlikely(ZEPHIR_IS_IDENTICAL(&_0, csv))) {
		RETURN_MM_NULL();
	}
	ZEPHIR_INIT_VAR(data);
	array_init(data);
	if (zephir_is_true(headers)) {
		ZEPHIR_INIT_VAR(hasHeaders);
		ZVAL_BOOL(hasHeaders, 1);
	} else {
		ZEPHIR_INIT_NVAR(hasHeaders);
		ZVAL_BOOL(hasHeaders, 0);
	}
	ZEPHIR_CALL_FUNCTION(&_1, "is_file", &_2, csv);
	zephir_check_call_status();
	if (zephir_is_true(_1)) {
		ZEPHIR_SINIT_VAR(_3);
		ZVAL_STRING(&_3, "rb", 0);
		ZEPHIR_CALL_FUNCTION(&handle, "fopen", &_4, csv, &_3);
		zephir_check_call_status();
	} else {
		ZEPHIR_SINIT_NVAR(_3);
		ZVAL_STRING(&_3, "php://temp/maxmemory=2097152", 0);
		ZEPHIR_SINIT_VAR(_5);
		ZVAL_STRING(&_5, "wb+", 0);
		ZEPHIR_CALL_FUNCTION(&handle, "fopen", &_4, &_3, &_5);
		zephir_check_call_status();
		zephir_fwrite(NULL, handle, csv TSRMLS_CC);
		ZEPHIR_CALL_FUNCTION(NULL, "rewind", &_6, handle);
		zephir_check_call_status();
	}
	if (zephir_is_true(hasHeaders)) {
		if (Z_TYPE_P(headers) != IS_ARRAY) {
			ZEPHIR_CALL_FUNCTION(&headers, "fgetcsv", &_7, handle);
			zephir_check_call_status();
		}
		ZEPHIR_INIT_VAR(numHeaders);
		ZVAL_LONG(numHeaders, zephir_fast_count_int(headers TSRMLS_CC));
	}
	while (1) {
		if (!(!(zephir_feof(handle TSRMLS_CC)))) {
			break;
		}
		ZEPHIR_CALL_FUNCTION(&line, "fgetcsv", &_7, handle);
		zephir_check_call_status();
		if (zephir_is_true(hasHeaders)) {
			ZEPHIR_SINIT_NVAR(_3);
			ZVAL_STRING(&_3, "", 0);
			ZEPHIR_CALL_FUNCTION(&_8, "array_pad", &_9, line, numHeaders, &_3);
			zephir_check_call_status();
			ZEPHIR_CALL_FUNCTION(&line, "array_combine", &_10, headers, _8);
			zephir_check_call_status();
		}
		ZEPHIR_INIT_LNVAR(_11);
		if (assoc) {
			ZEPHIR_CPY_WRT(_11, line);
		} else {
			zephir_convert_to_object(line);
			ZEPHIR_CPY_WRT(_11, line);
		}
		zephir_array_append(&data, _11, PH_SEPARATE, "util/decode.zep", 101);
	}
	zephir_fclose(handle TSRMLS_CC);
	ZEPHIR_INIT_VAR(_12);
	if (assoc) {
		ZEPHIR_CPY_WRT(_12, data);
	} else {
		zephir_convert_to_object(data);
		ZEPHIR_CPY_WRT(_12, data);
	}
	RETURN_CCTOR(_12);

}

