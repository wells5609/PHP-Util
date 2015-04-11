
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
#include "kernel/concat.h"
#include "kernel/memory.h"
#include "kernel/string.h"
#include "kernel/fcall.h"
#include "kernel/operators.h"
#include "kernel/exception.h"
#include "kernel/require.h"
#include "kernel/hash.h"
#include "kernel/file.h"
#include "ext/spl/spl_exceptions.h"
#include "kernel/object.h"
#include "kernel/array.h"

ZEPHIR_INIT_CLASS(Util_ClassLoader) {

	ZEPHIR_REGISTER_CLASS(Util, ClassLoader, util, classloader, util_classloader_method_entry, 0);

	zend_declare_property_null(util_classloader_ce, SL("prefixLengthsPsr4"), ZEND_ACC_PROTECTED TSRMLS_CC);

	zend_declare_property_null(util_classloader_ce, SL("prefixDirsPsr4"), ZEND_ACC_PROTECTED TSRMLS_CC);

	zend_declare_property_null(util_classloader_ce, SL("fallbackDirsPsr4"), ZEND_ACC_PROTECTED TSRMLS_CC);

	zend_declare_property_null(util_classloader_ce, SL("prefixesPsr0"), ZEND_ACC_PROTECTED TSRMLS_CC);

	zend_declare_property_null(util_classloader_ce, SL("fallbackDirsPsr0"), ZEND_ACC_PROTECTED TSRMLS_CC);

	zend_declare_property_null(util_classloader_ce, SL("classMap"), ZEND_ACC_PROTECTED TSRMLS_CC);

	zend_declare_property_bool(util_classloader_ce, SL("useIncludePath"), 0, ZEND_ACC_PROTECTED TSRMLS_CC);

	return SUCCESS;

}

PHP_METHOD(Util_ClassLoader, composerInit) {

	zephir_fcall_cache_entry *_9 = NULL, *_14 = NULL;
	HashTable *_7, *_12, *_19;
	HashPosition _6, _11, _18;
	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_3 = NULL;
	zend_bool prepend;
	zval *vendorPath_param = NULL, *prepend_param = NULL, *composerPath, *loader, *ns = NULL, *path = NULL, *namespaces = NULL, *psr4 = NULL, *classMap = NULL, *files = NULL, *_0, _1, *_2 = NULL, *_4, *_5 = NULL, **_8, *_10, **_13, *_15, *_16, *_17, **_20;
	zval *vendorPath = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &vendorPath_param, &prepend_param);

	if (unlikely(Z_TYPE_P(vendorPath_param) != IS_STRING && Z_TYPE_P(vendorPath_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'vendorPath' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(vendorPath_param) == IS_STRING)) {
		zephir_get_strval(vendorPath, vendorPath_param);
	} else {
		ZEPHIR_INIT_VAR(vendorPath);
		ZVAL_EMPTY_STRING(vendorPath);
	}
	if (!prepend_param) {
		prepend = 1;
	} else {
		prepend = zephir_get_boolval(prepend_param);
	}


	ZEPHIR_INIT_VAR(_0);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_STRING(&_1, "/\\", 0);
	zephir_fast_trim(_0, vendorPath, &_1, ZEPHIR_TRIM_RIGHT TSRMLS_CC);
	ZEPHIR_INIT_VAR(composerPath);
	ZEPHIR_CONCAT_VSSS(composerPath, _0, "/", "composer", "/");
	ZEPHIR_CALL_FUNCTION(&_2, "is_dir", &_3, composerPath);
	zephir_check_call_status();
	if (!(zephir_is_true(_2))) {
		ZEPHIR_THROW_EXCEPTION_DEBUG_STR(spl_ce_InvalidArgumentException, "Composer directory does not exist", "util/classloader.zep", 81);
		return;
	}
	ZEPHIR_INIT_VAR(loader);
	object_init_ex(loader, util_classloader_ce);
	ZEPHIR_CALL_METHOD(NULL, loader, "__construct", NULL);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(_4);
	ZEPHIR_CONCAT_VS(_4, composerPath, "autoload_namespaces.php");
	ZEPHIR_OBSERVE_OR_NULLIFY_PPZV(&_5);
	if (zephir_require_zval_ret(&_5, _4 TSRMLS_CC) == FAILURE) {
		RETURN_MM_NULL();
	}
	ZEPHIR_CPY_WRT(namespaces, _5);
	zephir_is_iterable(namespaces, &_7, &_6, 0, 0, "util/classloader.zep", 92);
	for (
	  ; zephir_hash_get_current_data_ex(_7, (void**) &_8, &_6) == SUCCESS
	  ; zephir_hash_move_forward_ex(_7, &_6)
	) {
		ZEPHIR_GET_HMKEY(ns, _7, _6);
		ZEPHIR_GET_HVALUE(path, _8);
		ZEPHIR_CALL_METHOD(NULL, loader, "set", &_9, ns, path);
		zephir_check_call_status();
	}
	ZEPHIR_INIT_VAR(_10);
	ZEPHIR_CONCAT_VS(_10, composerPath, "autoload_psr4.php");
	ZEPHIR_OBSERVE_OR_NULLIFY_PPZV(&_5);
	if (zephir_require_zval_ret(&_5, _10 TSRMLS_CC) == FAILURE) {
		RETURN_MM_NULL();
	}
	ZEPHIR_CPY_WRT(psr4, _5);
	zephir_is_iterable(psr4, &_12, &_11, 0, 0, "util/classloader.zep", 98);
	for (
	  ; zephir_hash_get_current_data_ex(_12, (void**) &_13, &_11) == SUCCESS
	  ; zephir_hash_move_forward_ex(_12, &_11)
	) {
		ZEPHIR_GET_HMKEY(ns, _12, _11);
		ZEPHIR_GET_HVALUE(path, _13);
		ZEPHIR_CALL_METHOD(NULL, loader, "setpsr4", &_14, ns, path);
		zephir_check_call_status();
	}
	ZEPHIR_INIT_VAR(_15);
	ZEPHIR_CONCAT_VS(_15, composerPath, "autoload_classmap.php");
	ZEPHIR_OBSERVE_OR_NULLIFY_PPZV(&_5);
	if (zephir_require_zval_ret(&_5, _15 TSRMLS_CC) == FAILURE) {
		RETURN_MM_NULL();
	}
	ZEPHIR_CPY_WRT(classMap, _5);
	if (!(ZEPHIR_IS_EMPTY(classMap))) {
		ZEPHIR_CALL_METHOD(NULL, loader, "addclassmap", NULL, classMap);
		zephir_check_call_status();
	}
	ZEPHIR_CALL_METHOD(NULL, loader, "register", NULL, (prepend ? ZEPHIR_GLOBAL(global_true) : ZEPHIR_GLOBAL(global_false)));
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(_16);
	ZEPHIR_CONCAT_VS(_16, composerPath, "autoload_files.php");
	if ((zephir_file_exists(_16 TSRMLS_CC) == SUCCESS)) {
		ZEPHIR_INIT_VAR(_17);
		ZEPHIR_CONCAT_VS(_17, composerPath, "autoload_files.php");
		ZEPHIR_OBSERVE_OR_NULLIFY_PPZV(&_5);
		if (zephir_require_zval_ret(&_5, _17 TSRMLS_CC) == FAILURE) {
			RETURN_MM_NULL();
		}
		ZEPHIR_CPY_WRT(files, _5);
		zephir_is_iterable(files, &_19, &_18, 0, 0, "util/classloader.zep", 113);
		for (
		  ; zephir_hash_get_current_data_ex(_19, (void**) &_20, &_18) == SUCCESS
		  ; zephir_hash_move_forward_ex(_19, &_18)
		) {
			ZEPHIR_GET_HVALUE(path, _20);
			if (zephir_require_zval(path TSRMLS_CC) == FAILURE) {
				RETURN_MM_NULL();
			}
		}
	}
	RETURN_CCTOR(loader);

}

