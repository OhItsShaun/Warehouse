<?php
/*
 *  Takes a filepath of a template file and replaces @template calls with the appropriate files
 */
class Templater {
    static function process ($filePath) {
        if (!file_exists($filePath)) {
            println ("     > Template at " . $filePath . " does not exist. Exiting.");
            println ("       Warehouse cannot continue. Please fix. Exiting program.");
            exit;
        }
        $rawFile = file_get_contents($filePath);
        $regex = "/@template\(([\w|.]+)\)/";

        $processedFile = preg_replace_callback($regex, function ($matches) use ($filePath) {
            $newPath = dirname($filePath) . "/" . $matches[1] . ".md";
            return Templater::process($newPath);
        }, $rawFile);

        return $processedFile;
    }
}
?>
