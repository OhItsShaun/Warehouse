<?php
/*
 *  Takes a filepath of a template file and replaces @template calls with the appropriate files
 */
class Templater {
    static function process ($filePath) {
        if (!file_exists($filePath)) {
            println ("Filepath " . $filePath . " for Templater does not exist.");
            println ("Crashing in 3.. 2.. ");
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