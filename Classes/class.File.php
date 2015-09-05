<?php

/*
 *   A markdown file to parse and prepare for export
 */
class File {

    var $contents = "";     // The file's raw contents
    var $path = "";         // The origional filepath
    var $pathQueue = [];    // A queue leading down to the filepath's content - used to get config.json's
    var $data = [];         // Data information of the document, including '@' attributes at the header of the file

    /**
     * Construct our file object from a markdown's file path
     * @param string $filepath The path to the file whom's content we should load
     */
    function __construct ($filepath) {
        $this->path = $filepath;
        $this->data["slug"] = basename($filepath, ".md");
        $file = file_get_contents($filepath);

        /*
         *  We need to strip attributes from the head of the file first, e.g. author, timestamp, etc..
         *  We use $fetchingdata as a flag to indicate whether we're still stripping. Cheeky.
         */
        $fetchingData = true;
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $file) as $line) {        // Break the file down line by line
            if ($fetchingData) {
                if (preg_match("/@([a-z|_|A-Z]+) (.+)/", $line, $matches)) {    // If we have an attribute...
                    $this->data[$matches[1]] = $matches[2];                 //                  ... store it into $data
                }
                else {
                    $fetchingData = false;
                    $this->contents .= $line . "\n";                               // If we don't have a match then we've reached the end of data.
                }
            }
            else {
                $this->contents .= $line . "\n";    // If we're not fetchign data then append the line to contents and add a newline incidcator
            }
        }

        /*
         * For each file we eventually want to recursively load any config.json's.
         * By storing a path queue which contains the directories leading to it, it makes it easier down the line.
         */
        $this->pathQueue = explode("/", dirname($filepath));

    }

}

?>