PHP_METHOD(Util_ClassLoader, getPrefixes) {

	zval *_0, _1;
	int ZEPHIR_LAST_CALL_STATUS;


	_0 = zephir_fetch_nproperty_this(this_ptr, SL("prefixesPsr0"), PH_NOISY_CC);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_STRING(&_1, "array_merge", 0);
	ZEPHIR_CALL_USER_FUNC_ARRAY(return_value, &_1, _0);
	zephir_check_call_status();
	return;

}

PHP_METHOD(Util_ClassLoader, getPrefixesPsr4) {


	RETURN_MEMBER(this_ptr, "prefixDirsPsr4");

}

PHP_METHOD(Util_ClassLoader, getFallbackDirs) {


	RETURN_MEMBER(this_ptr, "fallbackDirsPsr0");

}

PHP_METHOD(Util_ClassLoader, getFallbackDirsPsr4) {


	RETURN_MEMBER(this_ptr, "fallbackDirsPsr4");

}

PHP_METHOD(Util_ClassLoader, getClassMap) {


	RETURN_MEMBER(this_ptr, "classMap");

}

/**
 * @param array $classMap Class to filename map
 */
PHP_METHOD(Util_ClassLoader, addClassMap) {

	zval *classMap_param = NULL, *_0, *_1, *_2;
	zval *classMap = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &classMap_param);

	classMap = classMap_param;



	ZEPHIR_OBS_VAR(_0);
	zephir_read_property_this(&_0, this_ptr, SL("classMap"), PH_NOISY_CC);
	if (Z_TYPE_P(_0) == IS_ARRAY) {
		ZEPHIR_INIT_VAR(_1);
		_2 = zephir_fetch_nproperty_this(this_ptr, SL("classMap"), PH_NOISY_CC);
		zephir_fast_array_merge(_1, &(_2), &(classMap) TSRMLS_CC);
		zephir_update_property_this(this_ptr, SL("classMap"), _1 TSRMLS_CC);
	} else {
		zephir_update_property_this(this_ptr, SL("classMap"), classMap TSRMLS_CC);
	}
	ZEPHIR_MM_RESTORE();

}

