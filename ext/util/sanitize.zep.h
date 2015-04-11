
extern zend_class_entry *util_sanitize_ce;

ZEPHIR_INIT_CLASS(Util_Sanitize);

PHP_METHOD(Util_Sanitize, str);
PHP_METHOD(Util_Sanitize, numInt);
PHP_METHOD(Util_Sanitize, numFloat);
PHP_METHOD(Util_Sanitize, ascii);
PHP_METHOD(Util_Sanitize, url);
PHP_METHOD(Util_Sanitize, alpha);
PHP_METHOD(Util_Sanitize, alnum);
PHP_METHOD(Util_Sanitize, unicode);
PHP_METHOD(Util_Sanitize, slug);
PHP_METHOD(Util_Sanitize, stripQuotes);
PHP_METHOD(Util_Sanitize, stripControl);
PHP_METHOD(Util_Sanitize, sqlLike);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_str, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
	ZEND_ARG_INFO(0, flags)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_numint, 0, 0, 1)
	ZEND_ARG_INFO(0, value)
	ZEND_ARG_INFO(0, flags)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_numfloat, 0, 0, 1)
	ZEND_ARG_INFO(0, value)
	ZEND_ARG_INFO(0, flags)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_ascii, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_url, 0, 0, 1)
	ZEND_ARG_INFO(0, url)
	ZEND_ARG_INFO(0, flags)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_alpha, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_alnum, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_unicode, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_slug, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
	ZEND_ARG_INFO(0, separator)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_stripquotes, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_stripcontrol, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_sanitize_sqllike, 0, 0, 1)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_sanitize_method_entry) {
	PHP_ME(Util_Sanitize, str, arginfo_util_sanitize_str, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, numInt, arginfo_util_sanitize_numint, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, numFloat, arginfo_util_sanitize_numfloat, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, ascii, arginfo_util_sanitize_ascii, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, url, arginfo_util_sanitize_url, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, alpha, arginfo_util_sanitize_alpha, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, alnum, arginfo_util_sanitize_alnum, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, unicode, arginfo_util_sanitize_unicode, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, slug, arginfo_util_sanitize_slug, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, stripQuotes, arginfo_util_sanitize_stripquotes, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, stripControl, arginfo_util_sanitize_stripcontrol, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Sanitize, sqlLike, arginfo_util_sanitize_sqllike, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
