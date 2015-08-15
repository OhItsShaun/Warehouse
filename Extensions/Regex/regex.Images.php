<?php

class Images {

    static function getRegexs() {
        return array(
            '/^Image: (.+)/m' => "<img src=\"$1\"/>",
            '/^Image\[(.*)\]: (.+)/m' => "<img src=\"$2\" class=\"$1\"/>"
        );
    }

}

?>