/**
 * Registers a set of PSR-0 directories for a given prefix, either
 * appending or prepending to the ones previously set for this prefix.
 *
 * @param string       $prefix  The prefix
 * @param array|string $paths   The PSR-0 root directories
 * @param bool         $prepend Whether to prepend the directories
 */
PHP_METHOD(Util_ClassLoader, add) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_4 = NULL;
	zend_bool prepend;
	zval *prefix, *paths = NULL, *prepend_param = NULL, *_0 = NULL, *_1, *firstChar = NULL, _2, _3, *_5, *_6, *_7, *_8;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 1, &prefix, &paths, &prepend_param);

	ZEPHIR_SEPARATE_PARAM(paths);
	if (!prepend_param) {
		prepend = 0;
	} else {
		prepend = zephir_get_boolval(prepend_param);
	}


	if (Z_TYPE_P(paths) == IS_STRING) {
		ZEPHIR_INIT_NVAR(paths);
		array_init_size(paths, 2);
		zephir_array_fast_append(paths, paths);
	}
	if (Z_TYPE_P(prefix) != IS_STRING) {
		if (prepend) {
			ZEPHIR_INIT_VAR(_0);
			_1 = zephir_fetch_nproperty_this(this_ptr, SL("fallbackDirsPsr0"), PH_NOISY_CC);
			zephir_fast_array_merge(_0, &(paths), &(_1) TSRMLS_CC);
			zephir_update_property_this(this_ptr, SL("fallbackDirsPsr0"), _0 TSRMLS_CC);
		} else {
			ZEPHIR_INIT_NVAR(_0);
			_1 = zephir_fetch_nproperty_this(this_ptr, SL("fallbackDirsPsr0"), PH_NOISY_CC);
			zephir_fast_array_merge(_0, &(_1), &(paths) TSRMLS_CC);
			zephir_update_property_this(this_ptr, SL("fallbackDirsPsr0"), _0 TSRMLS_CC);
		}
		RETURN_MM_NULL();
	}
	ZEPHIR_SINIT_VAR(_2);
	ZVAL_LONG(&_2, 0);
	ZEPHIR_SINIT_VAR(_3);
	ZVAL_LONG(&_3, 1);
	ZEPHIR_CALL_FUNCTION(&firstChar, "substr", &_4, prefix, &_2, &_3);
	zephir_check_call_status();
	_1 = zephir_fetch_nproperty_this(this_ptr, SL("prefixesPsr0"), PH_NOISY_CC);
	zephir_array_fetch(&_5, _1, firstChar, PH_READONLY, "util/classloader.zep", 183 TSRMLS_CC);
	if (!(zephir_array_isset(_5, prefix))) {
		zephir_update_property_array_multi(this_ptr, SL("prefixesPsr0"), &paths TSRMLS_CC, SL("zz"), 2, firstChar, prefix);
		RETURN_MM_NULL();
	}
	if (prepend) {
		ZEPHIR_INIT_NVAR(_0);
		_6 = zephir_fetch_nproperty_this(this_ptr, SL("prefixesPsr0"), PH_NOISY_CC);
		zephir_array_fetch(&_7, _6, firstChar, PH_NOISY | PH_READONLY, "util/classloader.zep", 190 TSRMLS_CC);
		zephir_array_fetch(&_8, _7, prefix, PH_NOISY | PH_READONLY, "util/classloader.zep", 190 TSRMLS_CC);
		zephir_fast_array_merge(_0, &(paths), &(_8) TSRMLS_CC);
		zephir_update_property_array_multi(this_ptr, SL("prefixesPsr0"), &_0 TSRMLS_CC, SL("zz"), 2, firstChar, prefix);
	} else {
		ZEPHIR_INIT_NVAR(_0);
		_6 = zephir_fetch_nproperty_this(this_ptr, SL("prefixesPsr0"), PH_NOISY_CC);
		zephir_array_fetch(&_7, _6, firstChar, PH_NOISY | PH_READONLY, "util/classloader.zep", 192 TSRMLS_CC);
		zephir_array_fetch(&_8, _7, prefix, PH_NOISY | PH_READONLY, "util/classloader.zep", 192 TSRMLS_CC);
		zephir_fast_array_merge(_0, &(_8), &(paths) TSRMLS_CC);
		zephir_update_property_array_multi(this_ptr, SL("prefixesPsr0"), &_0 TSRMLS_CC, SL("zz"), 2, firstChar, prefix);
	}
	ZEPHIR_MM_RESTORE();

}

/**
 * Registers a set of PSR-4 directories for a given namespace, either
 * appending or prepending to the ones previously set for this namespace.
 *
 * @param string       $prefix  The prefix/namespace, with trailing "\\"
 * @param array|string $paths   The PSR-0 base directories
 * @param bool         $prepend Whether to prepend the directories
 * @throws \InvalidArgumentException
 */
