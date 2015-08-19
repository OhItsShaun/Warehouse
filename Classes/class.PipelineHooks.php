<?php

class PipelineHooks {

    // Called before ANY processing is performed on a directory
    public static function beforeProcessingFilesIn ($directory) {
        CL::printDebug("Before hook called for: " . $directory, 0, Colour::Green);
    }

    // Called after ALL files in a directory have been processed, and BEFORE further directories are explored
    public static function afterProcessingFilesIn ($directory, $files) {
        CL::printDebug("After hook called for: " . $directory, 0, Colour::Green);
    }

    private static function loadFunction ($call, $directory, $files = array()) {
        $ref = new ReflectionClass('PipelineHooks');
        $hooksPath = dirname(dirname($ref->getFileName())) . "/Extensions/Hooks/";
        $hooksClasses = glob($hooksPath . "hooks.*.php");
        foreach ($hooksClasses as $hookClass) {

        }
    }

}

?>
