<?php
/*
 *  Takes a filepath of a template file and replaces @template calls with the appropriate files
 */
class Templater {

    /**
     * Request a template from the templates directory
     * @param  String $templateName The name of the template to request
     * @return String               The processed template file
     */
    static function process ($templateName) {

        $ref = new ReflectionClass('Templater');
        $filePath = dirname(dirname($ref->getFileName())) . "/templates/" . $templateName . ".html";

        if (!file_exists($filePath)) {      // Does the requested file actuallye exist?
            CL::println ("Template at " . $filePath . " does not exist.", 0, Colour::Red);
            CL::println ("Warehouse cannot continue. Please fix. Exiting program.", 0, Colour::Red);
            exit;
        }

        $rawFile = file_get_contents($filePath);    // If it does get it's contents
        $regex = "/@template\(([\w|.]+)\)/";        // Check to see if there are any @template calls
        $processedFile = preg_replace_callback($regex, function ($matches) {
            return Templater::process($matches[1]);                            // recursion recursion recursion recursion..
        }, $rawFile);

        return $processedFile;
    }
}
?>
