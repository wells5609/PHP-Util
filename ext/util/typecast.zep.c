
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
#include "kernel/object.h"
#include "kernel/fcall.h"
#include "kernel/operators.h"
#include "kernel/memory.h"
#include "kernel/hash.h"
#include "kernel/array.h"
#include "kernel/string.h"
#include "kernel/exception.h"
#include "kernel/concat.h"


/**
 * Typecast provides utilities to cast PHP variables to other types.
 */
ZEPHIR_INIT_CLASS(Util_Typecast) {

	ZEPHIR_REGISTER_CLASS(Util, Typecast, util, typecast, util_typecast_method_entry, 0);

	/**
	 * The decimal point for the current locale
	 * @var string
	 */
	zend_declare_property_null(util_typecast_ce, SL("decimalPoint"), ZEND_ACC_PROTECTED|ZEND_ACC_STATIC TSRMLS_CC);

	zend_declare_class_constant_long(util_typecast_ce, SL("CAST_NUMERIC"), 0 TSRMLS_CC);

	zend_declare_class_constant_long(util_typecast_ce, SL("FORCE_STRING"), 1 TSRMLS_CC);

	zend_declare_class_constant_long(util_typecast_ce, SL("IGNORE_ERROR"), 5 TSRMLS_CC);

	return SUCCESS;

}

/**
 * Converts a value to an array
 *
 * @param mixed
 * @return array
 */
PHP_METHOD(Util_Typecast, toArray) {

	zval *_1 = NULL;
	zephir_nts_static zephir_fcall_cache_entry *_0 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *arg;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &arg);



	if (Z_TYPE_P(arg) == IS_ARRAY) {
		RETVAL_ZVAL(arg, 1, 0);
		RETURN_MM();
	}
	if (Z_TYPE_P(arg) == IS_OBJECT) {
		if ((zephir_method_exists_ex(arg, SS("toarray") TSRMLS_CC) == SUCCESS)) {
			ZEPHIR_RETURN_CALL_METHOD(arg, "toarray", NULL);
			zephir_check_call_status();
			RETURN_MM();
		}
		if (zephir_is_instance_of(arg, SL("Traversable") TSRMLS_CC)) {
			ZEPHIR_RETURN_CALL_FUNCTION("iterator_to_array", NULL, arg);
			zephir_check_call_status();
			RETURN_MM();
		}
		ZEPHIR_RETURN_CALL_FUNCTION("get_object_vars", &_0, arg);
		zephir_check_call_status();
		RETURN_MM();
	}
	zephir_get_arrval(_1, arg);
	RETURN_CTOR(_1);

}

/**
 * Converts a value to an object
 *
 * @param mixed
 * @return object
 */
PHP_METHOD(Util_Typecast, toObject) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *arg, *_0 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &arg);



	if (Z_TYPE_P(arg) == IS_OBJECT) {
		if (zephir_instance_of_ev(arg, zend_standard_class_def TSRMLS_CC)) {
			RETVAL_ZVAL(arg, 1, 0);
			RETURN_MM();
		}
		ZEPHIR_CALL_SELF(&_0, "toarray", NULL, arg);
		zephir_check_call_status();
		zephir_convert_to_object(_0);
		RETURN_CCTOR(_0);
	}
	zephir_convert_to_object(arg);
	RETVAL_ZVAL(arg, 1, 0);
	RETURN_MM();

}

/**
 * Converts a value to arrays recursively.
 *
 * @param mixed
 * @return array
 */
PHP_METHOD(Util_Typecast, toArrays) {

	zephir_fcall_cache_entry *_6 = NULL;
	zend_bool _4;
	HashTable *_2;
	HashPosition _1;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *arg, *arr, *key = NULL, *value = NULL, *_0 = NULL, **_3, *_5 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &arg);



	ZEPHIR_INIT_VAR(arr);
	array_init(arr);
	ZEPHIR_CALL_SELF(&_0, "toarray", NULL, arg);
	zephir_check_call_status();
	zephir_is_iterable(_0, &_2, &_1, 0, 0, "util/typecast.zep", 92);
	for (
	  ; zephir_hash_get_current_data_ex(_2, (void**) &_3, &_1) == SUCCESS
	  ; zephir_hash_move_forward_ex(_2, &_1)
	) {
		ZEPHIR_GET_HMKEY(key, _2, _1);
		ZEPHIR_GET_HVALUE(value, _3);
		_4 = Z_TYPE_P(value) == IS_ARRAY;
		if (!(_4)) {
			_4 = Z_TYPE_P(value) == IS_OBJECT;
		}
		if (_4) {
			ZEPHIR_CALL_SELF(&_5, "toarrays", &_6, value);
			zephir_check_call_status();
			zephir_array_update_zval(&arr, key, &_5, PH_COPY | PH_SEPARATE);
		} else {
			zephir_array_update_zval(&arr, key, &value, PH_COPY | PH_SEPARATE);
		}
	}
	RETURN_CCTOR(arr);

}

