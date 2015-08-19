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
        $raw = array();
        foreach ($extensions as $extension => $appliesTo) {
            $classFilePath = $dataExtensionsPath . "data." . $extension . ".php";
            if(file_exists($classFilePath)) {
                include_once($classFilePath);
                $appliesTo = explode(",", $appliesTo);
                foreach ($appliesTo as $applies) {
                    $applies = trim($applies);
                    if (array_key_exists($applies, $data)) {
                        CL::printDebug("Applying " . $extension . " to: " . $applies, 1);
                        $raw[$applies] = $data[$applies];
                        $data[$applies] = $extension::process($data[$applies], $applies, $data);
                    }
                }
            }
        }

        $tuple = array();
        $tuple["RAW"] = $raw;
        $tuple["DATA"] = $data;
        return $tuple;
    }

}

?>
