
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
#include "kernel/string.h"
#include "kernel/operators.h"
#include "kernel/memory.h"
#include "kernel/fcall.h"
#include "kernel/object.h"
#include "kernel/array.h"
#include "ext/spl/spl_exceptions.h"
#include "kernel/exception.h"
#include "kernel/hash.h"
#include "kernel/concat.h"


ZEPHIR_INIT_CLASS(Util_Str) {

	ZEPHIR_REGISTER_CLASS(Util, Str, util, str, util_str_method_entry, 0);

	/**
	 * Last error
	 * @var string
	 */
	zend_declare_property_null(util_str_ce, SL("lastError"), ZEND_ACC_PUBLIC|ZEND_ACC_STATIC TSRMLS_CC);

	return SUCCESS;

}

/**
 * Returns true if $haystack starts with $needle.
 *
 * @param string $haystack String to search within.
 * @param string $needle String to find.
 * @param boolean $case_sensitive Whether to check case-sensitive. Default true.
 * @return boolean
 */
PHP_METHOD(Util_Str, startsWith) {

	zend_bool case_sensitive;
	zval *haystack_param = NULL, *needle_param = NULL, *case_sensitive_param = NULL;
	zval *haystack = NULL, *needle = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 1, &haystack_param, &needle_param, &case_sensitive_param);

	zephir_get_strval(haystack, haystack_param);
	zephir_get_strval(needle, needle_param);
	if (!case_sensitive_param) {
		case_sensitive = 1;
	} else {
		case_sensitive = zephir_get_boolval(case_sensitive_param);
	}


	RETURN_MM_BOOL(zephir_start_with(haystack, needle, (case_sensitive ? ZEPHIR_GLOBAL(global_true) : ZEPHIR_GLOBAL(global_false))));

}

/**
 * Returns true if $haystack ends with $needle.
 *
 * @param string $haystack String to search within.
 * @param string $needle String to find.
 * @param boolean $case_sensitive Whether to check case-sensitive. Default true.
 * @return boolean
 */
PHP_METHOD(Util_Str, endsWith) {

	zend_bool case_sensitive;
	zval *haystack_param = NULL, *needle_param = NULL, *case_sensitive_param = NULL;
	zval *haystack = NULL, *needle = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 1, &haystack_param, &needle_param, &case_sensitive_param);

	zephir_get_strval(haystack, haystack_param);
	zephir_get_strval(needle, needle_param);
	if (!case_sensitive_param) {
		case_sensitive = 1;
	} else {
		case_sensitive = zephir_get_boolval(case_sensitive_param);
	}


	RETURN_MM_BOOL(zephir_end_with(haystack, needle, (case_sensitive ? ZEPHIR_GLOBAL(global_true) : ZEPHIR_GLOBAL(global_false))));

}

