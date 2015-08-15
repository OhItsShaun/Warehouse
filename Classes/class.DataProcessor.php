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
    public static function process ($data) {

        /*
         *  Since we have changed directories through code we need to get the file location for *this* class
         *  We then load the data processor extensions, find out what fields they want to edit and then apply them
         */
        $ref = new ReflectionClass('DataProcessor');
        $filePaths = glob(dirname(dirname($ref->getFileName())) . "/Extensions/Data/data.*.php");

        foreach ($filePaths as $filePath) {
            include_once($filePath);
            if (preg_match("/data\.(\w+)\.php/", basename($filePath), $matches)) {  // Extract the class name
                $appliesTo = $matches[1]::applyToFields();
                foreach ($appliesTo as $applies) {
                    if (array_key_exists($applies, $data)) {
                        $data[$applies] = $matches[1]::process($data[$applies], $applies, $data);
                    }
                }
            }
        }

        return $data;
    }

}

?>
