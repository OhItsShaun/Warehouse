<?php

class Blockquotes {

    static function getRegexs() {

        $keywords = array("Important", "Note", "Example");
        $regexs = array();

        foreach ($keywords as $keyword) {
            $pattern = '/^'. $keyword .': (.+)\n((.+\n)+)/m';                      // Note: Title \n A body of text
            $replace = "<blockquote data-type=\"". strtolower($keyword) ."\" data-title=\"$1\">\n$2\n</blockquote>\n";
            $regexs[$pattern] = $replace;

            $pattern = '/^'. $keyword .':\n((.+\n)+)/m';                      // Note: A body of text
            $replace = "<blockquote data-type=\"". strtolower($keyword) ."\">\n$1\n</blockquote>\n";
            $regexs[$pattern] = $replace;

            $pattern = '/^'. $keyword . ': (.+)/m';                                // Note: Title
            $replace = "<blockquote data-type=\"". strtolower($keyword) ."\">\n$1\n</blockquote>\n";
            $regexs[$pattern] = $replace;
        }

        $regexs['/^(.+) said \"((.+|.+\n)+)\"\n/m'] = "<blockquote data-title=\"$1\">\n$2\n</blockquote>\n";   // Dr Sesuss said "...."
        $regexs['/^\"((.+|.+\n)+)\"\n/m'] = "<blockquote>\n$1\n</blockquote>\n";   // "Quote"

        return $regexs;

    }

}

?>
