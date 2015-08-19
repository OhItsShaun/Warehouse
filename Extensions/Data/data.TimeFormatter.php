<?php

class TimeFormatter implements DataHandler {

    static function process ($time, $fieldName, $data) {
        date_default_timezone_set('Europe/London');
        return date('jS F, Y', strtotime($time));
    }
    
}

?>
