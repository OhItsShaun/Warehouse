<?php

/*
 *  The LTM (long-term-memory) class. This fills in all data requests handling:
 *  - @data
 *  - @data?            [optionals]
 *  - @data? { ... }    [optional blocks]
 *
 *  @todo add the ability to iterate/foreach and @this() for scoping
 */
class LTM {


    /**
     * Process source code for any data requests and optional requests and fill them
     * @param  string   $text Text to process
     * @param  array    $data Data used to fill
     * @return string   Processed text
     */
    public static function process ($text, $data, $raw) {
        $lines = [];
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $text) as $line) {
            array_push($lines, $line);
        }

        $renderableLines = self::parse($lines, $data, $raw);  // We know using $data which conditionals are able to be used

        $text = "";
        foreach($renderableLines as $line) {    // Go through line by line
            $text .= $line . "\n";
        }

        /*
         *  Optional values such as tags we'll omit - RAW
         */
        $text = preg_replace_callback("/@data\?\((\w+)\)::RAW/", function ($matches) use ($raw) {
                if (array_key_exists($matches[1], $raw)) {
                        return $raw[$matches[1]];
                }
                return "";
        }, $text);

        /*
         *  Optional values such as tags we'll omit
         */
        $text = preg_replace_callback("/@data\?\((\w+)\)/", function ($matches) use ($data) {
                if (array_key_exists($matches[1], $data)) {
                        return $data[$matches[1]];
                }
                return "";
        }, $text);

        /*
         *  Optional values such as tags we'll omit - RAW
         */
        $text = preg_replace_callback("/@data\((\w+)\)::RAW/", function ($matches) use ($raw) {
                if (array_key_exists($matches[1], $raw)) {
                        return $raw[$matches[1]];
                }
                CL::println("ERROR: Template requested ". $matches[1] ."::RAW but was not found. Please review file.", 0, Colour::Red);
                return $matches[0];
        }, $text);

        /*
         *  Non-Optionals we'll need to alert to the CLI to let the user know there's something up
         */
        $text = preg_replace_callback("/@data\((\w+)\)/", function ($matches) use ($data) {
                if (array_key_exists($matches[1], $data)) {
                        return $data[$matches[1]];
                }
                CL::println("ERROR: Template requested ". $matches[1] ." but was not found. Please review file.", 0, Colour::Red);
                return $matches[0];
        }, $text);

        return $text;
    }

    /**
     * The magic of parsing the source file
     * @param  string   $lines              The source code broken up line by line
     * @param  array    $data               The data to fill in
     * @param  boolean  [$ignoring = false] Whether or not we're ignoring this block (e.g. a condition isn't met)
     * @return array An array of lines to be rendered up the chain (e.g. all conditions met for that block)
     */
    private static function parse ($lines, $data, $raw, $ignoring = false) {
        $renderLines = array();
        while (count($lines) > 0) {
            $line = array_shift($lines);
            if (preg_match("/^[\t ]*@data\?\((\w+)\) {[\t ]*$/", $line, $matches)) {  // If we have a request for data
                if (array_key_exists($matches[1], $data)) {
                    $lines = self::parse($lines, $data, $raw);
                }
                else {
                    $lines = self::parse($lines, $data, $raw, true);
                }
            }
            else if (preg_match("/^[\t ]*@data\?\((\w+)\)::RAW {[\t ]*$/", $line, $matches)) {  // If we have a request for data
                if (array_key_exists($matches[1], $raw)) {
                    $lines = self::parse($lines, $data, $raw);
                }
                else {
                    $lines = self::parse($lines, $data, $raw, true);
                }
            }
            else if (preg_match("/^[\t ]*@data!\((\w+)\) {[\t ]*$/", $line, $matches)) {  // If we have a request for data
                if (array_key_exists($matches[1], $data)) {
                    $lines = self::parse($lines, $data, $raw, true);
                }
                else {
                    $lines = self::parse($lines, $data, $raw);
                }
            }
            else if (preg_match("/^[\t ]*@data!\((\w+)\)::RAW {[\t ]*$/", $line, $matches)) {  // If we have a request for data
                if (array_key_exists($matches[1], $raw)) {
                    $lines = self::parse($lines, $data, $raw, true);
                }
                else {
                    $lines = self::parse($lines, $data, $raw);
                }
            }
            else if (preg_match("/^[\t ]*}$/m", $line, $matches)) {    // If we have a block closure
                $ignoring = false;
                return array_merge($renderLines, $lines);
            }
            else {
                if (!$ignoring) {
                    array_push($renderLines, $line);
                }
            }
        }
        return array_merge($renderLines, $lines);
    }
}

?>
