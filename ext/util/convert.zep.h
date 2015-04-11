
extern zend_class_entry *util_convert_ce;

ZEPHIR_INIT_CLASS(Util_Convert);

PHP_METHOD(Util_Convert, toSeconds);
PHP_METHOD(Util_Convert, temp);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_convert_toseconds, 0, 0, 1)
	ZEND_ARG_INFO(0, arg)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_convert_temp, 0, 0, 3)
	ZEND_ARG_INFO(0, quantity)
	ZEND_ARG_INFO(0, from)
	ZEND_ARG_INFO(0, to)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_convert_method_entry) {
	PHP_ME(Util_Convert, toSeconds, arginfo_util_convert_toseconds, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Convert, temp, arginfo_util_convert_temp, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
