
extern zend_class_entry *util_filesystem_ce;

ZEPHIR_INIT_CLASS(Util_Filesystem);

PHP_METHOD(Util_Filesystem, scandir);
PHP_METHOD(Util_Filesystem, rmdir);
PHP_METHOD(Util_Filesystem, delete);
PHP_METHOD(Util_Filesystem, copy);
PHP_METHOD(Util_Filesystem, move);
PHP_METHOD(Util_Filesystem, listFiles);
PHP_METHOD(Util_Filesystem, listDirs);
PHP_METHOD(Util_Filesystem, listFilesMatch);

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_filesystem_scandir, 0, 0, 1)
	ZEND_ARG_INFO(0, dir)
	ZEND_ARG_INFO(0, skip_dots)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_filesystem_rmdir, 0, 0, 1)
	ZEND_ARG_INFO(0, dir)
	ZEND_ARG_INFO(0, recursive)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_filesystem_delete, 0, 0, 1)
	ZEND_ARG_INFO(0, path)
	ZEND_ARG_INFO(0, recursive)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_filesystem_copy, 0, 0, 2)
	ZEND_ARG_INFO(0, path)
	ZEND_ARG_INFO(0, dest)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_filesystem_move, 0, 0, 2)
	ZEND_ARG_INFO(0, path)
	ZEND_ARG_INFO(0, dest)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_filesystem_listfiles, 0, 0, 1)
	ZEND_ARG_INFO(0, dir)
	ZEND_ARG_INFO(0, depth)
	ZEND_ARG_ARRAY_INFO(0, exclude, 1)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_filesystem_listdirs, 0, 0, 1)
	ZEND_ARG_INFO(0, dir)
	ZEND_ARG_ARRAY_INFO(0, exclude, 1)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_util_filesystem_listfilesmatch, 0, 0, 2)
	ZEND_ARG_INFO(0, dir)
	ZEND_ARG_INFO(0, pattern)
	ZEND_ARG_INFO(0, depth)
ZEND_END_ARG_INFO()

ZEPHIR_INIT_FUNCS(util_filesystem_method_entry) {
	PHP_ME(Util_Filesystem, scandir, arginfo_util_filesystem_scandir, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Filesystem, rmdir, arginfo_util_filesystem_rmdir, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Filesystem, delete, arginfo_util_filesystem_delete, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Filesystem, copy, arginfo_util_filesystem_copy, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Filesystem, move, arginfo_util_filesystem_move, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Filesystem, listFiles, arginfo_util_filesystem_listfiles, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Filesystem, listDirs, arginfo_util_filesystem_listdirs, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	PHP_ME(Util_Filesystem, listFilesMatch, arginfo_util_filesystem_listfilesmatch, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
  PHP_FE_END
};
