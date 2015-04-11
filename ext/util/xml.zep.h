
extern zend_class_entry *util_xml_ce;

ZEPHIR_INIT_CLASS(Util_Xml);

PHP_METHOD(Util_Xml, writeDocument);
PHP_METHOD(Util_Xml, writeElement);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_xml_writedocument, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0, data, 0)
	ZEND_ARG_INFO(0, root_tag)
	ZEND_ARG_INFO(0, version)
	ZEND_ARG_INFO(0, encoding)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_xml_writeelement, 0, 0, 2)
	ZEND_ARG_OBJ_INFO(0, writer, XMLWriter, 0)
	ZEND_ARG_ARRAY_INFO(0, data, 0)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_xml_method_entry) {
	PHP_ME(Util_Xml, writeDocument, arginfo_util_xml_writedocument, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Xml, writeElement, arginfo_util_xml_writeelement, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
