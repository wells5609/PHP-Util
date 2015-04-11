
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
#include "kernel/fcall.h"
#include "kernel/operators.h"
#include "kernel/object.h"
#include "kernel/exception.h"
#include "kernel/hash.h"
#include "kernel/concat.h"
#include "kernel/array.h"


ZEPHIR_INIT_CLASS(Util_Xml) {

	ZEPHIR_REGISTER_CLASS(Util, Xml, util, xml, util_xml_method_entry, 0);

	return SUCCESS;

}

/**
 * Returns an XML document as a string.
 *
 * @param array $data Document data.
 * @param string $root_tag Root document element. Default "XML"
 * @param string $version XML version. Default "1.0"
 * @param string $encoding XML encoding. Default "UTF-8"
 * @return string XML document as string
 */
PHP_METHOD(Util_Xml, writeDocument) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *root_tag = NULL, *version = NULL, *encoding = NULL;
	zval *data_param = NULL, *root_tag_param = NULL, *version_param = NULL, *encoding_param = NULL, *writer, *_0;
	zval *data = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 3, &data_param, &root_tag_param, &version_param, &encoding_param);

	zephir_get_arrval(data, data_param);
	if (!root_tag_param) {
		ZEPHIR_INIT_VAR(root_tag);
		ZVAL_STRING(root_tag, "XML", 1);
	} else {
		zephir_get_strval(root_tag, root_tag_param);
	}
	if (!version_param) {
		ZEPHIR_INIT_VAR(version);
		ZVAL_STRING(version, "1.0", 1);
	} else {
		zephir_get_strval(version, version_param);
	}
	if (!encoding_param) {
		ZEPHIR_INIT_VAR(encoding);
		ZVAL_STRING(encoding, "UTF-8", 1);
	} else {
		zephir_get_strval(encoding, encoding_param);
	}


	ZEPHIR_INIT_VAR(writer);
	object_init_ex(writer, zephir_get_internal_ce(SS("xmlwriter") TSRMLS_CC));
	if (zephir_has_constructor(writer TSRMLS_CC)) {
		ZEPHIR_CALL_METHOD(NULL, writer, "__construct", NULL);
		zephir_check_call_status();
	}
	ZEPHIR_CALL_METHOD(NULL, writer, "openmemory", NULL);
	zephir_check_call_status();
	ZEPHIR_CALL_METHOD(NULL, writer, "startdocument", NULL, version, encoding);
	zephir_check_call_status();
	ZEPHIR_CALL_METHOD(NULL, writer, "startelement", NULL, root_tag);
	zephir_check_call_status();
	ZEPHIR_CALL_SELF(NULL, "writeelement", NULL, writer, data);
	zephir_check_call_status();
	ZEPHIR_CALL_METHOD(NULL, writer, "endelement", NULL);
	zephir_check_call_status();
	ZEPHIR_CALL_METHOD(NULL, writer, "enddocument", NULL);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(_0);
	ZVAL_BOOL(_0, 1);
	ZEPHIR_RETURN_CALL_METHOD(writer, "outputmemory", NULL, _0);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Writes an element.
 *
 * @param \XMLWriter $writer An XMLWriter instance.
 * @param array $data Data to write to the document.
 * @return void
 */
