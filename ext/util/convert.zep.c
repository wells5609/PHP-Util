
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
#include "kernel/exception.h"
#include "kernel/memory.h"
#include "kernel/array.h"
#include "kernel/operators.h"
#include "kernel/fcall.h"
#include "ext/spl/spl_exceptions.h"


ZEPHIR_INIT_CLASS(Util_Convert) {

	ZEPHIR_REGISTER_CLASS(Util, Convert, util, convert, util_convert_method_entry, 0);

	return SUCCESS;

}

/**
 * Convert a human-readable time unit description to seconds.
 *
 * COUNTEREXAMPLE
 *   ttl = (60 * 60 * 24 * 32.5); // 32.5 days
 * becomes:
 *   ttl = Format::seconds("32.5 days");
 *
 * @author facebook/libphutil
 * Edited by wells: always convert to seconds; allow decimals in units; use
 * explode() instead of preg_match(); add week conversions; return float.
 *
 * @param string Human readable description of a time unit quantity.
 * @return float Given unit in number of seconds.
 */
PHP_METHOD(Util_Convert, toSeconds) {

	zephir_nts_static zephir_fcall_cache_entry *_3 = NULL, *_6 = NULL, *_7 = NULL;
	int factor, ZEPHIR_LAST_CALL_STATUS;
	zval *arg_param = NULL, *parts, *qty, *unit, _0 = zval_used_for_init, _1 = zval_used_for_init, *_2 = NULL, *_4 = NULL, *_5 = NULL;
	zval *arg = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &arg_param);

	if (unlikely(Z_TYPE_P(arg_param) != IS_STRING && Z_TYPE_P(arg_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'arg' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(arg_param) == IS_STRING)) {
		zephir_get_strval(arg, arg_param);
	} else {
		ZEPHIR_INIT_VAR(arg);
		ZVAL_EMPTY_STRING(arg);
	}


	if (unlikely(!(zephir_memnstr_str(arg, SL(" "), "util/convert.zep", 26)))) {
		ZEPHIR_THROW_EXCEPTION_DEBUG_STR(spl_ce_RuntimeException, "Unable to parse unit specification (expected in the form '5 days')", "util/convert.zep", 27);
		return;
	}
	ZEPHIR_INIT_VAR(parts);
	zephir_fast_explode_str(parts, SL(" "), arg, 2  TSRMLS_CC);
	ZEPHIR_OBS_VAR(qty);
	zephir_array_fetch_long(&qty, parts, 0, PH_NOISY, "util/convert.zep", 34 TSRMLS_CC);
	ZEPHIR_OBS_VAR(unit);
	zephir_array_fetch_long(&unit, parts, 1, PH_NOISY, "util/convert.zep", 35 TSRMLS_CC);
	if (!(zephir_is_numeric(qty))) {
		ZEPHIR_THROW_EXCEPTION_DEBUG_STR(spl_ce_RuntimeException, "Unable to parse unit specification (expected numeric quantity)", "util/convert.zep", 38);
		return;
	}
	ZEPHIR_SINIT_VAR(_0);
	ZVAL_LONG(&_0, 0);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_LONG(&_1, 3);
	ZEPHIR_CALL_FUNCTION(&_2, "substr", &_3, unit, &_0, &_1);
	zephir_check_call_status();
	do {
		if (ZEPHIR_IS_STRING(_2, "sec")) {
			factor = 1;
			break;
		}
		if (ZEPHIR_IS_STRING(_2, "min")) {
			factor = 60;
			break;
		}
		if (ZEPHIR_IS_STRING(_2, "hou")) {
			factor = 3600;
			break;
		}
		if (ZEPHIR_IS_STRING(_2, "day")) {
			factor = 86400;
			break;
		}
		if (ZEPHIR_IS_STRING(_2, "wee")) {
			factor = 604800;
			break;
		}
		ZEPHIR_THROW_EXCEPTION_DEBUG_STR(spl_ce_DomainException, "Invalid unit given", "util/convert.zep", 64);
		return;
	} while(0);

	ZEPHIR_SINIT_NVAR(_0);
	ZVAL_LONG(&_0, factor);
	ZEPHIR_SINIT_NVAR(_1);
	ZVAL_LONG(&_1, 10);
	ZEPHIR_CALL_FUNCTION(&_4, "bcmul", NULL, qty, &_0, &_1);
	zephir_check_call_status();
	ZEPHIR_SINIT_NVAR(_0);
	ZVAL_LONG(&_0, 8);
	ZEPHIR_CALL_FUNCTION(&_5, "round", &_6, _4, &_0);
	zephir_check_call_status();
	ZEPHIR_RETURN_CALL_FUNCTION("strval", &_7, _5);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Convert a temperature to another unit
 *
 * @param number quantity Temperature to convert given in degrees
 * @param string from Given temperature unit: one of "C", "F", or "K"
 * @param string to Temperature unit to convert to
 * @return string Temperature in new unit
 * @throws InvalidArgumentException if quantity is not a number
 * @throws DomainException if either temperature unit is unknown
 */
PHP_METHOD(Util_Convert, temp) {

	zephir_nts_static zephir_fcall_cache_entry *_13 = NULL, *_14 = NULL;
	zephir_fcall_cache_entry *_6 = NULL, *_8 = NULL, *_9 = NULL, *_11 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zend_class_entry *_1 = NULL;
	zval *quantity_param = NULL, *from_param = NULL, *to_param = NULL, *temp = NULL, *_0 = NULL, *_2 = NULL, _3 = zval_used_for_init, _4 = zval_used_for_init, *_5 = NULL, *_7 = NULL, *_10 = NULL, _12;
	zval *quantity = NULL, *from = NULL, *to = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 3, 0, &quantity_param, &from_param, &to_param);

	zephir_get_strval(quantity, quantity_param);
	if (unlikely(Z_TYPE_P(from_param) != IS_STRING && Z_TYPE_P(from_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'from' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(from_param) == IS_STRING)) {
		zephir_get_strval(from, from_param);
	} else {
		ZEPHIR_INIT_VAR(from);
		ZVAL_EMPTY_STRING(from);
	}
	if (unlikely(Z_TYPE_P(to_param) != IS_STRING && Z_TYPE_P(to_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'to' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(to_param) == IS_STRING)) {
		zephir_get_strval(to, to_param);
	} else {
		ZEPHIR_INIT_VAR(to);
		ZVAL_EMPTY_STRING(to);
	}


	if (unlikely(!(zephir_is_numeric(quantity)))) {
		ZEPHIR_INIT_VAR(_0);
		if (!_1) {
			_1 = zend_fetch_class(SL("Util\\InvalidArgumentException"), ZEND_FETCH_CLASS_AUTO TSRMLS_CC);
		}
		object_init_ex(_0, _1);
		if (zephir_has_constructor(_0 TSRMLS_CC)) {
			ZEPHIR_INIT_VAR(_2);
			ZVAL_STRING(_2, "Quantity must be a number", ZEPHIR_TEMP_PARAM_COPY);
			ZEPHIR_CALL_METHOD(NULL, _0, "__construct", NULL, _2);
			zephir_check_temp_parameter(_2);
			zephir_check_call_status();
		}
		zephir_throw_exception_debug(_0, "util/convert.zep", 85 TSRMLS_CC);
		ZEPHIR_MM_RESTORE();
		return;
	}
	ZEPHIR_INIT_NVAR(_0);
	zephir_fast_strtoupper(_0, to);
	zephir_get_strval(to, _0);
	ZEPHIR_INIT_NVAR(_2);
	zephir_fast_strtoupper(_2, from);
	do {
		if (ZEPHIR_IS_STRING(_2, "F")) {
			do {
				if (ZEPHIR_IS_STRING(to, "C")) {
					ZEPHIR_SINIT_VAR(_3);
					ZVAL_LONG(&_3, 32);
					ZEPHIR_SINIT_VAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&_5, "bcsub", &_6, quantity, &_3, &_4);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 5);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&_7, "bcmul", &_8, _5, &_3, &_4);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 9);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&temp, "bcdiv", &_9, _7, &_3, &_4);
					zephir_check_call_status();
					break;
				}
				if (ZEPHIR_IS_STRING(to, "K")) {
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 32);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&_5, "bcsub", &_6, quantity, &_3, &_4);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 5);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&_7, "bcmul", &_8, _5, &_3, &_4);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 9);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&_10, "bcdiv", &_9, _7, &_3, &_4);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_STRING(&_3, "273.15", 0);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&temp, "bcadd", &_11, _10, &_3, &_4);
					zephir_check_call_status();
					break;
				}
				break;
			} while(0);

		}
		if (ZEPHIR_IS_STRING(_2, "C")) {
			do {
				if (ZEPHIR_IS_STRING(to, "F")) {
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 9);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&_5, "bcmul", &_8, quantity, &_3, &_4);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 5);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&_7, "bcdiv", &_9, _5, &_3, &_4);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 32);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&temp, "bcadd", &_11, _7, &_3, &_4);
					zephir_check_call_status();
					break;
				}
				if (ZEPHIR_IS_STRING(to, "K")) {
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_STRING(&_3, "273.15", 0);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&temp, "bcadd", &_11, quantity, &_3, &_4);
					zephir_check_call_status();
					break;
				}
				break;
			} while(0);

		}
		if (ZEPHIR_IS_STRING(_2, "K")) {
			do {
				if (ZEPHIR_IS_STRING(to, "C")) {
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_STRING(&_3, "273.15", 0);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&temp, "bcsub", &_6, quantity, &_3, &_4);
					zephir_check_call_status();
					break;
				}
				if (ZEPHIR_IS_STRING(to, "F")) {
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_STRING(&_3, "273.15", 0);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&_5, "bcsub", &_6, quantity, &_3, &_4);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 9);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 5);
					ZEPHIR_SINIT_VAR(_12);
					ZVAL_LONG(&_12, 10);
					ZEPHIR_CALL_FUNCTION(&_7, "bcdiv", &_9, &_3, &_4, &_12);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 10);
					ZEPHIR_CALL_FUNCTION(&_10, "bcmul", &_8, _5, _7, &_3);
					zephir_check_call_status();
					ZEPHIR_SINIT_NVAR(_3);
					ZVAL_LONG(&_3, 32);
					ZEPHIR_SINIT_NVAR(_4);
					ZVAL_LONG(&_4, 10);
					ZEPHIR_CALL_FUNCTION(&temp, "bcadd", &_11, _10, &_3, &_4);
					zephir_check_call_status();
					break;
				}
				break;
			} while(0);

		}
		break;
	} while(0);

	if (Z_TYPE_P(temp) == IS_NULL) {
		ZEPHIR_THROW_EXCEPTION_DEBUG_STR(spl_ce_DomainException, "Unknown temperature unit", "util/convert.zep", 142);
		return;
	}
	ZEPHIR_SINIT_NVAR(_3);
	ZVAL_LONG(&_3, 8);
	ZEPHIR_CALL_FUNCTION(&_5, "round", &_13, temp, &_3);
	zephir_check_call_status();
	ZEPHIR_RETURN_CALL_FUNCTION("strval", &_14, _5);
	zephir_check_call_status();
	RETURN_MM();

}

