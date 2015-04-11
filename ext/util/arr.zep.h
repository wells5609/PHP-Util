
extern zend_class_entry *util_arr_ce;

ZEPHIR_INIT_CLASS(Util_Arr);

PHP_METHOD(Util_Arr, mergev);
PHP_METHOD(Util_Arr, select);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_arr_mergev, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0, arrays, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_arr_select, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0, arr, 0)
	ZEND_ARG_ARRAY_INFO(0, conditions, 0)
	ZEND_ARG_INFO(0, operator)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_arr_method_entry) {
	PHP_ME(Util_Arr, mergev, arginfo_util_arr_mergev, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Arr, select, arginfo_util_arr_select, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
