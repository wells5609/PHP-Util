
extern zend_class_entry *util_classloader_ce;

ZEPHIR_INIT_CLASS(Util_ClassLoader);

PHP_METHOD(Util_ClassLoader, composerInit);
PHP_METHOD(Util_ClassLoader, getPrefixes);
PHP_METHOD(Util_ClassLoader, getPrefixesPsr4);
PHP_METHOD(Util_ClassLoader, getFallbackDirs);
PHP_METHOD(Util_ClassLoader, getFallbackDirsPsr4);
PHP_METHOD(Util_ClassLoader, getClassMap);
PHP_METHOD(Util_ClassLoader, addClassMap);
PHP_METHOD(Util_ClassLoader, add);
PHP_METHOD(Util_ClassLoader, addPsr4);
PHP_METHOD(Util_ClassLoader, set);
PHP_METHOD(Util_ClassLoader, setPsr4);
PHP_METHOD(Util_ClassLoader, setUseIncludePath);
PHP_METHOD(Util_ClassLoader, getUseIncludePath);
PHP_METHOD(Util_ClassLoader, register);
PHP_METHOD(Util_ClassLoader, unregister);
PHP_METHOD(Util_ClassLoader, loadClass);
PHP_METHOD(Util_ClassLoader, findFile);
PHP_METHOD(Util_ClassLoader, findFileWithExtension);
PHP_METHOD(Util_ClassLoader, __construct);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_composerinit, 0, 0, 1)
	ZEND_ARG_INFO(0, vendorPath)
	ZEND_ARG_INFO(0, prepend)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_addclassmap, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0, classMap, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_add, 0, 0, 2)
	ZEND_ARG_INFO(0, prefix)
	ZEND_ARG_INFO(0, paths)
	ZEND_ARG_INFO(0, prepend)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_addpsr4, 0, 0, 2)
	ZEND_ARG_INFO(0, prefix)
	ZEND_ARG_INFO(0, paths)
	ZEND_ARG_INFO(0, prepend)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_set, 0, 0, 2)
	ZEND_ARG_INFO(0, prefix)
	ZEND_ARG_INFO(0, paths)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_setpsr4, 0, 0, 2)
	ZEND_ARG_INFO(0, prefix)
	ZEND_ARG_INFO(0, paths)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_setuseincludepath, 0, 0, 1)
	ZEND_ARG_INFO(0, useIncludePath)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_register, 0, 0, 0)
	ZEND_ARG_INFO(0, prepend)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_loadclass, 0, 0, 1)
	ZEND_ARG_INFO(0, className)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_findfile, 0, 0, 1)
	ZEND_ARG_INFO(0, className)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_classloader_findfilewithextension, 0, 0, 2)
	ZEND_ARG_INFO(0, className)
	ZEND_ARG_INFO(0, ext)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_classloader_method_entry) {
	PHP_ME(Util_ClassLoader, composerInit, arginfo_util_classloader_composerinit, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_ClassLoader, getPrefixes, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, getPrefixesPsr4, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, getFallbackDirs, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, getFallbackDirsPsr4, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, getClassMap, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, addClassMap, arginfo_util_classloader_addclassmap, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, add, arginfo_util_classloader_add, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, addPsr4, arginfo_util_classloader_addpsr4, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, set, arginfo_util_classloader_set, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, setPsr4, arginfo_util_classloader_setpsr4, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, setUseIncludePath, arginfo_util_classloader_setuseincludepath, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, getUseIncludePath, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, register, arginfo_util_classloader_register, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, unregister, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, loadClass, arginfo_util_classloader_loadclass, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, findFile, arginfo_util_classloader_findfile, ZEND_ACC_PUBLIC)
	PHP_ME(Util_ClassLoader, findFileWithExtension, arginfo_util_classloader_findfilewithextension, ZEND_ACC_PROTECTED)
	PHP_ME(Util_ClassLoader, __construct, NULL, ZEND_ACC_PUBLIC|ZEND_ACC_CTOR)
  PHP_FE_END
};
