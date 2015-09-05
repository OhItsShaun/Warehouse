<?php

class FilesProcessor {

    public static function process ($directory = "", $defaults, $config = array()) {
        PipelineHooks::beforeProcessingFilesIn($directory, $defaults, $config);         // Hook for before this directory is processed

        $configPath = $directory . "config.json";
        if (file_exists($configPath)) {
            CL::printDebug("Config File at: " . $configPath, 1);
        }
        $config = Secretary::getJSON($configPath, $config);
        if (array_key_exists("Content", $config)) {
            CL::println("WARNING: You've declared field \"Content\" in JSON file: " . $directory . "config.json", 0, Colour::Red);
            CL::println("The \"Content\" keyword is reserved for storing the file contents in.", 0, Colour::Red);
            CL::println("The value for \"Content\" stored in the JSON file will be ignored.", 0, Colour::Red);
        }

        $files = array();

        $markdowns = glob($directory . "*.md");
        foreach ($markdowns as $markdown) {
            CL::printDebug("Processing: " . $markdown);
            $file = new File($markdown);

            // Set up our @data
            $data = array_merge($config, $file->data);  // Renaming to make more semantic sense as we're now working with the "data"
            $data["Content"] = $file->contents;         // Pass in our file contents
            $raw = $data;                               // Store raw data before processing

            // Process our data through data extensions
            if (array_key_exists("DataExtensions", $defaults)) {
                $data = DataProcessor::process($data, $defaults["DataExtensions"]);     // Process our data to be filled in
                CL::printDebug("Processed data", 1, Colour::Green);
            }
            else {
                CL::printDebug("No data extensions declared", 1);
            }

            $data["Content"] = Regex::process($data["Content"]); // Now regex it all
            CL::printDebug("Processed Regex", 1, Colour::Green);

            $templateFile = Templater::process($data["Template"]);    // Generate our template
            CL::printDebug("Processed template: " . $data["Template"], 1, Colour::Green);

            $processedFile = LTM::process($templateFile, $data, $raw);   // Fill in any conditions and optionals
            CL::printDebug("Processed LTM", 1, Colour::Green);

            $file->contents = $processedFile;   // Store the complete processed file back into the file object
            array_push($files, $file);  // Add it to the array ready to be exported!
        }

        PipelineHooks::afterProcessing($files, $directory, $defaults, $config);  // Hook for after this directory is processed - and lets pass some files!

        $directories = glob($directory . "*", GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $newFiles = self::process($directory . "/", $defaults, $config);
            foreach ($newFiles as $newFile) {
                array_push($files, $newFile);
            }
        }

        return $files;

    }

}
