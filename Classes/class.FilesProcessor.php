<?php

class FilesProcessor {

    public static function process ($directory = "", $defaults) {
        PipelineHooks::beforeProcessingFilesIn($directory);         // Hook for before this directory is processed

        $files = array();
        $markdowns = glob($directory . "*.md");
        foreach ($markdowns as $markdown) {
            CL::printDebug("Processing: " . $markdown);

            $file = new File($markdown);
            $config = array("Template" => $defaults["Template"]);
            $configPath = "";                                           // Load in the content of the JSON file
            foreach ($file->pathQueue as $pathDirectory) {
                $configPath = ($configPath == "" ? "" : dirname($configPath) . "/") . $pathDirectory . "/config.json";
                if (file_exists($configPath)) {
                    $config = Secretary::getJSON($configPath, $config);
                    CL::printDebug("Loaded Config at: " . $configPath, 1, Colour::Green);
                }
            }

            if (array_key_exists("Content", $config)) {
                CL::println("WARNING: You've declared a field \"Content\" in a JSON file.", 0, Colour::Red);
                CL::println("The \"Content\" keyword is reserved for storing the file contents in.", 0, Colour::Red);
                CL::println("The value stored in the JSON file will be ignore.", 0, Colour::Red);
            }

            $data = array_merge($config, $file->meta);  // Renaming to make more semantic sense as we're now working with the "data"
            $data["Content"] = $file->contents;         // Pass in our file contents
            $raw = $data;                               // Store raw data before processing
            if (array_key_exists("DataExtensions", $defaults)) {
                $data = DataProcessor::process($data, $defaults["DataExtensions"]);     // Process our data to be filled in
                CL::printDebug("Processed data", 1, Colour::Green);
            }
            else {
                CL::printDebug("No data extensions declared", 1, Colour::Green);
            }

            $templateFile = Templater::process("../templates/" . $data["Template"] . ".html");    // Generate our template
            CL::printDebug("Processed template: " . $data["Template"], 1, Colour::Green);

            $data["Content"] = Regex::process($data["Content"]); // Now regex it all
            CL::printDebug("Processed Regex", 1, Colour::Green);

            $processedFile = LTM::process($templateFile, $data, $raw);   // Fill in any conditions and optionals
            CL::printDebug("Processed LTM", 1, Colour::Green);

            $file->contents = $processedFile;   // Store the complete processed file back into the file object
            array_push($files, $file);  // Add it to the array ready to be exported!
        }

        PipelineHooks::afterProcessingFilesIn($directory, $files);  // Hook for after this directory is processed - and lets pass some files!

        $directories = glob($directory . "*", GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $newFiles = self::process($directory . "/", $defaults);
            foreach ($newFiles as $newFile) {
                array_push($files, $newFile);
            }
        }

        return $files;

    }

}
