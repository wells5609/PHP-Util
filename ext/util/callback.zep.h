
extern zend_class_entry *util_callback_ce;

ZEPHIR_INIT_CLASS(Util_Callback);

PHP_METHOD(Util_Callback, result);
PHP_METHOD(Util_Callback, id);
PHP_METHOD(Util_Callback, invoke);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_callback_result, 0, 0, 1)
	ZEND_ARG_INFO(0, arg)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_callback_id, 0, 0, 1)
	ZEND_ARG_INFO(0, callback)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_callback_invoke, 0, 0, 1)
	ZEND_ARG_INFO(0, callback)
	ZEND_ARG_ARRAY_INFO(0, args, 1)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_callback_method_entry) {
	PHP_ME(Util_Callback, result, arginfo_util_callback_result, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Callback, id, arginfo_util_callback_id, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Callback, invoke, arginfo_util_callback_invoke, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
