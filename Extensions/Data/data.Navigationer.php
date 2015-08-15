<?php

class Navigationer implements DataHandler {

    static function applyToFields() {
        return array("Navigation", "Footer");
    }

    static function process ($links, $fieldName, $data) {
        $text = "<ul>\n";
        foreach($links as $display => $slug) {
            $text .= "\t<li";
            if (is_array($slug)) {
                $text .= ">" . $display . "\n";
                $text .= self::process($slug, $fieldName, $data);
                $text .= "</li>";
            }
            else {
                if ($slug == $data["slug"]) {
                    $text .= " class=\"current\">";
                }
                else {
                    $text .= ">";
                }
                $text .= "<a href=\"" . ($data["RelativeRoot"] == "./" ? "" : $data["RelativeRoot"]) . $slug . "\">" . $display . "</a></li>\n";
            }
        }
        $text .= "</ul>\n";
        return $text;
    }

}

?>
