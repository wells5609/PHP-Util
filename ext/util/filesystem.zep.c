
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
#include "kernel/fcall.h"
#include "kernel/operators.h"
#include "ext/spl/spl_exceptions.h"
#include "kernel/exception.h"
#include "kernel/memory.h"
#include "kernel/hash.h"
#include "kernel/concat.h"
#include "kernel/string.h"
#include "kernel/array.h"
#include "kernel/file.h"


ZEPHIR_INIT_CLASS(Util_Filesystem) {

	ZEPHIR_REGISTER_CLASS(Util, Filesystem, util, filesystem, util_filesystem_method_entry, 0);

	return SUCCESS;

}

/**
 * Scans a directory for files and directories, optionally skipping "." and ".."
 *
 * @param string! dir Directory path
 * @param boolean skip_dots Skip "." and ".." (default true)
 * @return array Files in the directory
 */
PHP_METHOD(Util_Filesystem, scandir) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_0 = NULL, *_1 = NULL;
	zend_bool skip_dots;
	zval *dir_param = NULL, *skip_dots_param = NULL, *contents = NULL;
	zval *dir = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &dir_param, &skip_dots_param);

	if (unlikely(Z_TYPE_P(dir_param) != IS_STRING && Z_TYPE_P(dir_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'dir' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(dir_param) == IS_STRING)) {
		zephir_get_strval(dir, dir_param);
	} else {
		ZEPHIR_INIT_VAR(dir);
		ZVAL_EMPTY_STRING(dir);
	}
	if (!skip_dots_param) {
		skip_dots = 1;
	} else {
		skip_dots = zephir_get_boolval(skip_dots_param);
	}


	ZEPHIR_CALL_FUNCTION(&contents, "scandir", &_0, dir);
	zephir_check_call_status();
	if (skip_dots) {
		if (!(ZEPHIR_IS_EMPTY(contents))) {
			Z_SET_ISREF_P(contents);
			ZEPHIR_CALL_FUNCTION(NULL, "array_shift", &_1, contents);
			Z_UNSET_ISREF_P(contents);
			zephir_check_call_status();
			Z_SET_ISREF_P(contents);
			ZEPHIR_CALL_FUNCTION(NULL, "array_shift", &_1, contents);
			Z_UNSET_ISREF_P(contents);
			zephir_check_call_status();
		}
	}
	RETURN_CCTOR(contents);

}

/**
 * Removes a directory, optionally recursively.
 *
 * @param string! dir Directory path
 * @param boolean recursive Whether to recurse directories. Default true
 * @return boolean True on success or false on failure
 */
