
extern zend_class_entry *util_decode_ce;

ZEPHIR_INIT_CLASS(Util_Decode);

PHP_METHOD(Util_Decode, json);
PHP_METHOD(Util_Decode, xml);
PHP_METHOD(Util_Decode, csv);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_decode_json, 0, 0, 1)
	ZEND_ARG_INFO(0, json)
	ZEND_ARG_INFO(0, assoc)
	ZEND_ARG_INFO(0, depth)
	ZEND_ARG_INFO(0, flags)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_decode_xml, 0, 0, 1)
	ZEND_ARG_INFO(0, xml)
	ZEND_ARG_INFO(0, assoc)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_decode_csv, 0, 0, 1)
	ZEND_ARG_INFO(0, csv)
	ZEND_ARG_INFO(0, assoc)
	ZEND_ARG_INFO(0, headers)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_decode_method_entry) {
	PHP_ME(Util_Decode, json, arginfo_util_decode_json, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Decode, xml, arginfo_util_decode_xml, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Decode, csv, arginfo_util_decode_csv, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