PHP_METHOD(Util_ClassLoader, addPsr4) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_5 = NULL;
	zend_bool prepend;
	zval *prefix, *paths = NULL, *prepend_param = NULL, firstChar = zval_used_for_init, *_0, *_1 = NULL, *_2, _3, _4, *_6, *_7;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 1, &prefix, &paths, &prepend_param);

	ZEPHIR_SEPARATE_PARAM(paths);
	if (!prepend_param) {
		prepend = 0;
	} else {
		prepend = zephir_get_boolval(prepend_param);
	}


	if (Z_TYPE_P(paths) == IS_STRING) {
		ZEPHIR_INIT_NVAR(paths);
		array_init_size(paths, 2);
		zephir_array_fast_append(paths, paths);
	}
	_0 = zephir_fetch_nproperty_this(this_ptr, SL("prefixDirsPsr4"), PH_NOISY_CC);
	if (Z_TYPE_P(prefix) != IS_STRING) {
		if (prepend) {
			ZEPHIR_INIT_VAR(_1);
			_2 = zephir_fetch_nproperty_this(this_ptr, SL("fallbackDirsPsr4"), PH_NOISY_CC);
			zephir_fast_array_merge(_1, &(paths), &(_2) TSRMLS_CC);
			zephir_update_property_this(this_ptr, SL("fallbackDirsPsr4"), _1 TSRMLS_CC);
		} else {
			ZEPHIR_INIT_NVAR(_1);
			_2 = zephir_fetch_nproperty_this(this_ptr, SL("fallbackDirsPsr4"), PH_NOISY_CC);
			zephir_fast_array_merge(_1, &(_2), &(paths) TSRMLS_CC);
			zephir_update_property_this(this_ptr, SL("fallbackDirsPsr4"), _1 TSRMLS_CC);
		}
	} else if (!(zephir_array_isset(_0, prefix))) {
		ZEPHIR_SINIT_VAR(_3);
		ZVAL_LONG(&_3, 0);
		ZEPHIR_SINIT_VAR(_4);
		ZVAL_LONG(&_4, 1);
		ZEPHIR_CALL_FUNCTION(&firstChar, "substr", &_5, prefix, &_3, &_4);
		zephir_check_call_status();
		if (!(zephir_end_with_str(prefix, SL("\\")))) {
			ZEPHIR_THROW_EXCEPTION_DEBUG_STR(spl_ce_InvalidArgumentException, "A non-empty PSR-4 prefix must end with a namespace separator.", "util/classloader.zep", 228);
			return;
		}
		ZEPHIR_INIT_NVAR(_1);
		ZVAL_LONG(_1, zephir_fast_strlen_ev(prefix));
		zephir_update_property_array_multi(this_ptr, SL("prefixLengthsPsr4"), &_1 TSRMLS_CC, SL("zz"), 2, firstChar, prefix);
		zephir_update_property_array(this_ptr, SL("prefixDirsPsr4"), prefix, paths TSRMLS_CC);
	} else if (prepend) {
		ZEPHIR_INIT_NVAR(_1);
		_2 = zephir_fetch_nproperty_this(this_ptr, SL("prefixDirsPsr4"), PH_NOISY_CC);
		zephir_array_fetch(&_6, _2, prefix, PH_NOISY | PH_READONLY, "util/classloader.zep", 236 TSRMLS_CC);
		zephir_fast_array_merge(_1, &(paths), &(_6) TSRMLS_CC);
		zephir_update_property_array(this_ptr, SL("prefixDirsPsr4"), prefix, _1 TSRMLS_CC);
	} else {
		ZEPHIR_INIT_NVAR(_1);
		_7 = zephir_fetch_nproperty_this(this_ptr, SL("prefixDirsPsr4"), PH_NOISY_CC);
		zephir_array_fetch(&_6, _7, prefix, PH_NOISY | PH_READONLY, "util/classloader.zep", 240 TSRMLS_CC);
		zephir_fast_array_merge(_1, &(_6), &(paths) TSRMLS_CC);
		zephir_update_property_array(this_ptr, SL("prefixDirsPsr4"), prefix, _1 TSRMLS_CC);
	}
	ZEPHIR_MM_RESTORE();

}

/**
 * Registers a set of PSR-0 directories for a given prefix,
 * replacing any others previously set for this prefix.
 *
 * @param string       $prefix The prefix
 * @param array|string $paths  The PSR-0 base directories
 */
PHP_METHOD(Util_ClassLoader, set) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_3 = NULL;
	zval *prefix, *paths = NULL, _0, _1, *_2 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 0, &prefix, &paths);

	ZEPHIR_SEPARATE_PARAM(paths);


	if (Z_TYPE_P(paths) == IS_STRING) {
		ZEPHIR_INIT_NVAR(paths);
		array_init_size(paths, 2);
		zephir_array_fast_append(paths, paths);
	}
	if (Z_TYPE_P(prefix) == IS_STRING) {
		ZEPHIR_SINIT_VAR(_0);
		ZVAL_LONG(&_0, 0);
		ZEPHIR_SINIT_VAR(_1);
		ZVAL_LONG(&_1, 1);
		ZEPHIR_CALL_FUNCTION(&_2, "substr", &_3, prefix, &_0, &_1);
		zephir_check_call_status();
		zephir_update_property_array_multi(this_ptr, SL("prefixesPsr0"), &paths TSRMLS_CC, SL("zz"), 2, _2, prefix);
	} else {
		zephir_update_property_this(this_ptr, SL("fallbackDirsPsr0"), paths TSRMLS_CC);
	}
	ZEPHIR_MM_RESTORE();

}

