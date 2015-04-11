
/* This file was generated automatically by Zephir do not modify it! */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include <php.h>

#if PHP_VERSION_ID < 50500
#include <locale.h>
#endif

#include "php_ext.h"
#include "util.h"

#include <ext/standard/info.h>

#include <Zend/zend_operators.h>
#include <Zend/zend_exceptions.h>
#include <Zend/zend_interfaces.h>

#include "kernel/main.h"
#include "kernel/fcall.h"
#include "kernel/memory.h"



zend_class_entry *util_arr_ce;
zend_class_entry *util_callback_ce;
zend_class_entry *util_classloader_ce;
zend_class_entry *util_convert_ce;
zend_class_entry *util_decode_ce;
zend_class_entry *util_filesystem_ce;
zend_class_entry *util_sanitize_ce;
zend_class_entry *util_str_ce;
zend_class_entry *util_typecast_ce;
zend_class_entry *util_xml_ce;

ZEND_DECLARE_MODULE_GLOBALS(util)

static PHP_MINIT_FUNCTION(util)
{
#if PHP_VERSION_ID < 50500
	char* old_lc_all = setlocale(LC_ALL, NULL);
	if (old_lc_all) {
		size_t len = strlen(old_lc_all);
		char *tmp  = calloc(len+1, 1);
		if (UNEXPECTED(!tmp)) {
			return FAILURE;
		}

		memcpy(tmp, old_lc_all, len);
		old_lc_all = tmp;
	}

	setlocale(LC_ALL, "C");
#endif

	ZEPHIR_INIT(Util_Arr);
	ZEPHIR_INIT(Util_Callback);
	ZEPHIR_INIT(Util_ClassLoader);
	ZEPHIR_INIT(Util_Convert);
	ZEPHIR_INIT(Util_Decode);
	ZEPHIR_INIT(Util_Filesystem);
	ZEPHIR_INIT(Util_Sanitize);
	ZEPHIR_INIT(Util_Str);
	ZEPHIR_INIT(Util_Typecast);
	ZEPHIR_INIT(Util_Xml);

#if PHP_VERSION_ID < 50500
	setlocale(LC_ALL, old_lc_all);
	free(old_lc_all);
#endif
	return SUCCESS;
}

#ifndef ZEPHIR_RELEASE
static PHP_MSHUTDOWN_FUNCTION(util)
{

	zephir_deinitialize_memory(TSRMLS_C);

	return SUCCESS;
}
#endif

/**
 * Initialize globals on each request or each thread started
 */
static void php_zephir_init_globals(zend_util_globals *zephir_globals TSRMLS_DC)
{
	zephir_globals->initialized = 0;

	/* Memory options */
	zephir_globals->active_memory = NULL;

	/* Virtual Symbol Tables */
	zephir_globals->active_symbol_table = NULL;

	/* Cache Enabled */
	zephir_globals->cache_enabled = 1;

	/* Recursive Lock */
	zephir_globals->recursive_lock = 0;


}

static PHP_RINIT_FUNCTION(util)
{

	zend_util_globals *zephir_globals_ptr = ZEPHIR_VGLOBAL;

	php_zephir_init_globals(zephir_globals_ptr TSRMLS_CC);
	//zephir_init_interned_strings(TSRMLS_C);

	zephir_initialize_memory(zephir_globals_ptr TSRMLS_CC);

	return SUCCESS;
}

static PHP_RSHUTDOWN_FUNCTION(util)
{

	

	zephir_deinitialize_memory(TSRMLS_C);
	return SUCCESS;
}

static PHP_MINFO_FUNCTION(util)
{
	php_info_print_box_start(0);
	php_printf("%s", PHP_UTIL_DESCRIPTION);
	php_info_print_box_end();

	php_info_print_table_start();
	php_info_print_table_header(2, PHP_UTIL_NAME, "enabled");
	php_info_print_table_row(2, "Author", PHP_UTIL_AUTHOR);
	php_info_print_table_row(2, "Version", PHP_UTIL_VERSION);
	php_info_print_table_row(2, "Powered by Zephir", "Version " PHP_UTIL_ZEPVERSION);
	php_info_print_table_end();


}

static PHP_GINIT_FUNCTION(util)
{
	php_zephir_init_globals(util_globals TSRMLS_CC);
}

static PHP_GSHUTDOWN_FUNCTION(util)
{

}


zend_function_entry php_util_functions[] = {
ZEND_FE_END

};

zend_module_entry util_module_entry = {
	STANDARD_MODULE_HEADER_EX,
	NULL,
	NULL,
	PHP_UTIL_EXTNAME,
	php_util_functions,
	PHP_MINIT(util),
#ifndef ZEPHIR_RELEASE
	PHP_MSHUTDOWN(util),
#else
	NULL,
#endif
	PHP_RINIT(util),
	PHP_RSHUTDOWN(util),
	PHP_MINFO(util),
	PHP_UTIL_VERSION,
	ZEND_MODULE_GLOBALS(util),
	PHP_GINIT(util),
	PHP_GSHUTDOWN(util),
	NULL,
	STANDARD_MODULE_PROPERTIES_EX
};

#ifdef COMPILE_DL_UTIL
ZEND_GET_MODULE(util)
#endif
