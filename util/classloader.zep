namespace Util;

use InvalidArgumentException;

/*
 * This file was part of Composer.
 *
 * --------------------------------
 * Zephir/C extension
 * --------------------------------
 * @author Wells Peterson <peterson.wells@gmail.com>
 *
 * This class can serve as a replacement for Composer's default class loader.
 *
 * Instead of:
 *      $loader = require_once "../path/to/vendor/autoload.php";
 * Use:
 *      $loader = Utils\ClassLoader::composerInit("../path/to/vendor");
 *
 * --------------------------------
 * PHP (Original)
 * --------------------------------
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ClassLoader implements a PSR-0 class loader
 *
 * See https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 *     $loader = new \Composer\Autoload\ClassLoader();
 *
 *     // register classes with namespaces
 *     $loader->add("Symfony\Component", __DIR__."/component");
 *     $loader->add("Symfony",           __DIR__."/framework");
 *
 *     // activate the autoloader
 *     $loader->register();
 *
 *     // to enable searching the include path (eg. for PEAR packages)
 *     $loader->setUseIncludePath(true);
 *
 * In this example, if you try to use a class in the Symfony\Component
 * namespace or one of its children (Symfony\Component\Console for instance),
 * the autoloader will first look for the class under the component/
 * directory, and it will then fallback to the framework/ directory if not
 * found before giving up.
 *
 * This class is loosely based on the Symfony UniversalClassLoader.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ClassLoader
{

    // PSR-4
    protected prefixLengthsPsr4 = [];
    protected prefixDirsPsr4 = [];
    protected fallbackDirsPsr4 = [];

    // PSR-0
    protected prefixesPsr0 = [];
    protected fallbackDirsPsr0 = [];

    // Classmap
    protected classMap = [];

    // Use include_path
    protected useIncludePath = false;

    public static function composerInit(const string! vendorPath, const boolean prepend = true) -> <ClassLoader>
    {
        var composerPath, loader, ns, path, namespaces, psr4, classMap, files;

        let composerPath = rtrim(vendorPath, "/\\") . DIRECTORY_SEPARATOR . "composer" . DIRECTORY_SEPARATOR;

        if ! is_dir(composerPath) {
            throw new InvalidArgumentException("Composer directory does not exist");
        }

        let loader = new ClassLoader();

        let namespaces = require composerPath . "autoload_namespaces.php";

        for ns, path in namespaces {
            loader->set(ns, path);
        }

        let psr4 = require composerPath . "autoload_psr4.php";

        for ns, path in psr4 {
            loader->setPsr4(ns, path);
        }

        let classMap = require composerPath . "autoload_classmap.php";

        if ! empty classMap {
            loader->addClassMap(classMap);
        }

        loader->register(prepend);

        if file_exists(composerPath."autoload_files.php") {

            let files = require composerPath."autoload_files.php";

            for path in files {
                require path;
            }
        }

        return loader;
    }

    public function getPrefixes() -> array
    {
        return call_user_func_array("array_merge", this->prefixesPsr0);
    }

    public function getPrefixesPsr4() -> array
    {
        return this->prefixDirsPsr4;
    }

    public function getFallbackDirs() -> array
    {
        return this->fallbackDirsPsr0;
    }

    public function getFallbackDirsPsr4() -> array
    {
        return this->fallbackDirsPsr4;
    }

    public function getClassMap() -> array
    {
        return this->classMap;
    }

    /**
     * @param array $classMap Class to filename map
     */
    public function addClassMap(const array! classMap) -> void
    {
        if (typeof this->classMap == "array") {
            let this->classMap = array_merge(this->classMap, classMap);
        } else {
            let this->classMap = classMap;
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix, either
     * appending or prepending to the ones previously set for this prefix.
     *
     * @param string       $prefix  The prefix
     * @param array|string $paths   The PSR-0 root directories
     * @param bool         $prepend Whether to prepend the directories
     */
    public function add(var prefix, var paths, boolean prepend = false) -> void
    {

        if (typeof paths == "string") {
            let paths = [paths];
        }

        if (typeof prefix != "string") {
            if prepend {
                let this->fallbackDirsPsr0 = array_merge(paths, this->fallbackDirsPsr0);
            } else {
                let this->fallbackDirsPsr0 = array_merge(this->fallbackDirsPsr0, paths);
            }

            return;
        }

        var firstChar;
        let firstChar = substr(prefix, 0, 1);

        if !isset this->prefixesPsr0[firstChar][prefix] {
            let this->prefixesPsr0[firstChar][prefix] = paths;

            return;
        }

        if prepend {
            let this->prefixesPsr0[firstChar][prefix] = array_merge(paths, this->prefixesPsr0[firstChar][prefix]);
        } else {
            let this->prefixesPsr0[firstChar][prefix] = array_merge(this->prefixesPsr0[firstChar][prefix], paths);
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace, either
     * appending or prepending to the ones previously set for this namespace.
     *
     * @param string       $prefix  The prefix/namespace, with trailing "\\"
     * @param array|string $paths   The PSR-0 base directories
     * @param bool         $prepend Whether to prepend the directories
     * @throws \InvalidArgumentException
     */
    public function addPsr4(var prefix, var paths, const boolean prepend = false) -> void
    {
        var firstChar;

        if typeof paths == "string" {
            let paths = [paths];
        }

        if typeof prefix != "string" {

            // Register directories for the root namespace.
            if prepend {
                let this->fallbackDirsPsr4 = array_merge(paths, this->fallbackDirsPsr4);
            } else {
                let this->fallbackDirsPsr4 = array_merge(this->fallbackDirsPsr4, paths);
            }

        } elseif ! isset this->prefixDirsPsr4[prefix] {

            // Register directories for a new namespace.
            let firstChar = substr(prefix, 0, 1);

            if ! ends_with(prefix, "\\") {
                throw new InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }

            let this->prefixLengthsPsr4[firstChar][prefix] = strlen(prefix);
            let this->prefixDirsPsr4[prefix] = paths;

        } elseif prepend {
            // Prepend directories for an already registered namespace.
            let this->prefixDirsPsr4[prefix] = array_merge(paths, this->prefixDirsPsr4[prefix]);

        } else {
            // Append directories for an already registered namespace.
            let this->prefixDirsPsr4[prefix] = array_merge(this->prefixDirsPsr4[prefix], paths);
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix,
     * replacing any others previously set for this prefix.
     *
     * @param string       $prefix The prefix
     * @param array|string $paths  The PSR-0 base directories
     */
    public function set(var prefix, var paths) -> void
    {

        if typeof paths == "string" {
            let paths = [paths];
        }

        if typeof prefix == "string" {
            let this->prefixesPsr0[substr(prefix, 0, 1)][prefix] = paths;
        } else {
            let this->fallbackDirsPsr0 = paths;
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace,
     * replacing any others previously set for this namespace.
     *
     * @param string       $prefix The prefix/namespace, with trailing "\\"
     * @param array|string $paths  The PSR-4 base directories
     *
     * @throws \InvalidArgumentException
     */
    public function setPsr4(var prefix, var paths) -> void
    {

        if typeof paths == "string" {
            let paths = [paths];
        }

        if typeof prefix == "null" {
            let this->fallbackDirsPsr4 = paths;
        } else {

            if ! ends_with(prefix, "\\") {
                throw new InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }

            let this->prefixLengthsPsr4[substr(prefix, 0, 1)][prefix] = strlen(prefix);
            let this->prefixDirsPsr4[prefix] = paths;
        }
    }

    /**
     * Turns on searching the include path for class files.
     *
     * @param bool $useIncludePath
     */
    public function setUseIncludePath(const boolean useIncludePath) -> void
    {
        let this->useIncludePath = useIncludePath;
    }

    /**
     * Can be used to check if the autoloader uses the include path to check
     * for classes.
     *
     * @return bool
     */
    public function getUseIncludePath() -> boolean
    {
        return this->useIncludePath;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     */
    public function register(const boolean prepend = false) -> void
    {
        spl_autoload_register([this, "loadClass"], true, prepend);
    }

    /**
     * Unregisters this instance as an autoloader.
     */
    public function unregister() -> void
    {
        spl_autoload_unregister([this, "loadClass"]);
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return bool|null True if loaded, null otherwise
     */
    public function loadClass(const string! className)
    {
        var file;
        let file = this->findFile(className);

        if typeof file == "string" {
            require file;
            return true;
        }
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|false The path if found, false otherwise
     */
    public function findFile(string! className) -> string|boolean
    {
        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        let className = ltrim(className, "\\");

        // class map lookup
        if isset this->classMap[className] {
            return this->classMap[className];
        }

        var file;
        let file = this->findFileWithExtension(className, ".php");

        if typeof file == "null" {

            // Search for Hack files if we are running on HHVM
            if defined("HHVM_VERSION") {

                let file = this->findFileWithExtension(className, ".hh");

                if typeof file == "string" {
                    return file;
                }
            }

            // Remember that this class does not exist.
            let this->classMap[className] = false;

            return false;
        }

        return file;
    }

    protected function findFileWithExtension(const string! className, const string ext) -> string|null
    {
        var logicalPathPsr4, firstChar, prefix, length, dir, file, logicalPathPsr0, nsPos, dirs;

        // PSR-4 lookup
        let logicalPathPsr4 = strtr(className, "\\", DIRECTORY_SEPARATOR) . ext;

        let firstChar = substr(className, 0, 1);

        if isset this->prefixLengthsPsr4[firstChar] {

            for prefix, length in this->prefixLengthsPsr4[firstChar] {

                if starts_with(className, prefix) {

                    for dir in this->prefixDirsPsr4[prefix] {

                        let file = dir . DIRECTORY_SEPARATOR . substr(logicalPathPsr4, length);

                        if file_exists(file) {
                            return file;
                        }
                    }
                }
            }
        }

        // PSR-4 fallback dirs
        for dir in this->fallbackDirsPsr4 {

            let file = dir . DIRECTORY_SEPARATOR . logicalPathPsr4;

            if file_exists(file) {
                return file;
            }
        }

        let nsPos = strrpos(className, "\\");

        // PSR-0 lookup
        if nsPos {
            // namespaced class name
            let logicalPathPsr0 = substr(logicalPathPsr4, 0, nsPos + 1)
                . strtr(substr(logicalPathPsr4, nsPos + 1), "_", DIRECTORY_SEPARATOR);

        } else {
            // PEAR-like class name
            let logicalPathPsr0 = strtr(className, "_", DIRECTORY_SEPARATOR) . ext;
        }

        if isset this->prefixesPsr0[firstChar] {

            for prefix, dirs in this->prefixesPsr0[firstChar] {

                if starts_with(className, prefix) {

                    for dir in dirs {

                        let file = dir . DIRECTORY_SEPARATOR . logicalPathPsr0;

                        if file_exists(file) {
                            return file;
                        }
                    }
                }
            }
        }

        // PSR-0 fallback dirs
        for dir in this->fallbackDirsPsr0 {

            let file = dir . DIRECTORY_SEPARATOR . logicalPathPsr0;

            if file_exists(file) {
                return file;
            }
        }

        // PSR-0 include paths.
        if this->useIncludePath {

            let file = stream_resolve_include_path(logicalPathPsr0);

            if file {
                return file;
            }
        }

        return null;
    }

}