/**
 * Registers a set of PSR-4 directories for a given namespace,
 * replacing any others previously set for this namespace.
 *
 * @param string       $prefix The prefix/namespace, with trailing "\\"
 * @param array|string $paths  The PSR-4 base directories
 *
 * @throws \InvalidArgumentException
 */
PHP_METHOD(Util_ClassLoader, setPsr4) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_4 = NULL;
	zval *prefix, *paths = NULL, *_0, _1, _2, *_3 = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 0, &prefix, &paths);

	ZEPHIR_SEPARATE_PARAM(paths);


	if (Z_TYPE_P(paths) == IS_STRING) {
		ZEPHIR_INIT_NVAR(paths);
		array_init_size(paths, 2);
		zephir_array_fast_append(paths, paths);
	}
	if (Z_TYPE_P(prefix) == IS_NULL) {
		zephir_update_property_this(this_ptr, SL("fallbackDirsPsr4"), paths TSRMLS_CC);
	} else {
		if (!(zephir_end_with_str(prefix, SL("\\")))) {
			ZEPHIR_THROW_EXCEPTION_DEBUG_STR(spl_ce_InvalidArgumentException, "A non-empty PSR-4 prefix must end with a namespace separator.", "util/classloader.zep", 286);
			return;
		}
		ZEPHIR_INIT_VAR(_0);
		ZVAL_LONG(_0, zephir_fast_strlen_ev(prefix));
		ZEPHIR_SINIT_VAR(_1);
		ZVAL_LONG(&_1, 0);
		ZEPHIR_SINIT_VAR(_2);
		ZVAL_LONG(&_2, 1);
		ZEPHIR_CALL_FUNCTION(&_3, "substr", &_4, prefix, &_1, &_2);
		zephir_check_call_status();
		zephir_update_property_array_multi(this_ptr, SL("prefixLengthsPsr4"), &_0 TSRMLS_CC, SL("zz"), 2, _3, prefix);
		zephir_update_property_array(this_ptr, SL("prefixDirsPsr4"), prefix, paths TSRMLS_CC);
	}
	ZEPHIR_MM_RESTORE();

}

/**
 * Turns on searching the include path for class files.
 *
 * @param bool $useIncludePath
 */
PHP_METHOD(Util_ClassLoader, setUseIncludePath) {

	zval *useIncludePath_param = NULL;
	zend_bool useIncludePath;

	zephir_fetch_params(0, 1, 0, &useIncludePath_param);

	useIncludePath = zephir_get_boolval(useIncludePath_param);


	zephir_update_property_this(this_ptr, SL("useIncludePath"), useIncludePath ? ZEPHIR_GLOBAL(global_true) : ZEPHIR_GLOBAL(global_false) TSRMLS_CC);

}

/**
 * Can be used to check if the autoloader uses the include path to check
 * for classes.
 *
 * @return bool
 */
PHP_METHOD(Util_ClassLoader, getUseIncludePath) {


	RETURN_MEMBER(this_ptr, "useIncludePath");

}

/**
 * Registers this instance as an autoloader.
 *
 * @param bool $prepend Whether to prepend the autoloader or not
 */
PHP_METHOD(Util_ClassLoader, register) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *_0;
	zval *prepend_param = NULL, *_1;
	zend_bool prepend;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 0, 1, &prepend_param);

	if (!prepend_param) {
		prepend = 0;
	} else {
		prepend = zephir_get_boolval(prepend_param);
	}


	ZEPHIR_INIT_VAR(_0);
	array_init_size(_0, 3);
	zephir_array_fast_append(_0, this_ptr);
	ZEPHIR_INIT_VAR(_1);
	ZVAL_STRING(_1, "loadClass", 1);
	zephir_array_fast_append(_0, _1);
	ZEPHIR_CALL_FUNCTION(NULL, "spl_autoload_register", NULL, _0, ZEPHIR_GLOBAL(global_true), (prepend ? ZEPHIR_GLOBAL(global_true) : ZEPHIR_GLOBAL(global_false)));
	zephir_check_call_status();
	ZEPHIR_MM_RESTORE();

}

/**
 * Unregisters this instance as an autoloader.
 */
PHP_METHOD(Util_ClassLoader, unregister) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *_1;
	zval *_0;

	ZEPHIR_MM_GROW();

	ZEPHIR_INIT_VAR(_0);
	array_init_size(_0, 3);
	zephir_array_fast_append(_0, this_ptr);
	ZEPHIR_INIT_VAR(_1);
	ZVAL_STRING(_1, "loadClass", 1);
	zephir_array_fast_append(_0, _1);
	ZEPHIR_CALL_FUNCTION(NULL, "spl_autoload_unregister", NULL, _0);
	zephir_check_call_status();
	ZEPHIR_MM_RESTORE();

}

