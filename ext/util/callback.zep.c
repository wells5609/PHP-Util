
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
#include "kernel/memory.h"
#include "kernel/operators.h"
#include "kernel/concat.h"
#include "kernel/array.h"
#include "Zend/zend_closures.h"
#include "kernel/exception.h"
#include "kernel/hash.h"
#include "ext/spl/spl_exceptions.h"


ZEPHIR_INIT_CLASS(Util_Callback) {

	ZEPHIR_REGISTER_CLASS(Util, Callback, util, callback, util_callback_method_entry, 0);

	return SUCCESS;

}

/**
 * Returns the result of a closure or invokable object, or returns the argument unmodified.
 *
 * @author FuelPHP
 *
 * @param mixed $var Anything; executed if Closure or invokable object.
 * @return mixed Result of callback if callable, otherwise original value.
 */
PHP_METHOD(Util_Callback, result) {

	int ZEPHIR_LAST_CALL_STATUS;
	zend_bool _0;
	zval *arg;

	zephir_fetch_params(0, 1, 0, &arg);



	_0 = Z_TYPE_P(arg) == IS_OBJECT;
	if (_0) {
		_0 = (zephir_method_exists_ex(arg, SS("__invoke") TSRMLS_CC) == SUCCESS);
	}
	if (_0) {
		ZEPHIR_CALL_USER_FUNC(return_value, arg);
		zephir_check_call_status();
		return;
	}
	RETVAL_ZVAL(arg, 1, 0);
	return;

}

/**
 * Returns a human-readable identifier for a callable.
 *
 * @param callable callback Callable.
 * @return string Human-readable callable identifier, or NULL if invalid.
 */
PHP_METHOD(Util_Callback, id) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *callback, *_0, *_1, *_2, *_3, *_4 = NULL, *_5 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &callback);



	ZEPHIR_INIT_VAR(_0);
	zephir_gettype(_0, callback TSRMLS_CC);
	do {
		if (ZEPHIR_IS_STRING(_0, "string")) {
			ZEPHIR_CONCAT_VS(return_value, callback, "()");
			RETURN_MM();
		}
		if (ZEPHIR_IS_STRING(_0, "array")) {
			ZEPHIR_OBS_VAR(_1);
			zephir_array_fetch_long(&_1, callback, 0, PH_NOISY, "util/callback.zep", 45 TSRMLS_CC);
			if (Z_TYPE_P(_1) == IS_STRING) {
				zephir_array_fetch_long(&_2, callback, 0, PH_NOISY | PH_READONLY, "util/callback.zep", 46 TSRMLS_CC);
				zephir_array_fetch_long(&_3, callback, 1, PH_NOISY | PH_READONLY, "util/callback.zep", 46 TSRMLS_CC);
				ZEPHIR_CONCAT_VSVS(return_value, _2, "::", _3, "()");
				RETURN_MM();
			}
			ZEPHIR_INIT_VAR(_4);
			zephir_array_fetch_long(&_2, callback, 0, PH_NOISY | PH_READONLY, "util/callback.zep", 49 TSRMLS_CC);
			zephir_get_class(_4, _2, 0 TSRMLS_CC);
			zephir_array_fetch_long(&_3, callback, 1, PH_NOISY | PH_READONLY, "util/callback.zep", 49 TSRMLS_CC);
			ZEPHIR_CONCAT_VSVS(return_value, _4, "->", _3, "()");
			RETURN_MM();
		}
		if (ZEPHIR_IS_STRING(_0, "object")) {
			if (zephir_instance_of_ev(callback, zend_ce_closure TSRMLS_CC)) {
				ZEPHIR_CALL_FUNCTION(&_5, "spl_object_hash", NULL, callback);
				zephir_check_call_status();
				ZEPHIR_CONCAT_SV(return_value, "Closure_", _5);
				RETURN_MM();
			}
			ZEPHIR_INIT_NVAR(_4);
			zephir_get_class(_4, callback, 0 TSRMLS_CC);
			ZEPHIR_CONCAT_VS(return_value, _4, "::__invoke()");
			RETURN_MM();
		}
		RETURN_MM_NULL();
	} while(0);

	ZEPHIR_MM_RESTORE();

}

