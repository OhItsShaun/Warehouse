<?php

/*
 *  DataProcessor is one of the ways in which we enable extensions.
 *
 */
class DataProcessor {

    /**
     * Process data that is going to be inserted into the export
     * @param  array $data A mixed array of data to be processed
     * @return array An array of processed data ready to be plugged into the file
     */
    public static function process ($data, $extensions) {

        /*
         *  Since we have changed directories through code we need to get the file location for *this* class
         *  We then load the data processor extensions, find out what fields they want to edit and then apply them
         */
        $ref = new ReflectionClass('DataProcessor');
        $dataExtensionsPath = dirname(dirname($ref->getFileName())) . "/Extensions/Data/";

        foreach ($extensions as $extension => $appliesTo) {     // For every extension that the config file wants us to apply to

            $classFilePath = $dataExtensionsPath . "data." . $extension . ".php"; // Get the class filepath from the extension folder
            if(file_exists($classFilePath)) {
                include_once($classFilePath);   // Include the class

                $appliesTo = explode(",", $appliesTo);  // The "appliesTo" field specifies what @data should be processed, separated by ","
                foreach ($appliesTo as $applies) {      // For every @data it should be applied to
                    $applies = trim($applies);          // Trim any leading or trailing whitespace
                    if (array_key_exists($applies, $data)) {        // If the @data actually exists
                        CL::printDebug("Applying " . $extension . " to: " . $applies, 1);   // Notify we're applying the extension
                        $data[$applies] = $extension::process($data[$applies], $applies, $data);
                    }
                }
            }
            
        }
        return $data;
    }

}

?>