/**
 * Converts a value to objects recursively.
 *
 * Objects are converted to instances of stdClass
 *
 * @param mixed
 * @return object
 */
PHP_METHOD(Util_Typecast, toObjects) {

	zephir_fcall_cache_entry *_6 = NULL;
	zend_bool _4;
	HashTable *_2;
	HashPosition _1;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *arg, *arr, *key = NULL, *value = NULL, *_0 = NULL, **_3, *_5 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &arg);



	ZEPHIR_INIT_VAR(arr);
	array_init(arr);
	ZEPHIR_CALL_SELF(&_0, "toarray", NULL, arg);
	zephir_check_call_status();
	zephir_is_iterable(_0, &_2, &_1, 0, 0, "util/typecast.zep", 118);
	for (
	  ; zephir_hash_get_current_data_ex(_2, (void**) &_3, &_1) == SUCCESS
	  ; zephir_hash_move_forward_ex(_2, &_1)
	) {
		ZEPHIR_GET_HMKEY(key, _2, _1);
		ZEPHIR_GET_HVALUE(value, _3);
		_4 = Z_TYPE_P(value) == IS_ARRAY;
		if (!(_4)) {
			_4 = Z_TYPE_P(value) == IS_OBJECT;
		}
		if (_4) {
			ZEPHIR_CALL_SELF(&_5, "toobjects", &_6, value);
			zephir_check_call_status();
			zephir_array_update_zval(&arr, key, &_5, PH_COPY | PH_SEPARATE);
		} else {
			zephir_array_update_zval(&arr, key, &value, PH_COPY | PH_SEPARATE);
		}
	}
	zephir_convert_to_object(arr);
	RETURN_CCTOR(arr);

}

/**
 * Converts a variable to a boolean value
 *
 * @param mixed arg
 * @return boolean
 */
PHP_METHOD(Util_Typecast, toBool) {

	zend_bool _2, _4, _7, _9;
	zval *arg = NULL, *_0, _1, _3, _5, _6, _8, _10;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &arg);

	ZEPHIR_SEPARATE_PARAM(arg);


	if (Z_TYPE_P(arg) == IS_BOOL) {
		RETVAL_ZVAL(arg, 1, 0);
		RETURN_MM();
	}
	if (zephir_is_numeric(arg)) {
		RETURN_MM_BOOL(ZEPHIR_GT_LONG(arg, 0));
	}
	if (Z_TYPE_P(arg) == IS_STRING) {
		ZEPHIR_INIT_VAR(_0);
		zephir_fast_strtolower(_0, arg);
		ZEPHIR_CPY_WRT(arg, _0);
		ZEPHIR_SINIT_VAR(_1);
		ZVAL_STRING(&_1, "y", 0);
		_2 = ZEPHIR_IS_IDENTICAL(&_1, arg);
		if (!(_2)) {
			ZEPHIR_SINIT_VAR(_3);
			ZVAL_STRING(&_3, "yes", 0);
			_2 = ZEPHIR_IS_IDENTICAL(&_3, arg);
		}
		_4 = _2;
		if (!(_4)) {
			ZEPHIR_SINIT_VAR(_5);
			ZVAL_STRING(&_5, "true", 0);
			_4 = ZEPHIR_IS_IDENTICAL(&_5, arg);
		}
		if (_4) {
			RETURN_MM_BOOL(1);
		}
		ZEPHIR_SINIT_VAR(_6);
		ZVAL_STRING(&_6, "n", 0);
		_7 = ZEPHIR_IS_IDENTICAL(&_6, arg);
		if (!(_7)) {
			ZEPHIR_SINIT_VAR(_8);
			ZVAL_STRING(&_8, "no", 0);
			_7 = ZEPHIR_IS_IDENTICAL(&_8, arg);
		}
		_9 = _7;
		if (!(_9)) {
			ZEPHIR_SINIT_VAR(_10);
			ZVAL_STRING(&_10, "false", 0);
			_9 = ZEPHIR_IS_IDENTICAL(&_10, arg);
		}
		if (_9) {
			RETURN_MM_BOOL(0);
		}
	}
	RETURN_MM_BOOL(zephir_get_boolval(arg));

}

/**
 * Convert value to a scalar value.
 *
 * @param string Value we"d like to be scalar.
 * @param int $flags SCALAR_* flag bitwise mask.
 * @return string
 * @throws InvalidArgumentException if value can not be scalarized.
 */