/**
 * Loads the given class or interface.
 *
 * @param  string    $class The name of the class
 * @return bool|null True if loaded, null otherwise
 */
PHP_METHOD(Util_ClassLoader, loadClass) {

	int ZEPHIR_LAST_CALL_STATUS;
	zval *className_param = NULL, *file = NULL;
	zval *className = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &className_param);

	if (unlikely(Z_TYPE_P(className_param) != IS_STRING && Z_TYPE_P(className_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'className' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(className_param) == IS_STRING)) {
		zephir_get_strval(className, className_param);
	} else {
		ZEPHIR_INIT_VAR(className);
		ZVAL_EMPTY_STRING(className);
	}


	ZEPHIR_CALL_METHOD(&file, this_ptr, "findfile", NULL, className);
	zephir_check_call_status();
	if (Z_TYPE_P(file) == IS_STRING) {
		if (zephir_require_zval(file TSRMLS_CC) == FAILURE) {
			RETURN_MM_NULL();
		}
		RETURN_MM_BOOL(1);
	}
	ZEPHIR_MM_RESTORE();

}

/**
 * Finds the path to the file where the class is defined.
 *
 * @param string $class The name of the class
 *
 * @return string|false The path if found, false otherwise
 */
PHP_METHOD(Util_ClassLoader, findFile) {

	zephir_nts_static zephir_fcall_cache_entry *_8 = NULL;
	int ZEPHIR_LAST_CALL_STATUS;
	zval *className_param = NULL, *_0, _1, *_2, *_3, *_4, *file = NULL, *_5 = NULL, _6, *_7 = NULL;
	zval *className = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 0, &className_param);

	if (unlikely(Z_TYPE_P(className_param) != IS_STRING && Z_TYPE_P(className_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'className' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(className_param) == IS_STRING)) {
		zephir_get_strval(className, className_param);
	} else {
		ZEPHIR_INIT_VAR(className);
		ZVAL_EMPTY_STRING(className);
	}


	ZEPHIR_INIT_VAR(_0);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_STRING(&_1, "\\", 0);
	zephir_fast_trim(_0, className, &_1, ZEPHIR_TRIM_LEFT TSRMLS_CC);
	zephir_get_strval(className, _0);
	_2 = zephir_fetch_nproperty_this(this_ptr, SL("classMap"), PH_NOISY_CC);
	if (zephir_array_isset(_2, className)) {
		_3 = zephir_fetch_nproperty_this(this_ptr, SL("classMap"), PH_NOISY_CC);
		zephir_array_fetch(&_4, _3, className, PH_NOISY | PH_READONLY, "util/classloader.zep", 364 TSRMLS_CC);
		RETURN_CTOR(_4);
	}
	ZEPHIR_INIT_VAR(_5);
	ZVAL_STRING(_5, ".php", ZEPHIR_TEMP_PARAM_COPY);
	ZEPHIR_CALL_METHOD(&file, this_ptr, "findfilewithextension", NULL, className, _5);
	zephir_check_temp_parameter(_5);
	zephir_check_call_status();
	if (Z_TYPE_P(file) == IS_NULL) {
		ZEPHIR_SINIT_VAR(_6);
		ZVAL_STRING(&_6, "HHVM_VERSION", 0);
		ZEPHIR_CALL_FUNCTION(&_7, "defined", &_8, &_6);
		zephir_check_call_status();
		if (zephir_is_true(_7)) {
			ZEPHIR_INIT_NVAR(_5);
			ZVAL_STRING(_5, ".hh", ZEPHIR_TEMP_PARAM_COPY);
			ZEPHIR_CALL_METHOD(&file, this_ptr, "findfilewithextension", NULL, className, _5);
			zephir_check_temp_parameter(_5);
			zephir_check_call_status();
			if (Z_TYPE_P(file) == IS_STRING) {
				RETURN_CCTOR(file);
			}
		}
		zephir_update_property_array(this_ptr, SL("classMap"), className, ZEPHIR_GLOBAL(global_false) TSRMLS_CC);
		RETURN_MM_BOOL(0);
	}
	RETURN_CCTOR(file);

}

