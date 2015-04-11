namespace Util;

class Filesystem
{

    /**
     * Scans a directory for files and directories, optionally skipping "." and ".."
     *
     * @param string! dir Directory path
     * @param boolean skip_dots Skip "." and ".." (default true)
     * @return array Files in the directory
     */
    public static function scandir(string! dir, boolean skip_dots = true) -> array
    {
        var contents;
        let contents = scandir(dir);

        if skip_dots {

            if ! empty contents {
                array_shift(contents);
                array_shift(contents);
            }
        }

        return contents;
    }

    /**
     * Removes a directory, optionally recursively.
     *
     * @param string! dir Directory path
     * @param boolean recursive Whether to recurse directories. Default true
     * @return boolean True on success or false on failure
     */
	public static function rmdir(string! dir, boolean recursive = true) -> boolean
    {
        var path, item;
        let path = realpath(dir);

		if ! path {
			return true;
		}

		if recursive {

			for item in Filesystem::scandir(path, true) {
				Filesystem::delete(path . DIRECTORY_SEPARATOR . item, true);
			}
		}

		return rmdir(path);
	}

    /**
     * Deletes a file or directory, optionally recursively.
     *
     * @param string path File or directory path
     * @param boolean recursive Whether to recurse directories. Default true
     * @return boolean True on success, or false if failure.
     */
    public static function delete(string! path, boolean recursive = true) -> boolean
    {

        if is_file(path) {
            return unlink(path);
        }

        return Filesystem::rmdir(path, recursive);
    }

    /**
     * Copies a file or directory to another location
     *
     * @param string path
     * @param string dest
     * @return boolean
     */
    public static function copy(string! path, string! dest) -> boolean
    {
        return copy(path, dest);
    }

    /**
     * Moves a file or directory to another location
     *
     * @param string path
     * @param string dest
     * @return boolean
     */
    public static function move(string! path, string! dest) -> boolean
    {
        return rename(path, dest);
    }

    /**
     * Lists files in a directory, optionally recursively.
     *
     * @param string dir Directory path
     * @param int depth Directory levels deep to recurse. Default 0.
     * @param array exclude Filenames to exclude. Default [".DS_Store"]
     * @return array Indexed array of absolute filepaths
     */
    public static function listFiles(string! dir, int depth = 0, array! exclude = [".DS_Store"]) -> array
    {
        var files, item;

        let files = [];
		let dir = realpath(dir).DIRECTORY_SEPARATOR;

        for item in glob(dir."*", GLOB_MARK|GLOB_NOESCAPE) {

            if ends_with(item, DIRECTORY_SEPARATOR) {

                if depth {
                    let files = array_merge(files, Filesystem::listFiles(item, (depth - 1), exclude));
                }

            } else {

				if empty exclude || ! in_array(basename(item), exclude, true) {
					let files[] = item;
				}
            }
        }

		return files;
	}

    /**
     * Lists (sub)directories in a directory.
     *
     * @param string dir Directory path
     * @param array exclude Dirnames to exclude. Default [".settings", ".git"]
     * @return array Indexed array of absolute directory paths
     */
    public static function listDirs(string! dir, array! exclude = [".settings", ".git"]) -> array
    {
        var dirs, item;

        let dirs = [];
		let dir = realpath(dir).DIRECTORY_SEPARATOR;

        for item in glob(dir."*", GLOB_NOESCAPE|GLOB_ONLYDIR) {

            if empty exclude || ! in_array(basename(item), exclude, true) {
                let dirs[] = item.DIRECTORY_SEPARATOR;
            }
        }

		return dirs;
	}

    /**
     * Lists files in a directory, optionally recursively.
     *
     * @param string dir Directory path
     * @param int depth Directory levels deep to recurse. Default 0.
     * @param array exclude Filenames to exclude. Default [".DS_Store"]
     * @return array Indexed array of absolute filepaths
     */
    public static function listFilesMatch(string! dir, string! pattern, int depth = 0) -> array
    {
        var files, item;

        let files = [];
		let dir = realpath(dir).DIRECTORY_SEPARATOR;

        for item in glob(dir."*", GLOB_MARK|GLOB_NOESCAPE) {

            if ends_with(item, DIRECTORY_SEPARATOR) {

                if depth {
                    let files = array_merge(files, Filesystem::listFilesMatch(item, pattern, (depth - 1)));
                }

            } elseif fnmatch(pattern, item) {
				let files[] = item;
			}
        }

		return files;
	}

}
