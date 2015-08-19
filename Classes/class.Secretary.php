<?php
/*
 *  Our secretary, Kirkston; a tiny class to handle common file related functions
 */
class Secretary {

    /**
     * Delete a directory and all files within it (http://php.net/manual/en/function.rmdir.php#92050)
     * @param  string $dir The filepath of the directory to delete
     * @return boolean  Whether the file has deleted
     */
    public static function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!Secretary::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    /**
     * Recursive Glob (http://stackoverflow.com/questions/17160696/php-glob-scan-in-subfolders-for-a-file/17161106#17161106)
     * @param  string $pattern     Pattern to match against
     * @param  integer [$flags = 0] Any flags to match against
     * @return array An array of filepaths that match against $pattern
     */
    public static function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, Secretary::rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

    /**
     * Parse a JSON file and, if specified, merge it with an existing array (useful for chowing down config.json's)
     * @param  string $filePath              The filepath of the JSON file to decode
     * @param  array [$mergeWith = array()]  An array to merge the results with
     * @return array The Parsed Json file
     */
    public static function getJSON ($filePath, $mergeWith = array()) {
        if (file_exists($filePath)) {
            $configJSON = file_get_contents($filePath);
            $configDecode = json_decode($configJSON, true);
            return array_merge($mergeWith, $configDecode);
        }
        else {
            return array();
        }
    }

    /**
     * Return configuration file by loading Classes/config.defaults.json and then config.json
     */
    public static function initDefaults () {
        $defaults = array();
        if (file_exists("Classes/config.defaults.json")) {
            CL::printDebug("Loading default Warehouse configuration", 0, Colour::Green);
            $defaults = self::getJSON("Classes/config.defaults.json");
        }
        else {
            CL::printDebug("File: Classes/config.defaults.json was not found.", 0, Colour::Red);
            CL::printDebug("Warehouse may not function correctly without this file.", 0, Colour::Red);
            CL::printDebug("Please re-download from Github.", 0, Colour::Red);
        }

        if (file_exists("config.json")) {
            CL::printDebug("Loading config file.", 0, Colour::Green);
            $defaults = self::getJSON("config.json", $defaults);
        }
        else {
            CL::printDebug("No custom config file found. #BareBones");
        }
        return $defaults;
    }
}

?>
