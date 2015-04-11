
extern zend_class_entry *util_typecast_ce;

ZEPHIR_INIT_CLASS(Util_Typecast);

PHP_METHOD(Util_Typecast, toArray);
PHP_METHOD(Util_Typecast, toObject);
PHP_METHOD(Util_Typecast, toArrays);
PHP_METHOD(Util_Typecast, toObjects);
PHP_METHOD(Util_Typecast, toBool);
PHP_METHOD(Util_Typecast, toScalar);
PHP_METHOD(Util_Typecast, strnum);
PHP_METHOD(Util_Typecast, getDecimalPoint);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_typecast_toarray, 0, 0, 1)
	ZEND_ARG_INFO(0, arg)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_typecast_toobject, 0, 0, 1)
	ZEND_ARG_INFO(0, arg)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_typecast_toarrays, 0, 0, 1)
	ZEND_ARG_INFO(0, arg)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_typecast_toobjects, 0, 0, 1)
	ZEND_ARG_INFO(0, arg)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_typecast_tobool, 0, 0, 1)
	ZEND_ARG_INFO(0, arg)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_typecast_toscalar, 0, 0, 1)
	ZEND_ARG_INFO(0, arg)
	ZEND_ARG_INFO(0, flags)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_typecast_strnum, 0, 0, 1)
	ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_typecast_method_entry) {
	PHP_ME(Util_Typecast, toArray, arginfo_util_typecast_toarray, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Typecast, toObject, arginfo_util_typecast_toobject, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Typecast, toArrays, arginfo_util_typecast_toarrays, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Typecast, toObjects, arginfo_util_typecast_toobjects, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Typecast, toBool, arginfo_util_typecast_tobool, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Typecast, toScalar, arginfo_util_typecast_toscalar, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Typecast, strnum, arginfo_util_typecast_strnum, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Typecast, getDecimalPoint, NULL, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
