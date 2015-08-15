<?php

class Figures {

    static function getRegexs() {
        return array(
            '/^Figure: (.+) "(.+)"/m' => "<figure><img src=\"$1\"/><figcaption>$2</figcaption></figure>",
            '/^Figure\[(.*)\]: (.+) "(.*)"/m' => "<figure><img src=\"$2\" class=\"$1\"/><figcaption>$3</figcaption></figure>",
            '/^Figure: (.+)/m' => "<figure><img src=\"$1\"/></figure>",
            '/^Figure\[(.*)\]: (.+)/m' => "<figure><img src=\"$2\" class=\"$1\"/></figure>"
            );
    }

}

?>
