<?php

// Version RC-0.2-b
function __autoload($class_name) {
    include 'Classes/class.' . $class_name . '.php';
}

$defaults = Secretary::initDefaults();

define("DEBUG", $defaults["debug"]);  // If we're in debug then we don't care about overwriting certain data
CL::printDebug("Compiling in Debug Mode.");

if (!is_dir('source')) {    // Check if we have a source folder to work through, otherwise exit
    CL::println("No /source/ directory found. Unable to generate site.", 0, Colour::Red);
    CL::println("Please create /source/ so I can process some content :)");
    exit;
}

if (!is_dir('templates')) {    // Check if we have a template folder to work through, otherwise exit
    CL::println("No /templates/ directory found. Unable to generate site.", 0, Colour::Red);
    CL::println("Please create /templates/ with a template file in. :)");
    exit;
}

if (is_dir('upload')) {     // Overwrite the contents of an already existing upload folder
    if (!DEBUG) {           // We don't care about overwriting in debug mode, otherwise trigger a warning
        CL::println("An upload folder already exists.", 0, Colour::Red);
        CL::println("This process will overwrite ALL data in the upload folder.", 0, Colour::Red);
        CL::println("Type 'yes' to contoinue. Any other input will terminate the program.");
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if(trim($line) != 'yes'){
            CL::println("Oki-doki. Terminating program.");
            exit;
        }
    }
    Secretary::deleteDirectory('upload');
}
mkdir('upload');
CL::printDebug("Created Upload Folder.", 0, Colour::Green);

/*
 *  This is where all the magic happens.
 *
 */
chdir("source");

$markdowns = Secretary::rglob("*.md");
CL::printDebug("Found " . count($markdowns) . " Markdown Files");
CL::printDebug("----------------------------------------------");

$files = FilesProcessor::process("", $defaults);

chdir("../upload");

/*
 *  We've converted all the files, now lets start the exporting
 */

CL::printDebug("Exporting: ");
foreach ($files as $file) {
    CL::printDebug($file->path, 1);
    if(!is_dir(dirname($file->path))) {                // If the directory tree doesn't already exist
        mkdir(dirname($file->path), 0777, TRUE);       // Recursively create it
    }
    file_put_contents(dirname($file->path) . "/" . basename($file->path, ".md") . ".html", $file->contents);    // Now lets through the contents there
}

/*
 *  So we've converted all markdown files - now what? What more do you people want from me?!
 *  Fine we'll move your CSS, images, videos and all that other crap over.
 */

chdir("../source");

$all = Secretary::rglob('*.*');               // We start off by fetching every file
$confs = Secretary::rglob('config.json');     // We get all config.json's
$remaining = array_diff($all, $markdowns);    // We remove the markdowns we found earlier, we don't want to copy them to the live site
$remaining = array_diff($remaining, $confs);  // We then remove all configs as well - we definitely don't want them going across

CL::printDebug("Copying " . count($remaining) . " misc files to the upload folder.");
CL::printDebug("Copying: ");

foreach ($remaining as $filepath) {
    CL::printDebug($filepath, 1);

    $fileContents = file_get_contents($filepath);   // This is a really dirty way to do it, and creates a nasty memory foot print - alternatives?
    chdir("../upload");                             // Change to our upload
    if(!file_exists(dirname($filepath))) {          // If the directory path for the file doesn't already exist, recursively make it
        mkdir(dirname($filepath), 0777, TRUE);
    }
    file_put_contents(dirname($filepath) . "/" . basename($filepath), $fileContents);   // We then put the file contents there as a clone

    chdir("../source"); // Move back to the source for the next possible file
}

CL::println("Done!", 0, Colour::Green);

// That wasn't so bad, right?

?>
