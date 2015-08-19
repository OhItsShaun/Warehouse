<?php

class BlogFeed {

    public static function postProcessingDirectory($directoryPath, $files, $config) {
        $config["PostsPerPage"] = 10;
    }

}

?>