/**
 * Converts a string to a PEAR-like class name. (e.g. "Http_Request2_Response")
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Str, pearCase) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_2 = NULL, *_5 = NULL;
	zval *str_param = NULL, *pearStr = NULL, *_0 = NULL, *_1 = NULL, *_3 = NULL, *_4 = NULL, _6, _7, *_8;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_INIT_VAR(_0);
	ZVAL_STRING(_0, "/[A-Z]/", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_1);
	ZVAL_STRING(_1, " $0", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&pearStr, "preg_replace", &_2, _0, _1, str);
	zephir_check_temp_parameter(_0);
	zephir_check_temp_parameter(_1);
	zephir_check_call_status();
	ZEPHIR_INIT_NVAR(_0);
	zephir_fast_trim(_0, pearStr, NULL , ZEPHIR_TRIM_BOTH TSRMLS_CC);
	ZEPHIR_INIT_NVAR(_1);
	ZVAL_STRING(_1, "/[^a-zA-Z0-9]/", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_3);
	ZVAL_STRING(_3, " ", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&pearStr, "preg_replace", &_2, _1, _3, _0);
	zephir_check_temp_parameter(_1);
	zephir_check_temp_parameter(_3);
	zephir_check_call_status();
	ZEPHIR_INIT_NVAR(_1);
	ZEPHIR_CALL_FUNCTION(&_4, "ucwords", &_5, pearStr);
	zephir_check_call_status();
	ZEPHIR_SINIT_VAR(_6);
	ZVAL_STRING(&_6, " ", 0);
	ZEPHIR_SINIT_VAR(_7);
	ZVAL_STRING(&_7, "_", 0);
	zephir_fast_str_replace(_1, &_6, &_7, _4 TSRMLS_CC);
	ZEPHIR_INIT_NVAR(_3);
	ZVAL_STRING(_3, "/[_]{2,}/", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_8);
	ZVAL_STRING(_8, "_", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&pearStr, "preg_replace", &_2, _3, _8, _1);
	zephir_check_temp_parameter(_3);
	zephir_check_temp_parameter(_8);
	zephir_check_call_status();
	RETURN_CCTOR(pearStr);

}

/**
 * Converts a string to "snake_case"
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Str, snakeCase) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *_0 = NULL;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_CALL_SELF(&_0, "pearcase", NULL, str);
	zephir_check_call_status();
	zephir_fast_strtolower(return_value, _0);
	RETURN_MM();

}

/**
 * Converts a string to "StudlyCaps"
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Str, studlyCase) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_4 = NULL, *_6 = NULL;
	zval *str_param = NULL, *_0, *_1, *_2, *_3 = NULL, *_5 = NULL, _7, _8;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_INIT_VAR(_0);
	ZEPHIR_INIT_VAR(_1);
	ZVAL_STRING(_1, "/[^a-zA-Z0-9]/", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_2);
	ZVAL_STRING(_2, " ", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&_3, "preg_replace", &_4, _1, _2, str);
	zephir_check_temp_parameter(_1);
	zephir_check_temp_parameter(_2);
	zephir_check_call_status();
	zephir_fast_trim(_0, _3, NULL , ZEPHIR_TRIM_BOTH TSRMLS_CC);
	ZEPHIR_CALL_FUNCTION(&_5, "ucwords", &_6, _0);
	zephir_check_call_status();
	ZEPHIR_SINIT_VAR(_7);
	ZVAL_STRING(&_7, " ", 0);
	ZEPHIR_SINIT_VAR(_8);
	ZVAL_STRING(&_8, "", 0);
	zephir_fast_str_replace(return_value, &_7, &_8, _5 TSRMLS_CC);
	RETURN_MM();

}

/**
 * Converts a string to "camelCase"
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Str, camelCase) {

	zephir_nts_static zephir_fcall_cache_entry *_1 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *_0 = NULL;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_CALL_SELF(&_0, "studlycase", NULL, str);
	zephir_check_call_status();
	ZEPHIR_RETURN_CALL_FUNCTION("lcfirst", &_1, _0);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Returns whether the given variable is a valid JSON string.
 *
 * @param string
 * @return boolean
 */
PHP_METHOD(Util_Str, isJson) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, _0, *_1, *_2 = NULL;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_STRING(&_0, "", 0);
	if (likely(!ZEPHIR_IS_IDENTICAL(&_0, str))) {
		ZEPHIR_INIT_VAR(_1);
		zephir_json_decode(_1, &(_1), str, 0  TSRMLS_CC);
		ZEPHIR_CALL_FUNCTION(&_2, "json_last_error", NULL);
		zephir_check_call_status();
		if (ZEPHIR_IS_LONG_IDENTICAL(_2, 0)) {
			RETURN_MM_BOOL(1);
		}
	}
	RETURN_MM_BOOL(0);

}

/**
 * Checks whether the given value is a valid XML string.
 *
 * @param string $arg Value to check if XML.
 * @return boolean TRUE if value is a valid XML string, otherwise false.
 */
PHP_METHOD(Util_Str, isXml) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_fcall_cache_entry *_0 = NULL;
	zend_bool boolVal, _2;
	zval *str_param = NULL, *xmlErrors = NULL, *_1 = NULL, *_3 = NULL;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	if (!(zephir_start_with_str(str, SL("<?xml ")))) {
		RETURN_MM_BOOL(0);
	}
	ZEPHIR_CALL_FUNCTION(&xmlErrors, "libxml_use_internal_errors", &_0, ZEPHIR_GLOBAL(global_true));
	zephir_check_call_status();
	ZEPHIR_CALL_FUNCTION(&_1, "simplexml_load_string", NULL, str);
	zephir_check_call_status();
	_2 = zephir_instance_of_ev(_1, zephir_get_internal_ce(SS("simplexmlelement") TSRMLS_CC) TSRMLS_CC);
	if (_2) {
		ZEPHIR_CALL_FUNCTION(&_3, "libxml_get_last_error", NULL);
		zephir_check_call_status();
		_2 = ZEPHIR_IS_BOOL(_3, 0);
	}
	boolVal = _2;
	ZEPHIR_CALL_FUNCTION(NULL, "libxml_use_internal_errors", &_0, xmlErrors);
	zephir_check_call_status();
	RETURN_MM_BOOL(boolVal);

}

