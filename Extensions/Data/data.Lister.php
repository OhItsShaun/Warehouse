<?php

class Lister implements DataHandler {

    static function process ($fieldData, $fieldName, $data) {
        $items = explode(", ", $fieldData);
        $text = "<ul class=\"" . $fieldName . " \">\n";
        foreach($items as $item) {
            $text .= "\t<li>" . $item . "</li>\n";
        }
        $text .= "</ul>\n";
        return $text;
    }
}

?>
