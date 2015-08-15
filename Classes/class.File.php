<?php

/*
 *   A markdown file to parse and prepare for export
 */
class File {

    var $meta = [];         // Meta information about the document, including '@' attributes at the header of the file
    var $path = "";         // The origional filepath
    var $contents = "";     // The file's raw contents
    var $pathQueue = [];    // A queue leading down to the filepath's content - used to get config.json's

    /**
     * Construct our file object from a markdown's file path
     * @param string $filepath The path to the file whom's content we should load
     */
    function __construct ($filepath) {
        $this->path = $filepath;
        $this->meta["slug"] = basename($filepath, ".md");
        $file = file_get_contents($filepath);

        /*
         *  We need to strip attributes from the head of the file first, e.g. author, timestamp, etc..
         *  We use $fetchingMeta as a flag to indicate whether we're still stripping. Cheeky.
         */
        $fetchingMeta = true;
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $file) as $line) {        // Break the file down line by line
            if ($fetchingMeta) {
                if (preg_match("/@([a-z|_|A-Z]+) (.+)/", $line, $matches)) {    // If we have an attribute...
                    $this->meta[$matches[1]] = $matches[2];                 //                  ... store it into $meta
                }
                else {
                    $fetchingMeta = false;
                    $this->contents .= $line . "\n";                               // If we don't have a match then we've reached the end of meta.
                }
            }
            else {
                $this->contents .= $line . "\n";    // If we're not fetchign meta then append the line to contents and add a newline incidcator
            }
        }

        /*
         * For each file we eventually want to recursively load any config.json's.
         * By storing a path queue which contains the directories leading to it, it makes it easier down the line.
         */
        $this->pathQueue = explode("/", dirname($filepath));

        /*
         *  It's handy to calculate the relative path to the root for loading stylesheets for deep rooted files
         */
        $level = "";
        foreach ($this->pathQueue as $path) {
            $level .= ($path == "." ? "./" : "../");
        }
        $this->meta["RelativeRoot"] = $level;
    }

    /**
     * Useful for debug
     * @return string A description of our file
     */
    public function __toString() {
        return $this->path;
    }

}

?>
