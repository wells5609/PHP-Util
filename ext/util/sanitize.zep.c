
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
#include "kernel/string.h"
#include "kernel/array.h"


/**
 * Sanitize provides utilities to sanitize PHP variables.
 */
ZEPHIR_INIT_CLASS(Util_Sanitize) {

	ZEPHIR_REGISTER_CLASS(Util, Sanitize, util, sanitize, util_sanitize_method_entry, 0);

	return SUCCESS;

}

/**
 * Sanitizes a string value
 *
 * @param string str
 * @param int flags [Optional] Sanitize flags
 * @return string
 */
PHP_METHOD(Util_Sanitize, str) {

	int flags, ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *flags_param = NULL, _0, _1;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &str_param, &flags_param);

	zephir_get_strval(str, str_param);
	if (!flags_param) {
		flags = 0;
	} else {
		flags = zephir_get_intval(flags_param);
	}


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_LONG(&_0, 513);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_LONG(&_1, flags);
	ZEPHIR_RETURN_CALL_FUNCTION("filter_var", NULL, str, &_0, &_1);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Sanitizes an integer value
 *
 * @param scalar value
 * @param int flags [Optional] Sanitize flags
 * @return int
 */
PHP_METHOD(Util_Sanitize, numInt) {

	int flags, ZEPHIR_LAST_CALL_STATUS;
	zval *value, *flags_param = NULL, _0, _1;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &value, &flags_param);

	if (!flags_param) {
		flags = 0;
	} else {
		flags = zephir_get_intval(flags_param);
	}


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_LONG(&_0, 519);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_LONG(&_1, flags);
	ZEPHIR_RETURN_CALL_FUNCTION("filter_var", NULL, value, &_0, &_1);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Sanitizes a float value
 *
 * @param scalar value
 * @param int flags [Optional] Sanitize flags
 * @return float
 */
PHP_METHOD(Util_Sanitize, numFloat) {

	int flags, ZEPHIR_LAST_CALL_STATUS;
	zval *value, *flags_param = NULL, _0, _1;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &value, &flags_param);

	if (!flags_param) {
		flags = 0;
	} else {
		flags = zephir_get_intval(flags_param);
	}


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_LONG(&_0, 520);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_LONG(&_1, flags);
	ZEPHIR_RETURN_CALL_FUNCTION("filter_var", NULL, value, &_0, &_1);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Sanitizes a string to contain only ASCII characters
 *
 * @param string $str
 * @return string
 */
PHP_METHOD(Util_Sanitize, ascii) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_3 = NULL, *_7 = NULL;
	zval *str_param = NULL, _0 = zval_used_for_init, _1 = zval_used_for_init, *_2 = NULL, *_4, *_5, *_6 = NULL;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_LONG(&_0, 3);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_STRING(&_1, "UTF-8", 0);
	ZEPHIR_CALL_FUNCTION(&_2, "html_entity_decode", &_3, str, &_0, &_1);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(_4);
	ZVAL_STRING(_4, "/[\\x01-\\x08\\x0B-\\x1F]/", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_5);
	ZVAL_STRING(_5, "", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&_6, "preg_replace", &_7, _4, _5, _2);
	zephir_check_temp_parameter(_4);
	zephir_check_temp_parameter(_5);
	zephir_check_call_status();
	ZEPHIR_SINIT_NVAR(_0);
	ZVAL_LONG(&_0, 513);
	ZEPHIR_SINIT_NVAR(_1);
	ZVAL_LONG(&_1, (8 | 512));
	ZEPHIR_RETURN_CALL_FUNCTION("filter_var", NULL, _6, &_0, &_1);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Sanitizes a URL, decoding using rawurldecode() and filter_var().
 *
 * @param string url URL, possibly containing encoded characters
 * @param int flags [Optional] Optional filter_var() flags
 * @return string Sanitized URL with "%##" characters translated
 */