/**
 * Checks whether the given value is a valid serialized string.
 *
 * @param string $data Value to check if serialized
 * @return boolean TRUE If value is a valid serialized string, otherwise false.
 */
PHP_METHOD(Util_Str, isSerialized) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_3 = NULL, *_4 = NULL, *_5 = NULL;
	zval *str_param = NULL, _0, _1, _2, *_6;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_STRING(&_0, "", 0);
	if (unlikely(ZEPHIR_IS_IDENTICAL(&_0, str))) {
		RETURN_MM_BOOL(0);
	}
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_STRING(&_1, "N;", 0);
	if (ZEPHIR_IS_IDENTICAL(&_1, str)) {
		RETURN_MM_BOOL(1);
	}
	zephir_update_static_property_ce(util_str_ce, SL("lastError"), &(ZEPHIR_GLOBAL(global_null)) TSRMLS_CC);
	ZEPHIR_SINIT_VAR(_2);
	ZVAL_STRING(&_2, "Utils\\Str::handleError", 0);
	ZEPHIR_CALL_FUNCTION(NULL, "set_error_handler", &_3, &_2);
	zephir_check_call_status();
	ZEPHIR_CALL_FUNCTION(NULL, "unserialize", &_4, str);
	zephir_check_call_status();
	ZEPHIR_CALL_FUNCTION(NULL, "restore_error_handler", &_5);
	zephir_check_call_status();
	ZEPHIR_OBS_VAR(_6);
	zephir_read_static_property_ce(&_6, util_str_ce, SL("lastError") TSRMLS_CC);
	if (Z_TYPE_P(_6) == IS_NULL) {
		RETURN_MM_BOOL(1);
	}
	RETURN_MM_BOOL(0);

}

/**
 * Formats a string by injecting non-numeric characters into
 * the string in the positions they appear in the template.
 *
 * @param string $string The string to format
 * @param string $template String format to apply
 * @return string Formatted string.
 */
PHP_METHOD(Util_Str, format) {

	zephir_fcall_cache_entry *_4 = NULL;
	zephir_nts_static zephir_fcall_cache_entry *_2 = NULL;
	int fpos, spos, ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *template_param = NULL, *result, *chr = NULL, _0 = zval_used_for_init, _1 = zval_used_for_init, *_3 = NULL, *_5 = NULL;
	zval *str = NULL, *template = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 0, &str_param, &template_param);

	zephir_get_strval(str, str_param);
	zephir_get_strval(template, template_param);


	ZEPHIR_INIT_VAR(result);
	ZVAL_STRING(result, "", 1);
	fpos = 0;
	spos = 0;
	while (1) {
		if (!(((zephir_fast_strlen_ev(template) - 1)) >= fpos)) {
			break;
		}
		ZEPHIR_SINIT_NVAR(_0);
		ZVAL_LONG(&_0, fpos);
		ZEPHIR_SINIT_NVAR(_1);
		ZVAL_LONG(&_1, 1);
		ZEPHIR_CALL_FUNCTION(&chr, "substr", &_2, template, &_0, &_1);
		zephir_check_call_status();
		ZEPHIR_CALL_FUNCTION(&_3, "ctype_alnum", &_4, chr);
		zephir_check_call_status();
		if (!(zephir_is_true(_3))) {
			zephir_concat_self(&result, chr TSRMLS_CC);
		} else {
			ZEPHIR_SINIT_NVAR(_0);
			ZVAL_LONG(&_0, spos);
			ZEPHIR_SINIT_NVAR(_1);
			ZVAL_LONG(&_1, 1);
			ZEPHIR_CALL_FUNCTION(&_5, "substr", &_2, str, &_0, &_1);
			zephir_check_call_status();
			zephir_concat_self(&result, _5 TSRMLS_CC);
			spos++;
		}
		fpos++;
	}
	RETURN_CCTOR(result);

}