PHP_METHOD(Util_ClassLoader, findFileWithExtension) {

	HashTable *_9, *_14, *_18, *_24, *_27, *_30;
	HashPosition _8, _13, _17, _23, _26, _29;
	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_3 = NULL, *_4 = NULL, *_20 = NULL, *_32 = NULL;
	zval *className_param = NULL, *ext_param = NULL, *logicalPathPsr4, *firstChar = NULL, *prefix = NULL, *length = NULL, *dir = NULL, *file = NULL, *logicalPathPsr0, *nsPos = NULL, *dirs = NULL, _0 = zval_used_for_init, _1 = zval_used_for_init, *_2 = NULL, *_5, *_6, *_7, **_10, *_11, *_12, **_15, *_16 = NULL, **_19, *_21 = NULL, *_22 = NULL, **_25, **_28, **_31;
	zval *className = NULL, *ext = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 0, &className_param, &ext_param);

	if (unlikely(Z_TYPE_P(className_param) != IS_STRING && Z_TYPE_P(className_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'className' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(className_param) == IS_STRING)) {
		zephir_get_strval(className, className_param);
	} else {
		ZEPHIR_INIT_VAR(className);
		ZVAL_EMPTY_STRING(className);
	}
	zephir_get_strval(ext, ext_param);


	ZEPHIR_SINIT_VAR(_0);
	ZVAL_STRING(&_0, "\\", 0);
	ZEPHIR_SINIT_VAR(_1);
	ZVAL_STRING(&_1, "/", 0);
	ZEPHIR_CALL_FUNCTION(&_2, "strtr", &_3, className, &_0, &_1);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(logicalPathPsr4);
	ZEPHIR_CONCAT_VV(logicalPathPsr4, _2, ext);
	ZEPHIR_SINIT_NVAR(_0);
	ZVAL_LONG(&_0, 0);
	ZEPHIR_SINIT_NVAR(_1);
	ZVAL_LONG(&_1, 1);
	ZEPHIR_CALL_FUNCTION(&firstChar, "substr", &_4, className, &_0, &_1);
	zephir_check_call_status();
	_5 = zephir_fetch_nproperty_this(this_ptr, SL("prefixLengthsPsr4"), PH_NOISY_CC);
	if (zephir_array_isset(_5, firstChar)) {
		_6 = zephir_fetch_nproperty_this(this_ptr, SL("prefixLengthsPsr4"), PH_NOISY_CC);
		zephir_array_fetch(&_7, _6, firstChar, PH_NOISY | PH_READONLY, "util/classloader.zep", 402 TSRMLS_CC);
		zephir_is_iterable(_7, &_9, &_8, 0, 0, "util/classloader.zep", 416);
		for (
		  ; zephir_hash_get_current_data_ex(_9, (void**) &_10, &_8) == SUCCESS
		  ; zephir_hash_move_forward_ex(_9, &_8)
		) {
			ZEPHIR_GET_HMKEY(prefix, _9, _8);
			ZEPHIR_GET_HVALUE(length, _10);
			if (zephir_start_with(className, prefix, NULL)) {
				_11 = zephir_fetch_nproperty_this(this_ptr, SL("prefixDirsPsr4"), PH_NOISY_CC);
				zephir_array_fetch(&_12, _11, prefix, PH_NOISY | PH_READONLY, "util/classloader.zep", 406 TSRMLS_CC);
				zephir_is_iterable(_12, &_14, &_13, 0, 0, "util/classloader.zep", 414);
				for (
				  ; zephir_hash_get_current_data_ex(_14, (void**) &_15, &_13) == SUCCESS
				  ; zephir_hash_move_forward_ex(_14, &_13)
				) {
					ZEPHIR_GET_HVALUE(dir, _15);
					ZEPHIR_CALL_FUNCTION(&_16, "substr", &_4, logicalPathPsr4, length);
					zephir_check_call_status();
					ZEPHIR_INIT_NVAR(file);
					ZEPHIR_CONCAT_VSV(file, dir, "/", _16);
					if ((zephir_file_exists(file TSRMLS_CC) == SUCCESS)) {
						RETURN_CCTOR(file);
					}
				}
			}
		}
	}
	_6 = zephir_fetch_nproperty_this(this_ptr, SL("fallbackDirsPsr4"), PH_NOISY_CC);
	zephir_is_iterable(_6, &_18, &_17, 0, 0, "util/classloader.zep", 428);
	for (
	  ; zephir_hash_get_current_data_ex(_18, (void**) &_19, &_17) == SUCCESS
	  ; zephir_hash_move_forward_ex(_18, &_17)
	) {
		ZEPHIR_GET_HVALUE(dir, _19);
		ZEPHIR_INIT_NVAR(file);
		ZEPHIR_CONCAT_VSV(file, dir, "/", logicalPathPsr4);
		if ((zephir_file_exists(file TSRMLS_CC) == SUCCESS)) {
			RETURN_CCTOR(file);
		}
	}
	ZEPHIR_SINIT_NVAR(_0);
	ZVAL_STRING(&_0, "\\", 0);
	ZEPHIR_CALL_FUNCTION(&nsPos, "strrpos", &_20, className, &_0);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(logicalPathPsr0);
	if (zephir_is_true(nsPos)) {
		ZEPHIR_SINIT_NVAR(_0);
		ZVAL_LONG(&_0, 0);
		ZEPHIR_SINIT_NVAR(_1);
		ZVAL_LONG(&_1, (zephir_get_numberval(nsPos) + 1));
		ZEPHIR_CALL_FUNCTION(&_16, "substr", &_4, logicalPathPsr4, &_0, &_1);
		zephir_check_call_status();
		ZEPHIR_SINIT_NVAR(_0);
		ZVAL_LONG(&_0, (zephir_get_numberval(nsPos) + 1));
		ZEPHIR_CALL_FUNCTION(&_21, "substr", &_4, logicalPathPsr4, &_0);
		zephir_check_call_status();
		ZEPHIR_SINIT_NVAR(_0);
		ZVAL_STRING(&_0, "_", 0);
		ZEPHIR_SINIT_NVAR(_1);
		ZVAL_STRING(&_1, "/", 0);
		ZEPHIR_CALL_FUNCTION(&_22, "strtr", &_3, _21, &_0, &_1);
		zephir_check_call_status();
		ZEPHIR_CONCAT_VV(logicalPathPsr0, _16, _22);
	} else {
		ZEPHIR_SINIT_NVAR(_0);
		ZVAL_STRING(&_0, "_", 0);
		ZEPHIR_SINIT_NVAR(_1);
		ZVAL_STRING(&_1, "/", 0);
		ZEPHIR_CALL_FUNCTION(&_21, "strtr", &_3, className, &_0, &_1);
		zephir_check_call_status();
		ZEPHIR_CONCAT_VV(logicalPathPsr0, _21, ext);
	}
	_6 = zephir_fetch_nproperty_this(this_ptr, SL("prefixesPsr0"), PH_NOISY_CC);
	if (zephir_array_isset(_6, firstChar)) {
		_11 = zephir_fetch_nproperty_this(this_ptr, SL("prefixesPsr0"), PH_NOISY_CC);
		zephir_array_fetch(&_7, _11, firstChar, PH_NOISY | PH_READONLY, "util/classloader.zep", 443 TSRMLS_CC);
		zephir_is_iterable(_7, &_24, &_23, 0, 0, "util/classloader.zep", 457);
		for (
		  ; zephir_hash_get_current_data_ex(_24, (void**) &_25, &_23) == SUCCESS
		  ; zephir_hash_move_forward_ex(_24, &_23)
		) {
			ZEPHIR_GET_HMKEY(prefix, _24, _23);
			ZEPHIR_GET_HVALUE(dirs, _25);
			if (zephir_start_with(className, prefix, NULL)) {
				zephir_is_iterable(dirs, &_27, &_26, 0, 0, "util/classloader.zep", 455);
				for (
				  ; zephir_hash_get_current_data_ex(_27, (void**) &_28, &_26) == SUCCESS
				  ; zephir_hash_move_forward_ex(_27, &_26)
				) {
					ZEPHIR_GET_HVALUE(dir, _28);
					ZEPHIR_INIT_NVAR(file);
					ZEPHIR_CONCAT_VSV(file, dir, "/", logicalPathPsr0);
					if ((zephir_file_exists(file TSRMLS_CC) == SUCCESS)) {
						RETURN_CCTOR(file);
					}
				}
			}
		}
	}
	_6 = zephir_fetch_nproperty_this(this_ptr, SL("fallbackDirsPsr0"), PH_NOISY_CC);
	zephir_is_iterable(_6, &_30, &_29, 0, 0, "util/classloader.zep", 470);
	for (
	  ; zephir_hash_get_current_data_ex(_30, (void**) &_31, &_29) == SUCCESS
	  ; zephir_hash_move_forward_ex(_30, &_29)
	) {
		ZEPHIR_GET_HVALUE(dir, _31);
		ZEPHIR_INIT_NVAR(file);
		ZEPHIR_CONCAT_VSV(file, dir, "/", logicalPathPsr0);
		if ((zephir_file_exists(file TSRMLS_CC) == SUCCESS)) {
			RETURN_CCTOR(file);
		}
	}
	_6 = zephir_fetch_nproperty_this(this_ptr, SL("useIncludePath"), PH_NOISY_CC);
	if (zephir_is_true(_6)) {
		ZEPHIR_CALL_FUNCTION(&file, "stream_resolve_include_path", &_32, logicalPathPsr0);
		zephir_check_call_status();
		if (zephir_is_true(file)) {
			RETURN_CCTOR(file);
		}
	}
	RETURN_MM_NULL();

}

