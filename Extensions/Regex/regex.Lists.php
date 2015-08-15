<?php

/*
 *  Detects lists and wraps with ul and li tag respectively
 */
class Lists {

    static function getRegexs() {
        return array(
            /*
             *  Detects lists with asteriks and replaces wraps with ul and li tag respectively
             */
            '/((^\* .+\n)+)/m' => "<ul>\n$1</ul>",      // Lists
            '/^\* (.+)/m' => "\t<li>$1</li>",            // List Item

            /*
             *  Detects lists with dashes
             */
            '/((^- .+\n)+)/m' => "<ul>\n$1</ul>",      // Lists
            '/^- (.+)/m' => "\t<li>$1</li>",            // List Item
        );
    }

}

?>
