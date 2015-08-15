<?php

class Headings {

    static function getRegexs() {
        return array(
            '/^# (.+)/m' => "<h2>$1</h2>",             // h2
            '/^## (.+)/m' => "<h3>$1</h3>",             // h3
            '/^### (.+)/m' => "<h4>$1</h4>",             // h4
            '/^#### (.+)/m' => "<h5>$1</h5>",             // h5
            '/^##### (.+)/m' => "<h6>$1</h6>",             // h5
        );
    }

}
