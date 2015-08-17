<?php
/*
 *  Takes a filepath of a template file and replaces @template calls with the appropriate files
 */
class Templater {
    static function process ($filePath) {
        if (!file_exists($filePath)) {      // Does the requested file actuallye exist?
            CL::println ("Template at " . $filePath . " does not exist.", 0, Colour::Red);
            CL::println ("Warehouse cannot continue. Please fix. Exiting program.", 0, Colour::Red);
            exit;
        }

        $rawFile = file_get_contents($filePath);    // If it does get it's contents
        $regex = "/@template\(([\w|.]+)\)/";        // Check to see if there are any @template calls
        $processedFile = preg_replace_callback($regex, function ($matches) use ($filePath) {
            $newPath = dirname($filePath) . "/" . $matches[1] . ".html";    // If there are get the filepath and..
            return Templater::process($newPath);                            // recursion recursion recursion recursion..
        }, $rawFile);

        return $processedFile;
    }
}
?>
