
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
#include "kernel/string.h"
#include "kernel/hash.h"
#include "kernel/array.h"
#include "kernel/object.h"


ZEPHIR_INIT_CLASS(Util_Arr) {

	ZEPHIR_REGISTER_CLASS(Util, Arr, util, arr, util_arr_method_entry, 0);

	return SUCCESS;

}

/**
 * Merges a vector of arrays.
 *
 * More performant than using array_merge in a loop.
 *
 * @author facebook/libphutil
 *
 * @param array $arrays Array of arrays to merge.
 * @return array Merged arrays.
 */
PHP_METHOD(Util_Arr, mergev) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *arrays_param = NULL, *_0, _1;
	zval *arrays = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &arrays_param);

	zephir_get_arrval(arrays, arrays_param);


	ZEPHIR_INIT_VAR(_0);
	if (ZEPHIR_IS_EMPTY(arrays)) {
		array_init(_0);
	} else {
		ZEPHIR_SINIT_VAR(_1);
		ZVAL_STRING(&_1, "array_merge", 0);
		ZEPHIR_CALL_USER_FUNC_ARRAY(_0, &_1, arrays);
		zephir_check_call_status();
	}
	RETURN_CCTOR(_0);

}

/**
 * Returns an array of elements that satisfy the given conditions.
 *
 * @param array array Array of arrays or objects.
 * @param array conditions Associative array of keys/properties and values.
 * @param string operator One of "AND", "OR", or "NOT". Default "AND".
 * @return array Array elements that satisfy the conditions.
 */
PHP_METHOD(Util_Arr, select) {

	zend_bool _6, _13, _15, _16, _18;
	HashTable *_1, *_4, *_9;
	HashPosition _0, _3, _8;
	int match;
	zval *operator = NULL;
	zval *arr_param = NULL, *conditions_param = NULL, *operator_param = NULL, *filtered, *oper, *numCond, *key = NULL, *obj = NULL, *mKey = NULL, *mVal = NULL, **_2, **_5, *_7, **_10, *_11 = NULL, _12 = zval_used_for_init, _14 = zval_used_for_init, _17 = zval_used_for_init;
	zval *arr = NULL, *conditions = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 1, &arr_param, &conditions_param, &operator_param);

	zephir_get_arrval(arr, arr_param);
	zephir_get_arrval(conditions, conditions_param);
	if (!operator_param) {
		ZEPHIR_INIT_VAR(operator);
		ZVAL_STRING(operator, "AND", 1);
	} else {
		zephir_get_strval(operator, operator_param);
	}


	if (unlikely(ZEPHIR_IS_EMPTY(conditions))) {
		RETURN_CTOR(arr);
	}
	ZEPHIR_INIT_VAR(filtered);
	array_init(filtered);
	ZEPHIR_INIT_VAR(oper);
	zephir_fast_strtoupper(oper, operator);
	ZEPHIR_INIT_VAR(numCond);
	ZVAL_LONG(numCond, zephir_fast_count_int(conditions TSRMLS_CC));
	zephir_is_iterable(arr, &_1, &_0, 0, 0, "util/arr.zep", 67);
	for (
	  ; zephir_hash_get_current_data_ex(_1, (void**) &_2, &_0) == SUCCESS
	  ; zephir_hash_move_forward_ex(_1, &_0)
	) {
		ZEPHIR_GET_HMKEY(key, _1, _0);
		ZEPHIR_GET_HVALUE(obj, _2);
		match = 0;
		if (Z_TYPE_P(obj) == IS_ARRAY) {
			zephir_is_iterable(conditions, &_4, &_3, 0, 0, "util/arr.zep", 53);
			for (
			  ; zephir_hash_get_current_data_ex(_4, (void**) &_5, &_3) == SUCCESS
			  ; zephir_hash_move_forward_ex(_4, &_3)
			) {
				ZEPHIR_GET_HMKEY(mKey, _4, _3);
				ZEPHIR_GET_HVALUE(mVal, _5);
				_6 = zephir_array_key_exists(obj, mKey TSRMLS_CC);
				if (_6) {
					zephir_array_fetch(&_7, obj, mKey, PH_NOISY | PH_READONLY, "util/arr.zep", 48 TSRMLS_CC);
					_6 = ZEPHIR_IS_EQUAL(mVal, _7);
				}
				if (_6) {
					match++;
				}
			}
		} else if (Z_TYPE_P(obj) == IS_OBJECT) {
			zephir_is_iterable(conditions, &_9, &_8, 0, 0, "util/arr.zep", 60);
			for (
			  ; zephir_hash_get_current_data_ex(_9, (void**) &_10, &_8) == SUCCESS
			  ; zephir_hash_move_forward_ex(_9, &_8)
			) {
				ZEPHIR_GET_HMKEY(mKey, _9, _8);
				ZEPHIR_GET_HVALUE(mVal, _10);
				_6 = zephir_isset_property(obj, SS("mKey") TSRMLS_CC);
				if (_6) {
					ZEPHIR_OBS_NVAR(_11);
					zephir_read_property(&_11, obj, SL("mKey"), PH_NOISY_CC);
					_6 = ZEPHIR_IS_EQUAL(mVal, _11);
				}
				if (_6) {
					match++;
				}
			}
		}
		ZEPHIR_SINIT_NVAR(_12);
		ZVAL_STRING(&_12, "AND", 0);
		_6 = ZEPHIR_IS_IDENTICAL(&_12, oper);
		if (_6) {
			_6 = ZEPHIR_IS_LONG(numCond, match);
		}
		_13 = _6;
		if (!(_13)) {
			ZEPHIR_SINIT_NVAR(_14);
			ZVAL_STRING(&_14, "OR", 0);
			_15 = ZEPHIR_IS_IDENTICAL(&_14, oper);
			if (_15) {
				_15 = match > 0;
			}
			_13 = _15;
		}
		_16 = _13;
		if (!(_16)) {
			ZEPHIR_SINIT_NVAR(_17);
			ZVAL_STRING(&_17, "NOT", 0);
			_18 = ZEPHIR_IS_IDENTICAL(&_17, oper);
			if (_18) {
				_18 = 0 == match;
			}
			_16 = _18;
		}
		if (_16) {
			zephir_array_update_zval(&filtered, key, &obj, PH_COPY | PH_SEPARATE);
		}
	}
	RETURN_CCTOR(filtered);

}