PHP_METHOD(Util_Xml, writeElement) {

	zend_bool _11;
	zephir_nts_static zephir_fcall_cache_entry *_7 = NULL, *_9 = NULL, *_14 = NULL, *_21 = NULL, *_24 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zephir_fcall_cache_entry *_4 = NULL, *_6 = NULL, *_10 = NULL, *_18 = NULL, *_19 = NULL, *_20 = NULL, *_25 = NULL;
	HashTable *_1, *_16;
	HashPosition _0, _15;
	zval *data = NULL;
	zval *writer, *data_param = NULL, *key = NULL, *value = NULL, **_2, *_3 = NULL, *_5 = NULL, *_8, *_12 = NULL, *k = NULL, *v = NULL, *_13 = NULL, **_17, _22 = zval_used_for_init, *_23 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 0, &writer, &data_param);

	zephir_get_arrval(data, data_param);


	if (!(zephir_instance_of_ev(writer, zephir_get_internal_ce(SS("xmlwriter") TSRMLS_CC) TSRMLS_CC))) {
		ZEPHIR_THROW_EXCEPTION_DEBUG_STR(spl_ce_InvalidArgumentException, "Parameter 'writer' must be an instance of 'XMLWriter'", "", 0);
		return;
	}
	zephir_is_iterable(data, &_1, &_0, 0, 0, "util/xml.zep", 86);
	for (
	  ; zephir_hash_get_current_data_ex(_1, (void**) &_2, &_0) == SUCCESS
	  ; zephir_hash_move_forward_ex(_1, &_0)
	) {
		ZEPHIR_GET_HMKEY(key, _1, _0);
		ZEPHIR_GET_HVALUE(value, _2);
		ZEPHIR_CALL_CE_STATIC(&_3, util_sanitize_ce, "alnum", &_4, key);
		zephir_check_call_status();
		ZEPHIR_CPY_WRT(key, _3);
		if (zephir_is_numeric(key)) {
			ZEPHIR_INIT_LNVAR(_5);
			ZEPHIR_CONCAT_SV(_5, "Item_", key);
			ZEPHIR_CPY_WRT(key, _5);
		}
		if (Z_TYPE_P(value) == IS_OBJECT) {
			ZEPHIR_CALL_CE_STATIC(&_3, util_typecast_ce, "toarray", &_6, value);
			zephir_check_call_status();
			ZEPHIR_CPY_WRT(value, _3);
		}
		ZEPHIR_CALL_FUNCTION(&_3, "is_scalar", &_7, value);
		zephir_check_call_status();
		if (Z_TYPE_P(value) == IS_ARRAY) {
			if (zephir_array_isset_string(value, SS("@tag"))) {
				zephir_array_fetch_string(&_8, value, SL("@tag"), PH_NOISY | PH_READONLY, "util/xml.zep", 61 TSRMLS_CC);
				ZEPHIR_CALL_FUNCTION(&key, "strval", &_9, _8);
				zephir_check_call_status();
				zephir_array_unset_string(&value, SS("@tag"), PH_SEPARATE);
			}
			ZEPHIR_CALL_METHOD(NULL, writer, "startelement", &_10, key);
			zephir_check_call_status();
			_11 = zephir_array_isset_string(value, SS("@attributes"));
			if (_11) {
				ZEPHIR_OBS_NVAR(_12);
				zephir_array_fetch_string(&_12, value, SL("@attributes"), PH_NOISY, "util/xml.zep", 67 TSRMLS_CC);
				_11 = Z_TYPE_P(_12) == IS_ARRAY;
			}
			if (_11) {
				zephir_array_fetch_string(&_8, value, SL("@attributes"), PH_NOISY | PH_READONLY, "util/xml.zep", 71 TSRMLS_CC);
				ZEPHIR_CALL_FUNCTION(&_13, "array_unique", &_14, _8);
				zephir_check_call_status();
				zephir_is_iterable(_13, &_16, &_15, 0, 0, "util/xml.zep", 75);
				for (
				  ; zephir_hash_get_current_data_ex(_16, (void**) &_17, &_15) == SUCCESS
				  ; zephir_hash_move_forward_ex(_16, &_15)
				) {
					ZEPHIR_GET_HMKEY(k, _16, _15);
					ZEPHIR_GET_HVALUE(v, _17);
					ZEPHIR_CALL_METHOD(NULL, writer, "writeattribute", &_18, k, v);
					zephir_check_call_status();
				}
				zephir_array_unset_string(&value, SS("@attributes"), PH_SEPARATE);
			}
			ZEPHIR_CALL_SELF(NULL, "writeelement", &_19, writer, value);
			zephir_check_call_status();
			ZEPHIR_CALL_METHOD(NULL, writer, "endelement", &_20);
			zephir_check_call_status();
		} else if (zephir_is_true(_3)) {
			ZEPHIR_CALL_FUNCTION(&_13, "html_entity_decode", &_21, value);
			zephir_check_call_status();
			ZEPHIR_SINIT_NVAR(_22);
			ZVAL_LONG(&_22, (16 | 128));
			ZEPHIR_CALL_FUNCTION(&_23, "htmlentities", &_24, _13, &_22);
			zephir_check_call_status();
			ZEPHIR_CALL_METHOD(NULL, writer, "writeelement", &_25, key, _23);
			zephir_check_call_status();
		}
	}
	ZEPHIR_MM_RESTORE();

}

