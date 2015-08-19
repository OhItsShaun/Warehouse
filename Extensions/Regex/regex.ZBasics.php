<?php

/**
 *  Zee Basics of Markdown.
 *  Get it? Z-Basics? Zee aka The? No? I thought it was a creative way to schedual this file last..
 */
class ZBasics {

    public static function getRegexs() {
        return array(
            '/\*(.+?)\*/' => "<b>$1</b>",             // *bold*
            '/~(.+?)~/' => "<del>$1</del>",           // ~deleted content~
            '/\[(.+?)\] \((.+?)\)/' => "<a href=\"$2\">$1</a>",           // [visit google] (www.google.com)
        );
    }

}

?>