PHP_METHOD(Util_Typecast, toScalar) {

	zval *_2 = NULL;
	int flags, ZEPHIR_LAST_CALL_STATUS;
	zval *arg, *flags_param = NULL, *_0, *_1 = NULL, *_3, *_4;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &arg, &flags_param);

	if (!flags_param) {
		flags = 0;
	} else {
		flags = zephir_get_intval(flags_param);
	}


	ZEPHIR_INIT_VAR(_0);
	zephir_gettype(_0, arg TSRMLS_CC);
	do {
		if (ZEPHIR_IS_STRING(_0, "string")) {
			ZEPHIR_INIT_VAR(_1);
			if ((flags & 0)) {
				ZEPHIR_CALL_SELF(&_1, "strnum", NULL, arg);
				zephir_check_call_status();
			} else {
				ZEPHIR_CPY_WRT(_1, arg);
			}
			RETURN_CCTOR(_1);
		}
		if (ZEPHIR_IS_STRING(_0, "double") || ZEPHIR_IS_STRING(_0, "integer")) {
			if ((flags & 1)) {
				zephir_get_strval(_2, arg);
				ZEPHIR_CPY_WRT(_1, _2);
			} else {
				ZEPHIR_CPY_WRT(_1, arg);
			}
			RETURN_CCTOR(_1);
		}
		if (ZEPHIR_IS_STRING(_0, "boolean")) {
			if ((flags & 1)) {
				if (zephir_is_true(arg)) {
					ZVAL_LONG(_1, 1);
				} else {
					ZVAL_LONG(_1, 0);
				}
			} else {
				if (zephir_is_true(arg)) {
					ZVAL_LONG(_1, 1);
				} else {
					ZEPHIR_INIT_NVAR(_1);
					ZVAL_LONG(_1, 0);
				}
			}
			RETURN_CCTOR(_1);
		}
		if (ZEPHIR_IS_STRING(_0, "NULL")) {
			RETURN_MM_STRING("", 1);
		}
		if (ZEPHIR_IS_STRING(_0, "object")) {
			if ((zephir_method_exists_ex(arg, SS("__tostring") TSRMLS_CC) == SUCCESS)) {
				ZEPHIR_RETURN_CALL_METHOD(arg, "__tostring", NULL);
				zephir_check_call_status();
				RETURN_MM();
			}
		}
	} while(0);

	if ((flags & 5)) {
		RETURN_MM_STRING("", 1);
	}
	ZEPHIR_INIT_NVAR(_1);
	object_init_ex(_1, spl_ce_InvalidArgumentException);
	ZEPHIR_INIT_VAR(_3);
	zephir_gettype(_3, arg TSRMLS_CC);
	ZEPHIR_INIT_VAR(_4);
	ZEPHIR_CONCAT_SVS(_4, "Value can not be scalar, given: '", _3, "'.");
	ZEPHIR_CALL_METHOD(NULL, _1, "__construct", NULL, _4);
	zephir_check_call_status();
	zephir_throw_exception_debug(_1, "util/typecast.zep", 189 TSRMLS_CC);
	ZEPHIR_MM_RESTORE();
	return;

}

/**
 * If $val is a numeric string, converts to float or integer depending on
 * whether a decimal point is present. Otherwise returns original.
 *
 * @param string $value If numeric string, converted to integer or float.
 * @return scalar Value as string, integer, or float.
 */
PHP_METHOD(Util_Typecast, strnum) {

	int ZEPHIR_LAST_CALL_STATUS;
	zend_bool _0;
	zval *value, *_1, *_2 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &value);



	_0 = Z_TYPE_P(value) == IS_STRING;
	if (_0) {
		_0 = zephir_is_numeric(value);
	}
	if (_0) {
		ZEPHIR_INIT_VAR(_1);
		ZEPHIR_CALL_SELF(&_2, "getdecimalpoint", NULL);
		zephir_check_call_status();
		if (zephir_memnstr(value, _2, "util/typecast.zep", 202)) {
			ZVAL_DOUBLE(_1, zephir_get_doubleval(value));
		} else {
			ZVAL_LONG(_1, zephir_get_intval(value));
		}
		RETURN_CCTOR(_1);
	}
	RETVAL_ZVAL(value, 1, 0);
	RETURN_MM();

}

/**
 * Returns the decimal point for the current locale
 *
 * @return string
 */
PHP_METHOD(Util_Typecast, getDecimalPoint) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_1 = NULL;
	zval *_0, *loc = NULL, *_2 = NULL, *_3;

	ZEPHIR_MM_GROW();

	ZEPHIR_OBS_VAR(_0);
	zephir_read_static_property_ce(&_0, util_typecast_ce, SL("decimalPoint") TSRMLS_CC);
	if (Z_TYPE_P(_0) == IS_NULL) {
		ZEPHIR_CALL_FUNCTION(&loc, "localeconv", &_1);
		zephir_check_call_status();
		ZEPHIR_INIT_VAR(_2);
		if (zephir_array_isset_string(loc, SS("decimal_point"))) {
			ZEPHIR_OBS_NVAR(_2);
			zephir_array_fetch_string(&_2, loc, SL("decimal_point"), PH_NOISY, "util/typecast.zep", 220 TSRMLS_CC);
		} else {
			ZEPHIR_INIT_NVAR(_2);
			ZVAL_STRING(_2, ".", 1);
		}
		zephir_update_static_property_ce(util_typecast_ce, SL("decimalPoint"), &_2 TSRMLS_CC);
	}
	_3 = zephir_fetch_static_property_ce(util_typecast_ce, SL("decimalPoint") TSRMLS_CC);
	RETURN_CTOR(_3);

}

