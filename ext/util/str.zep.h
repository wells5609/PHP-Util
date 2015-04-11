
extern zend_class_entry *util_str_ce;

ZEPHIR_INIT_CLASS(Util_Str);

PHP_METHOD(Util_Str, startsWith);
PHP_METHOD(Util_Str, endsWith);
PHP_METHOD(Util_Str, pearCase);
PHP_METHOD(Util_Str, snakeCase);
PHP_METHOD(Util_Str, studlyCase);
PHP_METHOD(Util_Str, camelCase);
PHP_METHOD(Util_Str, isJson);
PHP_METHOD(Util_Str, isXml);
PHP_METHOD(Util_Str, isSerialized);
PHP_METHOD(Util_Str, format);
PHP_METHOD(Util_Str, between);
PHP_METHOD(Util_Str, sentences);
PHP_METHOD(Util_Str, handleError);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_startswith, 0, 0, 2)
	ZEND_ARG_INFO(0, haystack)
	ZEND_ARG_INFO(0, needle)
	ZEND_ARG_INFO(0, case_sensitive)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_endswith, 0, 0, 2)
	ZEND_ARG_INFO(0, haystack)
	ZEND_ARG_INFO(0, needle)
	ZEND_ARG_INFO(0, case_sensitive)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_pearcase, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_snakecase, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_studlycase, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_camelcase, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_isjson, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_isxml, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_isserialized, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_format, 0, 0, 2)
	ZEND_ARG_INFO(0, str)
	ZEND_ARG_INFO(0, template)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_between, 0, 0, 3)
	ZEND_ARG_INFO(0, str)
	ZEND_ARG_INFO(0, substr_start)
	ZEND_ARG_INFO(0, substr_end)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_sentences, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
	ZEND_ARG_INFO(0, num)
	ZEND_ARG_INFO(0, strip_abbr)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_str_handleerror, 0, 0, 2)
	ZEND_ARG_INFO(0, errorNum)
	ZEND_ARG_INFO(0, errorMsg)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_str_method_entry) {
	PHP_ME(Util_Str, startsWith, arginfo_util_str_startswith, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, endsWith, arginfo_util_str_endswith, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, pearCase, arginfo_util_str_pearcase, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, snakeCase, arginfo_util_str_snakecase, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, studlyCase, arginfo_util_str_studlycase, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, camelCase, arginfo_util_str_camelcase, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, isJson, arginfo_util_str_isjson, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, isXml, arginfo_util_str_isxml, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, isSerialized, arginfo_util_str_isserialized, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, format, arginfo_util_str_format, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, between, arginfo_util_str_between, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, sentences, arginfo_util_str_sentences, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Str, handleError, arginfo_util_str_handleerror, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