PHP_METHOD(Util_Filesystem, rmdir) {

	zephir_fcall_cache_entry *_6 = NULL;
	HashTable *_4;
	HashPosition _3;
	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_0 = NULL, *_8 = NULL;
	zend_bool recursive;
	zval *dir_param = NULL, *recursive_param = NULL, *path = NULL, *item = NULL, *_1 = NULL, *_2 = NULL, **_5, *_7 = NULL;
	zval *dir = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &dir_param, &recursive_param);

	if (unlikely(Z_TYPE_P(dir_param) != IS_STRING && Z_TYPE_P(dir_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'dir' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(dir_param) == IS_STRING)) {
		zephir_get_strval(dir, dir_param);
	} else {
		ZEPHIR_INIT_VAR(dir);
		ZVAL_EMPTY_STRING(dir);
	}
	if (!recursive_param) {
		recursive = 1;
	} else {
		recursive = zephir_get_boolval(recursive_param);
	}


	ZEPHIR_CALL_FUNCTION(&path, "realpath", &_0, dir);
	zephir_check_call_status();
	if (!(zephir_is_true(path))) {
		RETURN_MM_BOOL(1);
	}
	if (recursive) {
		ZEPHIR_INIT_VAR(_2);
		ZVAL_BOOL(_2, 1);
		ZEPHIR_CALL_SELF(&_1, "scandir", NULL, path, _2);
		zephir_check_call_status();
		zephir_is_iterable(_1, &_4, &_3, 0, 0, "util/filesystem.zep", 50);
		for (
		  ; zephir_hash_get_current_data_ex(_4, (void**) &_5, &_3) == SUCCESS
		  ; zephir_hash_move_forward_ex(_4, &_3)
		) {
			ZEPHIR_GET_HVALUE(item, _5);
			ZEPHIR_INIT_LNVAR(_7);
			ZEPHIR_CONCAT_VSV(_7, path, "/", item);
			ZEPHIR_INIT_NVAR(_2);
			ZVAL_BOOL(_2, 1);
			ZEPHIR_CALL_SELF(NULL, "delete", &_6, _7, _2);
			zephir_check_call_status();
		}
	}
	ZEPHIR_RETURN_CALL_FUNCTION("rmdir", &_8, path);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Deletes a file or directory, optionally recursively.
 *
 * @param string path File or directory path
 * @param boolean recursive Whether to recurse directories. Default true
 * @return boolean True on success, or false if failure.
 */
PHP_METHOD(Util_Filesystem, delete) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_1 = NULL, *_2 = NULL;
	zend_bool recursive;
	zval *path_param = NULL, *recursive_param = NULL, *_0 = NULL;
	zval *path = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &path_param, &recursive_param);

	if (unlikely(Z_TYPE_P(path_param) != IS_STRING && Z_TYPE_P(path_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'path' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(path_param) == IS_STRING)) {
		zephir_get_strval(path, path_param);
	} else {
		ZEPHIR_INIT_VAR(path);
		ZVAL_EMPTY_STRING(path);
	}
	if (!recursive_param) {
		recursive = 1;
	} else {
		recursive = zephir_get_boolval(recursive_param);
	}


	ZEPHIR_CALL_FUNCTION(&_0, "is_file", &_1, path);
	zephir_check_call_status();
	if (zephir_is_true(_0)) {
		ZEPHIR_RETURN_CALL_FUNCTION("unlink", &_2, path);
		zephir_check_call_status();
		RETURN_MM();
	}
	ZEPHIR_RETURN_CALL_SELF("rmdir", NULL, path, (recursive ? ZEPHIR_GLOBAL(global_true) : ZEPHIR_GLOBAL(global_false)));
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Copies a file or directory to another location
 *
 * @param string path
 * @param string dest
 * @return boolean
 */
PHP_METHOD(Util_Filesystem, copy) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_0 = NULL;
	zval *path_param = NULL, *dest_param = NULL;
	zval *path = NULL, *dest = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 0, &path_param, &dest_param);

	if (unlikely(Z_TYPE_P(path_param) != IS_STRING && Z_TYPE_P(path_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'path' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(path_param) == IS_STRING)) {
		zephir_get_strval(path, path_param);
	} else {
		ZEPHIR_INIT_VAR(path);
		ZVAL_EMPTY_STRING(path);
	}
	if (unlikely(Z_TYPE_P(dest_param) != IS_STRING && Z_TYPE_P(dest_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'dest' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(dest_param) == IS_STRING)) {
		zephir_get_strval(dest, dest_param);
	} else {
		ZEPHIR_INIT_VAR(dest);
		ZVAL_EMPTY_STRING(dest);
	}


	ZEPHIR_RETURN_CALL_FUNCTION("copy", &_0, path, dest);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Moves a file or directory to another location
 *
 * @param string path
 * @param string dest
 * @return boolean
 */
PHP_METHOD(Util_Filesystem, move) {

	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_0 = NULL;
	zval *path_param = NULL, *dest_param = NULL;
	zval *path = NULL, *dest = NULL;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 0, &path_param, &dest_param);

	if (unlikely(Z_TYPE_P(path_param) != IS_STRING && Z_TYPE_P(path_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'path' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(path_param) == IS_STRING)) {
		zephir_get_strval(path, path_param);
	} else {
		ZEPHIR_INIT_VAR(path);
		ZVAL_EMPTY_STRING(path);
	}
	if (unlikely(Z_TYPE_P(dest_param) != IS_STRING && Z_TYPE_P(dest_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'dest' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(dest_param) == IS_STRING)) {
		zephir_get_strval(dest, dest_param);
	} else {
		ZEPHIR_INIT_VAR(dest);
		ZVAL_EMPTY_STRING(dest);
	}


	ZEPHIR_RETURN_CALL_FUNCTION("rename", &_0, path, dest);
	zephir_check_call_status();
	RETURN_MM();

}

/**
 * Lists files in a directory, optionally recursively.
 *
 * @param string dir Directory path
 * @param int depth Directory levels deep to recurse. Default 0.
 * @param array exclude Filenames to exclude. Default [".DS_Store"]
 * @return array Indexed array of absolute filepaths
 */
PHP_METHOD(Util_Filesystem, listFiles) {

	zend_bool _14;
	zephir_fcall_cache_entry *_12 = NULL;
	HashTable *_8;
	HashPosition _7;
	zephir_nts_static zephir_fcall_cache_entry *_1 = NULL, *_6 = NULL, *_15 = NULL;
	zval *exclude = NULL;
	int depth, ZEPHIR_LAST_CALL_STATUS;
	zval *dir_param = NULL, *depth_param = NULL, *exclude_param = NULL, *files = NULL, *item = NULL, *_0 = NULL, *_2, _4 = zval_used_for_init, *_5 = NULL, **_9, *_10 = NULL, *_11 = NULL, *_13 = NULL;
	zval *dir = NULL, *_3;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 2, &dir_param, &depth_param, &exclude_param);

	if (unlikely(Z_TYPE_P(dir_param) != IS_STRING && Z_TYPE_P(dir_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'dir' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(dir_param) == IS_STRING)) {
		zephir_get_strval(dir, dir_param);
	} else {
		ZEPHIR_INIT_VAR(dir);
		ZVAL_EMPTY_STRING(dir);
	}
	if (!depth_param) {
		depth = 0;
	} else {
		depth = zephir_get_intval(depth_param);
	}
	if (!exclude_param) {
		ZEPHIR_INIT_VAR(exclude);
		array_init(exclude);
	} else {
	exclude = exclude_param;

	}


	ZEPHIR_INIT_VAR(files);
	array_init(files);
	ZEPHIR_CALL_FUNCTION(&_0, "realpath", &_1, dir);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(_2);
	ZEPHIR_CONCAT_VS(_2, _0, "/");
	zephir_get_strval(dir, _2);
	ZEPHIR_INIT_VAR(_3);
	ZEPHIR_CONCAT_VS(_3, dir, "*");
	ZEPHIR_SINIT_VAR(_4);
	ZVAL_LONG(&_4, (8 | 8192));
	ZEPHIR_CALL_FUNCTION(&_5, "glob", &_6, _3, &_4);
	zephir_check_call_status();
	zephir_is_iterable(_5, &_8, &_7, 0, 0, "util/filesystem.zep", 127);
	for (
	  ; zephir_hash_get_current_data_ex(_8, (void**) &_9, &_7) == SUCCESS
	  ; zephir_hash_move_forward_ex(_8, &_7)
	) {
		ZEPHIR_GET_HVALUE(item, _9);
		ZEPHIR_SINIT_NVAR(_4);
		ZVAL_STRING(&_4, "/", 0);
		if (zephir_end_with(item, &_4, NULL)) {
			if (depth) {
				ZEPHIR_INIT_NVAR(_10);
				ZEPHIR_INIT_NVAR(_13);
				ZVAL_LONG(_13, ((depth - 1)));
				ZEPHIR_CALL_SELF(&_11, "listfiles", &_12, item, _13, exclude);
				zephir_check_call_status();
				zephir_fast_array_merge(_10, &(files), &(_11) TSRMLS_CC);
				ZEPHIR_CPY_WRT(files, _10);
			}
		} else {
			_14 = ZEPHIR_IS_EMPTY(exclude);
			if (!(_14)) {
				ZEPHIR_INIT_NVAR(_10);
				zephir_basename(_10, item TSRMLS_CC);
				ZEPHIR_CALL_FUNCTION(&_11, "in_array", &_15, _10, exclude, ZEPHIR_GLOBAL(global_true));
				zephir_check_call_status();
				_14 = !zephir_is_true(_11);
			}
			if (_14) {
				zephir_array_append(&files, item, PH_SEPARATE, "util/filesystem.zep", 122);
			}
		}
	}
	RETURN_CCTOR(files);

}

/**
 * Lists (sub)directories in a directory.
 *
 * @param string dir Directory path
 * @param array exclude Dirnames to exclude. Default [".settings", ".git"]
 * @return array Indexed array of absolute directory paths
 */
PHP_METHOD(Util_Filesystem, listDirs) {

	zend_bool _10;
	HashTable *_8;
	HashPosition _7;
	int ZEPHIR_LAST_CALL_STATUS;
	zephir_nts_static zephir_fcall_cache_entry *_1 = NULL, *_6 = NULL, *_13 = NULL;
	zval *exclude = NULL;
	zval *dir_param = NULL, *exclude_param = NULL, *dirs, *item = NULL, *_0 = NULL, *_2, _4, *_5 = NULL, **_9, *_11 = NULL, *_12 = NULL, *_14 = NULL;
	zval *dir = NULL, *_3;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 1, 1, &dir_param, &exclude_param);

	if (unlikely(Z_TYPE_P(dir_param) != IS_STRING && Z_TYPE_P(dir_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'dir' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(dir_param) == IS_STRING)) {
		zephir_get_strval(dir, dir_param);
	} else {
		ZEPHIR_INIT_VAR(dir);
		ZVAL_EMPTY_STRING(dir);
	}
	if (!exclude_param) {
		ZEPHIR_INIT_VAR(exclude);
		array_init(exclude);
	} else {
	exclude = exclude_param;

	}


	ZEPHIR_INIT_VAR(dirs);
	array_init(dirs);
	ZEPHIR_CALL_FUNCTION(&_0, "realpath", &_1, dir);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(_2);
	ZEPHIR_CONCAT_VS(_2, _0, "/");
	zephir_get_strval(dir, _2);
	ZEPHIR_INIT_VAR(_3);
	ZEPHIR_CONCAT_VS(_3, dir, "*");
	ZEPHIR_SINIT_VAR(_4);
	ZVAL_LONG(&_4, (8192 | 1073741824));
	ZEPHIR_CALL_FUNCTION(&_5, "glob", &_6, _3, &_4);
	zephir_check_call_status();
	zephir_is_iterable(_5, &_8, &_7, 0, 0, "util/filesystem.zep", 151);
	for (
	  ; zephir_hash_get_current_data_ex(_8, (void**) &_9, &_7) == SUCCESS
	  ; zephir_hash_move_forward_ex(_8, &_7)
	) {
		ZEPHIR_GET_HVALUE(item, _9);
		_10 = ZEPHIR_IS_EMPTY(exclude);
		if (!(_10)) {
			ZEPHIR_INIT_NVAR(_11);
			zephir_basename(_11, item TSRMLS_CC);
			ZEPHIR_CALL_FUNCTION(&_12, "in_array", &_13, _11, exclude, ZEPHIR_GLOBAL(global_true));
			zephir_check_call_status();
			_10 = !zephir_is_true(_12);
		}
		if (_10) {
			ZEPHIR_INIT_LNVAR(_14);
			ZEPHIR_CONCAT_VS(_14, item, "/");
			zephir_array_append(&dirs, _14, PH_SEPARATE, "util/filesystem.zep", 147);
		}
	}
	RETURN_CCTOR(dirs);

}

/**
 * Lists files in a directory, optionally recursively.
 *
 * @param string dir Directory path
 * @param int depth Directory levels deep to recurse. Default 0.
 * @param array exclude Filenames to exclude. Default [".DS_Store"]
 * @return array Indexed array of absolute filepaths
 */
PHP_METHOD(Util_Filesystem, listFilesMatch) {

	zephir_fcall_cache_entry *_14 = NULL;
	HashTable *_8;
	HashPosition _7;
	zephir_nts_static zephir_fcall_cache_entry *_1 = NULL, *_6 = NULL, *_11 = NULL;
	int depth, ZEPHIR_LAST_CALL_STATUS;
	zval *dir_param = NULL, *pattern_param = NULL, *depth_param = NULL, *files = NULL, *item = NULL, *_0 = NULL, *_2, _4 = zval_used_for_init, *_5 = NULL, **_9, *_10 = NULL, *_12 = NULL, *_13 = NULL, *_15 = NULL;
	zval *dir = NULL, *pattern = NULL, *_3;

	ZEPHIR_MM_GROW();
	zephir_fetch_params(1, 2, 1, &dir_param, &pattern_param, &depth_param);

	if (unlikely(Z_TYPE_P(dir_param) != IS_STRING && Z_TYPE_P(dir_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'dir' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(dir_param) == IS_STRING)) {
		zephir_get_strval(dir, dir_param);
	} else {
		ZEPHIR_INIT_VAR(dir);
		ZVAL_EMPTY_STRING(dir);
	}
	if (unlikely(Z_TYPE_P(pattern_param) != IS_STRING && Z_TYPE_P(pattern_param) != IS_NULL)) {
		zephir_throw_exception_string(spl_ce_InvalidArgumentException, SL("Parameter 'pattern' must be a string") TSRMLS_CC);
		RETURN_MM_NULL();
	}

	if (likely(Z_TYPE_P(pattern_param) == IS_STRING)) {
		zephir_get_strval(pattern, pattern_param);
	} else {
		ZEPHIR_INIT_VAR(pattern);
		ZVAL_EMPTY_STRING(pattern);
	}
	if (!depth_param) {
		depth = 0;
	} else {
		depth = zephir_get_intval(depth_param);
	}


	ZEPHIR_INIT_VAR(files);
	array_init(files);
	ZEPHIR_CALL_FUNCTION(&_0, "realpath", &_1, dir);
	zephir_check_call_status();
	ZEPHIR_INIT_VAR(_2);
	ZEPHIR_CONCAT_VS(_2, _0, "/");
	zephir_get_strval(dir, _2);
	ZEPHIR_INIT_VAR(_3);
	ZEPHIR_CONCAT_VS(_3, dir, "*");
	ZEPHIR_SINIT_VAR(_4);
	ZVAL_LONG(&_4, (8 | 8192));
	ZEPHIR_CALL_FUNCTION(&_5, "glob", &_6, _3, &_4);
	zephir_check_call_status();
	zephir_is_iterable(_5, &_8, &_7, 0, 0, "util/filesystem.zep", 182);
	for (
	  ; zephir_hash_get_current_data_ex(_8, (void**) &_9, &_7) == SUCCESS
	  ; zephir_hash_move_forward_ex(_8, &_7)
	) {
		ZEPHIR_GET_HVALUE(item, _9);
		ZEPHIR_SINIT_NVAR(_4);
		ZVAL_STRING(&_4, "/", 0);
		ZEPHIR_CALL_FUNCTION(&_10, "fnmatch", &_11, pattern, item);
		zephir_check_call_status();
		if (zephir_end_with(item, &_4, NULL)) {
			if (depth) {
				ZEPHIR_INIT_NVAR(_12);
				ZEPHIR_INIT_NVAR(_15);
				ZVAL_LONG(_15, ((depth - 1)));
				ZEPHIR_CALL_SELF(&_13, "listfilesmatch", &_14, item, pattern, _15);
				zephir_check_call_status();
				zephir_fast_array_merge(_12, &(files), &(_13) TSRMLS_CC);
				ZEPHIR_CPY_WRT(files, _12);
			}
		} else if (zephir_is_true(_10)) {
			zephir_array_append(&files, item, PH_SEPARATE, "util/filesystem.zep", 178);
		}
	}
	RETURN_CCTOR(files);

}