/**
 * Invokes a callback using array of arguments.
 *
 * Uses the Reflection API to invoke an arbitrary callable.
 *
 * Arguments can be named and/or not in the proper order, as they will be ordered by variable name via reflection.
 *
 * Use case: Ordering an array of regex matches from URI routing as callback parameters.
 *
 * @param callable $callback Callable callback function.
 * @param array $args Array of callback parameters.
 * @return mixed Result of callback function.
 * @throws \LogicException if given an invalid callable.
 * @throws \RuntimeException if missing a required callback parameter.
 */
PHP_METHOD(Util_Callback, invoke) {

	zephir_fcall_cache_entry *_12 = NULL;
	HashTable *_8;
	HashPosition _7;
	int ZEPHIR_LAST_CALL_STATUS;
	zend_bool _0;
	zval *args = NULL;
	zval *callback, *args_param = NULL, *type = NULL, *refl = NULL, *params, *idx = NULL, *param = NULL, *pName = NULL, *_1, *_2, *_3 = NULL, *_4 = NULL, *_5 = NULL, *_6 = NULL, **_9, *_10 = NULL, *_11 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &callback, &args_param);

	if (!args_param) {
		ZEPHIR_INIT_VAR(args);
		array_init(args);
	} else {
	args = args_param;

	}


	_0 = Z_TYPE_P(callback) == IS_STRING;
	if (!(_0)) {
		_0 = zephir_instance_of_ev(callback, zend_ce_closure TSRMLS_CC);
	}
	if (_0) {
		ZEPHIR_INIT_VAR(refl);
		object_init_ex(refl, zephir_get_internal_ce(SS("reflectionfunction") TSRMLS_CC));
		ZEPHIR_CALL_METHOD(NULL, refl, "__construct", NULL, callback);
		zephir_check_call_status();
		ZEPHIR_INIT_VAR(type);
		ZVAL_STRING(type, "func", 1);
	} else if (Z_TYPE_P(callback) == IS_ARRAY) {
		ZEPHIR_INIT_NVAR(refl);
		object_init_ex(refl, zephir_get_internal_ce(SS("reflectionmethod") TSRMLS_CC));
		zephir_array_fetch_long(&_1, callback, 0, PH_NOISY | PH_READONLY, "util/callback.zep", 89 TSRMLS_CC);
		zephir_array_fetch_long(&_2, callback, 1, PH_NOISY | PH_READONLY, "util/callback.zep", 89 TSRMLS_CC);
		ZEPHIR_CALL_METHOD(NULL, refl, "__construct", NULL, _1, _2);
		zephir_check_call_status();
		ZEPHIR_INIT_NVAR(type);
		ZVAL_STRING(type, "method", 1);
	} else if (Z_TYPE_P(callback) == IS_OBJECT) {
		ZEPHIR_INIT_NVAR(refl);
		object_init_ex(refl, zephir_get_internal_ce(SS("reflectionmethod") TSRMLS_CC));
		ZEPHIR_INIT_VAR(_3);
		zephir_get_class(_3, callback, 0 TSRMLS_CC);
		ZEPHIR_INIT_VAR(_4);
		ZVAL_STRING(_4, "__invoke", ZEPHIR_TEMP_PARAM_COPY);
		ZEPHIR_CALL_METHOD(NULL, refl, "__construct", NULL, _3, _4);
		zephir_check_temp_parameter(_4);
		zephir_check_call_status();
		ZEPHIR_INIT_NVAR(type);
		ZVAL_STRING(type, "object", 1);
	} else {
		ZEPHIR_INIT_NVAR(_3);
		object_init_ex(_3, spl_ce_InvalidArgumentException);
		ZEPHIR_INIT_NVAR(_4);
		zephir_gettype(_4, callback TSRMLS_CC);
		ZEPHIR_INIT_VAR(_5);
		ZEPHIR_CONCAT_SV(_5, "Invalid callback, given: ", _4);
		ZEPHIR_CALL_METHOD(NULL, _3, "__construct", NULL, _5);
		zephir_check_call_status();
		zephir_throw_exception_debug(_3, "util/callback.zep", 97 TSRMLS_CC);
		ZEPHIR_MM_RESTORE();
		return;
	}
	ZEPHIR_INIT_VAR(params);
	array_init(params);
	ZEPHIR_CALL_METHOD(&_6, refl, "getparameters", NULL);
	zephir_check_call_status();
	zephir_is_iterable(_6, &_8, &_7, 0, 0, "util/callback.zep", 120);
	for (
	  ; zephir_hash_get_current_data_ex(_8, (void**) &_9, &_7) == SUCCESS
	  ; zephir_hash_move_forward_ex(_8, &_7)
	) {
		ZEPHIR_GET_HMKEY(idx, _8, _7);
		ZEPHIR_GET_HVALUE(param, _9);
		ZEPHIR_CALL_METHOD(&pName, param, "getname", NULL);
		zephir_check_call_status();
		ZEPHIR_CALL_METHOD(&_10, param, "isdefaultvalueavailable", NULL);
		zephir_check_call_status();
		if (zephir_array_isset(args, pName)) {
			zephir_array_fetch(&_1, args, pName, PH_NOISY | PH_READONLY, "util/callback.zep", 107 TSRMLS_CC);
			zephir_array_update_zval(&params, pName, &_1, PH_COPY | PH_SEPARATE);
		} else if (zephir_array_isset(args, idx)) {
			zephir_array_fetch(&_2, args, idx, PH_NOISY | PH_READONLY, "util/callback.zep", 110 TSRMLS_CC);
			zephir_array_update_zval(&params, pName, &_2, PH_COPY | PH_SEPARATE);
		} else if (zephir_is_true(_10)) {
			ZEPHIR_CALL_METHOD(&_11, param, "getdefaultvalue", NULL);
			zephir_check_call_status();
			zephir_array_update_zval(&params, pName, &_11, PH_COPY | PH_SEPARATE);
		} else {
			ZEPHIR_INIT_NVAR(_3);
			object_init_ex(_3, spl_ce_RuntimeException);
			ZEPHIR_INIT_LNVAR(_5);
			ZEPHIR_CONCAT_SVS(_5, "Missing parameter: '", pName, "'.");
			ZEPHIR_CALL_METHOD(NULL, _3, "__construct", &_12, _5);
			zephir_check_call_status();
			zephir_throw_exception_debug(_3, "util/callback.zep", 116 TSRMLS_CC);
			ZEPHIR_MM_RESTORE();
			return;
		}
	}
	do {
		if (ZEPHIR_IS_STRING(type, "func")) {
			ZEPHIR_RETURN_CALL_METHOD(refl, "invokeargs", NULL, params);
			zephir_check_call_status();
			RETURN_MM();
		}
		if (ZEPHIR_IS_STRING(type, "method")) {
			ZEPHIR_INIT_NVAR(_3);
			ZEPHIR_CALL_METHOD(&_10, refl, "isstatic", NULL);
			zephir_check_call_status();
			if (zephir_is_true(_10)) {
				ZEPHIR_CALL_USER_FUNC_ARRAY(_3, callback, params);
				zephir_check_call_status();
			} else {
				zephir_array_fetch_long(&_1, callback, 0, PH_NOISY | PH_READONLY, "util/callback.zep", 128 TSRMLS_CC);
				ZEPHIR_CALL_METHOD(&_3, refl, "invokeargs", NULL, _1, params);
				zephir_check_call_status();
			}
			RETURN_CCTOR(_3);
		}
		if (ZEPHIR_IS_STRING(type, "object")) {
			ZEPHIR_RETURN_CALL_METHOD(refl, "invokeargs", NULL, callback, params);
			zephir_check_call_status();
			RETURN_MM();
		}
	} while(0);

	ZEPHIR_MM_RESTORE();

}

