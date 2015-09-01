<?php

class PipelineHooks {

    // Called before ANY processing is performed on a directory
    public static function beforeProcessingFilesIn ($directory, $defaults, $config) {
        foreach ($defaults["Hooks"] as $appliesTo => $hookExtensions) {        // Cycle through the paths that apply to
            if ($appliesTo == $directory) {            // If the path matches the current directory
                foreach ($hookExtensions as $hookExtension => $arguments) {   // For every hook for this directory
                    CL::printDebug($hookExtension . " before-hook called upon: " . $directory, 0, Colour::White);
                    include_once(self::getHookPath($hookExtension));
                    $hookExtension::beforeProcessingFilesIn($directory, $config, $arguments);
                }
            }
        }
    }

    // Called after ALL files in a directory have been processed, and BEFORE further directories are explored
    public static function afterProcessing ($files, $directory, $defaults, $config) {
        foreach ($defaults["Hooks"] as $appliesTo => $hookExtensions) {        // Cycle through the paths that apply to
            if ($appliesTo == $directory) {            // If the path matches the current directory
                foreach ($hookExtensions as $hookExtension => $arguments) {   // For every hook for this directory
                    CL::printDebug($hookExtension . " after-hook called upon: " . $directory, 0, Colour::White);
                    include_once(self::getHookPath($hookExtension));
                    $hookExtension::afterProcessing($files, $directory, $config, $arguments);
                }
            }
        }
    }

    private static function getHookPath ($className) {
        $ref = new ReflectionClass('PipelineHooks');
        return dirname(dirname($ref->getFileName())) . "/Extensions/Hooks/hooks." . $className . ".php";
    }

}

?>
