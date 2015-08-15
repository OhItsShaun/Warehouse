<?php

/*
 *  The big scary beast that proccesses our content and adds semantics.
 *  Looks awful, right?
 */
class Regex {

    /**
     * Processes raw text with regex patterns
     * @param  string $text The raw string to apply the regex on
     * @return string The processes string
     */
    public static function process ($text) {

        /*
         *  Since we have changed directories through code we need to get the file location for *this* class
         *  We then load all regex files
         */
        $thisClassReference = new ReflectionClass('Regex');
        $filePaths = glob(dirname(dirname($thisClassReference->getFileName())) . "/Extensions/Regex/regex.*.php");

        foreach ($filePaths as $filePath) {
            include_once($filePath);
            if (preg_match("/regex\.(\w+)\.php/", basename($filePath), $matches)) {     // We extract out the class name from the file
                printDebug("Applying Regex: " . $matches[1]);
                $rules = $matches[1]::getRegexs();                                      // We then get the regexes array (pattern => replace)
                foreach ($rules as $regex => $replacement) {
                    if (is_callable ($matches[1] . "::" . $replacement)) {
                        $text = preg_replace_callback ($regex, $matches[1] . "::" . $replacement, $text);
                    }
                    else {
                        $text = preg_replace ($regex, $replacement, $text);
                    }
                }
            }
        }
        return $text;
    }

}

?>