/**
 * Returns 1st occurance of text between two strings.
 *
 * The "between" strings are not included in output.
 *
 * @param string $string The string in which to search.
 * @param string $substr_start The starting string.
 * @param string $substr_end The ending string.
 * @return string Text between $start and $end.
 */
PHP_METHOD(Util_Str, between) {

	zval *str_param = NULL, *substr_start_param = NULL, *substr_end_param = NULL, *str1, *str2, *_0, *_1;
	zval *str = NULL, *substr_start = NULL, *substr_end = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 3, 0, &str_param, &substr_start_param, &substr_end_param);

	if (unlikely(Z_TYPE_P(str_param) != IS_STRING && Z_TYPE_P(str_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'str' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(str_param) == IS_STRING)) {
		zephir_get_strval(str, str_param);
	} else {
		ZEPHIR_INIT_VAR(str);
		ZVAL_EMPTY_STRING(str);
	}
	if (unlikely(Z_TYPE_P(substr_start_param) != IS_STRING && Z_TYPE_P(substr_start_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'substr_start' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(substr_start_param) == IS_STRING)) {
		zephir_get_strval(substr_start, substr_start_param);
	} else {
		ZEPHIR_INIT_VAR(substr_start);
		ZVAL_EMPTY_STRING(substr_start);
	}
	if (unlikely(Z_TYPE_P(substr_end_param) != IS_STRING && Z_TYPE_P(substr_end_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'substr_end' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(substr_end_param) == IS_STRING)) {
		zephir_get_strval(substr_end, substr_end_param);
	} else {
		ZEPHIR_INIT_VAR(substr_end);
		ZVAL_EMPTY_STRING(substr_end);
	}


	ZEPHIR_INIT_VAR(str1);
	zephir_fast_explode(str1, substr_start, str, LONG_MAX TSRMLS_CC);
	if (!(zephir_array_isset_long(str1, 1))) {
		RETURN_MM_NULL();
	}
	ZEPHIR_INIT_VAR(str2);
	zephir_array_fetch_long(&_0, str1, 1, PH_NOISY | PH_READONLY, "util/str.zep", 211 TSRMLS_CC);
	zephir_fast_explode(str2, substr_end, _0, LONG_MAX TSRMLS_CC);
	zephir_array_fetch_long(&_1, str2, 0, PH_NOISY | PH_READONLY, "util/str.zep", 213 TSRMLS_CC);
	zephir_fast_trim(return_value, _1, NULL , ZEPHIR_TRIM_BOTH TSRMLS_CC);
	RETURN_MM();

}

/**
 * Get a given number of sentences from a string.
 *
 * @param string $text The full string of sentences.
 * @param int $num Number of sentences to return. Default 1.
 * @param boolean|array $strip_abbr Whether to strip abbreviations (they break the function).
 * Pass an array to account for those abbreviations as well.
 * @return string Given number of sentences.
 */
PHP_METHOD(Util_Str, sentences) {

	HashTable *_3;
	HashPosition _2;
	zephir_nts_static zephir_fcall_cache_entry *_1 = NULL, *_9 = NULL, *_12 = NULL;
	int num, ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *num_param = NULL, *strip_abbr = NULL, *replacements, *strip = NULL, *replace_keys = NULL, *replace_vals = NULL, *matches, *_0 = NULL, **_4, *_5 = NULL, _6 = zval_used_for_init, _7 = zval_used_for_init, *_8 = NULL, *_11 = NULL, *_13, *_14;
	zval *str = NULL, *_10;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 2, &str_param, &num_param, &strip_abbr);

	zephir_get_strval(str, str_param);
	if (!num_param) {
		num = 1;
	} else {
		num = zephir_get_intval(num_param);
	}
	if (!strip_abbr) {
		strip_abbr = ZEPHIR_GLOBAL(global_false);
	}


	ZEPHIR_CALL_FUNCTION(&_0, "strip_tags", &_1, str);
	zephir_check_call_status();
	zephir_get_strval(str, _0);
	ZEPHIR_INIT_VAR(matches);
	array_init(matches);
	if (zephir_is_true(strip_abbr)) {
		ZEPHIR_INIT_VAR(replacements);
		array_init_size(replacements, 9);
		add_assoc_stringl_ex(replacements, SS("Dr."), SL("<Dr>"), 1);
		add_assoc_stringl_ex(replacements, SS("Mrs."), SL("<Mrs>"), 1);
		add_assoc_stringl_ex(replacements, SS("Mr."), SL("<Mr>"), 1);
		add_assoc_stringl_ex(replacements, SS("Ms."), SL("<Ms>"), 1);
		add_assoc_stringl_ex(replacements, SS("Co."), SL("<Co>"), 1);
		add_assoc_stringl_ex(replacements, SS("Ltd."), SL("<Ltd>"), 1);
		add_assoc_stringl_ex(replacements, SS("Inc."), SL("<Inc>"), 1);
		if (Z_TYPE_P(strip_abbr) == IS_ARRAY) {
			zephir_is_iterable(strip_abbr, &_3, &_2, 0, 0, "util/str.zep", 252);
			for (
			  ; zephir_hash_get_current_data_ex(_3, (void**) &_4, &_2) == SUCCESS
			  ; zephir_hash_move_forward_ex(_3, &_2)
			) {
				ZEPHIR_GET_HVALUE(strip, _4);
				ZEPHIR_INIT_NVAR(_5);
				ZEPHIR_SINIT_NVAR(_6);
				ZVAL_STRING(&_6, ".", 0);
				ZEPHIR_SINIT_NVAR(_7);
				ZVAL_STRING(&_7, "", 0);
				zephir_fast_str_replace(_5, &_6, &_7, strip TSRMLS_CC);
				ZEPHIR_INIT_LNVAR(_8);
				ZEPHIR_CONCAT_SVS(_8, "<", _5, ">");
				zephir_array_update_zval(&replacements, strip, &_8, PH_COPY | PH_SEPARATE);
			}
		}
		ZEPHIR_INIT_VAR(replace_keys);
		zephir_array_keys(replace_keys, replacements TSRMLS_CC);
		ZEPHIR_CALL_FUNCTION(&replace_vals, "array_values", &_9, replacements);
		zephir_check_call_status();
		ZEPHIR_INIT_NVAR(_5);
		zephir_fast_str_replace(_5, replace_keys, replace_vals, str TSRMLS_CC);
		zephir_get_strval(str, _5);
	}
	ZEPHIR_SINIT_NVAR(_6);
	ZVAL_LONG(&_6, num);
	ZEPHIR_INIT_VAR(_10);
	ZEPHIR_CONCAT_SVS(_10, "/^([^.!?]*[\\.!?]+){0,", &_6, "}/");
	Z_SET_ISREF_P(matches);
	ZEPHIR_CALL_FUNCTION(&_11, "preg_match", &_12, _10, str, matches);
	Z_UNSET_ISREF_P(matches);
	zephir_check_call_status();
	if (!(zephir_is_true(_11))) {
		RETURN_MM_NULL();
	}
	ZEPHIR_INIT_VAR(_13);
	if (zephir_is_true(strip_abbr)) {
		zephir_array_fetch_long(&_14, matches, 0, PH_NOISY | PH_READONLY, "util/str.zep", 267 TSRMLS_CC);
		zephir_fast_str_replace(_13, replace_vals, replace_keys, _14 TSRMLS_CC);
	} else {
		zephir_array_fetch_long(&_13, matches, 0, PH_NOISY, "util/str.zep", 267 TSRMLS_CC);
	}
	RETURN_CCTOR(_13);

}

/**
 * Class error handler
 *
 * @param int errorNum
 * @param string errorMsg
 */
PHP_METHOD(Util_Str, handleError) {

	zval *errorMsg = NULL, *_1;
	zval *errorNum_param = NULL, *errorMsg_param = NULL, _0;
	int errorNum;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 0, &errorNum_param, &errorMsg_param);

	errorNum = zephir_get_intval(errorNum_param);
	zephir_get_strval(errorMsg, errorMsg_param);


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_LONG(&_0, errorNum);
	ZEPHIR_INIT_VAR(_1);
	ZEPHIR_CONCAT_VSV(_1, &_0, ": ", errorMsg);
	zephir_update_static_property_ce(util_str_ce, SL("lastError"), &_1 TSRMLS_CC);
	ZEPHIR_MM_RESTORE();

}

