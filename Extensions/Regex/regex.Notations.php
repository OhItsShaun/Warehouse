<?php

class Notations {

    static function getRegexs() {
        return array('/\^\((.+?)\)/' => "<sup>$1</sup>",
                    '/\v\((.+?)\)/' => "<sub>$1</sub>");
    }

}

?>
