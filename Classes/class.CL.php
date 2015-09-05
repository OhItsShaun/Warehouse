<?php

/*
 *  A small file to handle printing to the command line
 */
class CL {
    /**
     * Print a line of text to the CLI with a new line character.
     * @param string $string The text to be printed to the CLI
     */
    public static function println ($text, $indent = 0, $colour = Colour::White) {
        $prefix = $colour;
        for ($i = 0; $i < $indent; $i++) {
            $prefix .= "    ";
        }
        if ($indent > 0) {
            $prefix .= "> ";
        }
        print $prefix . $text . Colour::White . "\n";   // We use Colour::White to prevent bleeding the colour over to the next line or immediate exit
    }

    /**
     * Quickway to print text if we're in debug
     * @param string $text Text to print to console
     */
    public static function printDebug ($text, $indent = 0, $colour = Colour::White) {
        if (DEBUG) {
            CL::println($text, $indent, $colour);
        }
    }
}

class Colour {
    const White = "\033[0m";
    const Red = "\033[31m";
    const Green = "\033[32m";
    const Blue = "\033[34m";
    const Pink = "\033[35m";
    const Aqua = "\033[36m";
}