PHP_METHOD(Util_Sanitize, url) {

	zephir_nts_static zephir_fcall_cache_entry *_1 = NULL;
	int flags, ZEPHIR_LAST_CALL_STATUS;
	zval *url_param = NULL, *flags_param = NULL, *_0 = NULL, _2, _3;
	zval *url = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &url_param, &flags_param);

	zephir_get_strval(url, url_param);
	if (!flags_param) {
		flags = 0;
	} else {
		flags = zephir_get_intval(flags_param);
	}


	ZEPHIR_CALL_FUNCTION(&_0, "rawurldecode", &_1, url);
	zephir_check_call_status();
	ZEPHIR_SINIT_VAR(_2);
	ZVAL_LONG(&_2, 518);
	ZEPHIR_SINIT_VAR(_3);
	ZVAL_LONG(&_3, flags);
	ZEPHIR_RETURN_CALL_FUNCTION("filter_var", NULL, _0, &_2, &_3);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Strips non-alphabetic characters from a string.
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Sanitize, alpha) {

	zephir_nts_static zephir_fcall_cache_entry *_4 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *_0 = NULL, *_1 = NULL, *_2, *_3;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_INIT_VAR(_0);
	ZEPHIR_CALL_FUNCTION(&_1, "ctype_alpha", NULL, str);
	zephir_check_call_status();
	if (zephir_is_true(_1)) {
		ZEPHIR_CPY_WRT(_0, str);
	} else {
		ZEPHIR_INIT_VAR(_2);
		ZVAL_STRING(_2, "/[^a-zA-Z]/", ZEPHIR_TEMP_PARAM_COPY);
		ZEPHIR_INIT_VAR(_3);
		ZVAL_STRING(_3, "", ZEPHIR_TEMP_PARAM_COPY);
		ZEPHIR_CALL_FUNCTION(&_0, "preg_replace", &_4, _2, _3, str);
		zephir_check_temp_parameter(_2);
		zephir_check_temp_parameter(_3);
		zephir_check_call_status();
	}
	RETURN_CCTOR(_0);

}

/**
 * Strips non-alphanumeric characters from a string.
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Sanitize, alnum) {

	zephir_nts_static zephir_fcall_cache_entry *_4 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *_0 = NULL, *_1 = NULL, *_2, *_3;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_INIT_VAR(_0);
	ZEPHIR_CALL_FUNCTION(&_1, "ctype_alnum", NULL, str);
	zephir_check_call_status();
	if (zephir_is_true(_1)) {
		ZEPHIR_CPY_WRT(_0, str);
	} else {
		ZEPHIR_INIT_VAR(_2);
		ZVAL_STRING(_2, "/[^a-zA-Z0-9]/", ZEPHIR_TEMP_PARAM_COPY);
		ZEPHIR_INIT_VAR(_3);
		ZVAL_STRING(_3, "", ZEPHIR_TEMP_PARAM_COPY);
		ZEPHIR_CALL_FUNCTION(&_0, "preg_replace", &_4, _2, _3, str);
		zephir_check_temp_parameter(_2);
		zephir_check_temp_parameter(_3);
		zephir_check_call_status();
	}
	RETURN_CCTOR(_0);

}

/**
 * Strips invalid unicode from a string.
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Sanitize, unicode) {

	zephir_nts_static zephir_fcall_cache_entry *_3 = NULL, *_7 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *encoding = NULL, _0, *subChr = NULL, _1 = zval_used_for_init, _2 = zval_used_for_init, *_4 = NULL, *_5, *_6;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	if ((zephir_function_exists_ex(SS("mb_convert_encoding") TSRMLS_CC) == SUCCESS)) {
		ZEPHIR_CALL_FUNCTION(&encoding, "mb_detect_encoding", NULL, str);
		zephir_check_call_status();
		ZEPHIR_SINIT_VAR(_0);
		ZVAL_STRING(&_0, "ASCII", 0);
		if (!ZEPHIR_IS_IDENTICAL(&_0, encoding)) {
			ZEPHIR_SINIT_VAR(_1);
			ZVAL_STRING(&_1, "mbstring.substitute_character", 0);
			ZEPHIR_SINIT_VAR(_2);
			ZVAL_STRING(&_2, "none", 0);
			ZEPHIR_CALL_FUNCTION(&subChr, "ini_set", &_3, &_1, &_2);
			zephir_check_call_status();
			ZEPHIR_SINIT_NVAR(_1);
			ZVAL_STRING(&_1, "UTF-8", 0);
			ZEPHIR_SINIT_NVAR(_2);
			ZVAL_STRING(&_2, "UTF-8", 0);
			ZEPHIR_CALL_FUNCTION(&_4, "mb_convert_encoding", NULL, str, &_1, &_2);
			zephir_check_call_status();
			zephir_get_strval(str, _4);
			ZEPHIR_SINIT_NVAR(_1);
			ZVAL_STRING(&_1, "mbstring.substitute_character", 0);
			ZEPHIR_CALL_FUNCTION(NULL, "ini_set", &_3, &_1, subChr);
			zephir_check_call_status();
		}
	}
	ZEPHIR_INIT_VAR(_5);
	ZVAL_STRING(_5, "/\\\\u([0-9a-f]{4})/i", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_6);
	ZVAL_STRING(_6, "", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&_4, "preg_replace", &_7, _5, _6, str);
	zephir_check_temp_parameter(_5);
	zephir_check_temp_parameter(_6);
	zephir_check_call_status();
	zephir_stripcslashes(return_value, _4 TSRMLS_CC);
	RETURN_MM();

}

/**
 * Sanitizes a string to a "slug" format: lowercase alphanumeric string with given separator.
 *
 * @param string $string Dirty string.
 * @param string $separator [Optional] Character used to replace non-alphanumeric characters. Default "-".
 * @return string Slugified string.
 */
