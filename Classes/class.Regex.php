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

        CL::printDebug("Applying Regexes", 1);
        foreach ($filePaths as $filePath) {     // For every regex in the regex extensions
            include_once($filePath);            // Include the file
            if (preg_match("/regex\.(\w+)\.php/", basename($filePath), $matches)) {     // We extract out the class name from the file, and load that as the class
                //CL::printDebug("Regex: " . $matches[1], 2); // Extensive debug option here
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
