<?php

interface DataHandler
{
    static function applyToFields();
    static function process ($fieldData, $fieldName, $data);
}

?>