PHP_METHOD(Util_ClassLoader, __construct) {

	zval *_0, *_1, *_2, *_3, *_4, *_5;

	ZEPHIR_MM_GROW();

	ZEPHIR_INIT_VAR(_0);
	array_init(_0);
	zephir_update_property_this(this_ptr, SL("classMap"), _0 TSRMLS_CC);
	ZEPHIR_INIT_VAR(_1);
	array_init(_1);
	zephir_update_property_this(this_ptr, SL("fallbackDirsPsr0"), _1 TSRMLS_CC);
	ZEPHIR_INIT_VAR(_2);
	array_init(_2);
	zephir_update_property_this(this_ptr, SL("prefixesPsr0"), _2 TSRMLS_CC);
	ZEPHIR_INIT_VAR(_3);
	array_init(_3);
	zephir_update_property_this(this_ptr, SL("fallbackDirsPsr4"), _3 TSRMLS_CC);
	ZEPHIR_INIT_VAR(_4);
	array_init(_4);
	zephir_update_property_this(this_ptr, SL("prefixDirsPsr4"), _4 TSRMLS_CC);
	ZEPHIR_INIT_VAR(_5);
	array_init(_5);
	zephir_update_property_this(this_ptr, SL("prefixLengthsPsr4"), _5 TSRMLS_CC);
	ZEPHIR_MM_RESTORE();

}