PHP_METHOD(Util_Sanitize, slug) {

	zephir_nts_static zephir_fcall_cache_entry *_3 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *str_param = NULL, *separator_param = NULL, *slug = NULL, *_0 = NULL, *_1 = NULL, *_2, *_4 = NULL;
	zval *str = NULL, *separator = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &str_param, &separator_param);

	zephir_get_strval(str, str_param);
	if (!separator_param) {
		ZEPHIR_INIT_VAR(separator);
		ZVAL_STRING(separator, "-", 1);
	} else {
		zephir_get_strval(separator, separator_param);
	}


	ZEPHIR_CALL_SELF(&_0, "ascii", NULL, str);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(_1);
	ZVAL_STRING(_1, "#[\"\\'\\’\\x01-\\x08\\x0B-\\x1F]#", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_2);
	ZVAL_STRING(_2, "", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&slug, "preg_replace", &_3, _1, _2, _0);
	zephir_check_temp_parameter(_1);
	zephir_check_temp_parameter(_2);
	zephir_check_call_status();
	ZEPHIR_INIT_NVAR(_1);
	ZVAL_STRING(_1, "#[^a-z0-9]#i", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&_4, "preg_replace", &_3, _1, separator, slug);
	zephir_check_temp_parameter(_1);
	zephir_check_call_status();
	ZEPHIR_INIT_NVAR(_1);
	ZVAL_STRING(_1, "#[/_|+ -]+#u", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_FUNCTION(&slug, "preg_replace", &_3, _1, separator, _4);
	zephir_check_temp_parameter(_1);
	zephir_check_call_status();
	ZEPHIR_INIT_NVAR(_1);
	zephir_fast_trim(_1, slug, separator, ZEPHIR_TRIM_BOTH TSRMLS_CC);
	zephir_fast_strtolower(return_value, _1);
	RETURN_MM();

}

/**
 * Removes single and double quotes from a string.
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Sanitize, stripQuotes) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_2 = NULL;
	zval *str_param = NULL, *_0, *_1;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_INIT_VAR(_0);
	ZVAL_STRING(_0, "/[\"\\'\\’]/", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_1);
	ZVAL_STRING(_1, "", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_RETURN_CALL_FUNCTION("preg_replace", &_2, _0, _1, str);
	zephir_check_temp_parameter(_0);
	zephir_check_temp_parameter(_1);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Removes non-printing ASCII control characters from a string.
 *
 * @param string
 * @return string
 */
PHP_METHOD(Util_Sanitize, stripControl) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_2 = NULL;
	zval *str_param = NULL, *_0, *_1;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_INIT_VAR(_0);
	ZVAL_STRING(_0, "/[\\x01-\\x08\\x0B-\\x1F]/", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_INIT_VAR(_1);
	ZVAL_STRING(_1, "", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_RETURN_CALL_FUNCTION("preg_replace", &_2, _0, _1, str);
	zephir_check_temp_parameter(_0);
	zephir_check_temp_parameter(_1);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Escapes text for SQL LIKE special characters % and _.
 *
 * @param string $text The text to be escaped.
 * @return string text, safe for inclusion in LIKE query.
 */
PHP_METHOD(Util_Sanitize, sqlLike) {

	zval *_0, *_2;
	zval *str_param = NULL, *_1 = NULL;
	zval *str = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &str_param);

	zephir_get_strval(str, str_param);


	ZEPHIR_INIT_VAR(_0);
	array_init_size(_0, 3);
	ZEPHIR_INIT_VAR(_1);
	ZVAL_STRING(_1, "%", 1);
	zephir_array_fast_append(_0, _1);
	ZEPHIR_INIT_NVAR(_1);
	ZVAL_STRING(_1, "_", 1);
	zephir_array_fast_append(_0, _1);
	ZEPHIR_INIT_VAR(_2);
	array_init_size(_2, 3);
	ZEPHIR_INIT_NVAR(_1);
	ZVAL_STRING(_1, "\\%", 1);
	zephir_array_fast_append(_2, _1);
	ZEPHIR_INIT_NVAR(_1);
	ZVAL_STRING(_1, "\\_", 1);
	zephir_array_fast_append(_2, _1);
	zephir_fast_str_replace(return_value, _0, _2, str TSRMLS_CC);
	RETURN_MM();

}

