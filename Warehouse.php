<?php

// Version RC-0.1-b
function __autoload($class_name) {
    include 'Classes/class.' . $class_name . '.php';
}

$defaults = array();
if (file_exists("Classes/config.defaults.json")) {
    CL::printDebug("Loading default Warehouse configuration", 0, Colour::Green);
    $defaults = Secretary::getJSON("Classes/config.defaults.json");
}
else {
    CL::printDebug("File: Classes/config.defaults.json was not found.", 0, Colour::Red);
    CL::printDebug("Warehouse may not function correctly without this file.", 0, Colour::Red);
    CL::printDebug("Please re-download from Github.", 0, Colour::Red);
}

if (file_exists("config.json")) {
    CL::printDebug("Loading config file.", 0, Colour::Green);
    $defaults = Secretary::getJSON("config.json", $defaults);
}
else {
    CL::printDebug("No custom config file found. #BareBones");
}

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

$files = [];
$markdowns = Secretary::rglob("*.md");

CL::printDebug("Found " . count($markdowns) . " Markdown Files");
CL::printDebug("----------------------------------------------");

foreach ($markdowns as $markdown) {

    CL::printDebug("Processing: " . $markdown);

    $file = new File($markdown);

    $configPath = "";                      // Load in the content of the JSON file
    $config = array("Template" => $defaults["Template"]);
    foreach ($file->pathQueue as $directory) {
        $configPath = ($configPath == "" ? "" : dirname($configPath) . "/") . $directory . "/config.json";
        if (file_exists($configPath)) {
            $config = Secretary::getJSON($configPath, $config);
            CL::printDebug("Loaded Config at: " . $configPath, 1, Colour::Green);
        }
    }

    if (array_key_exists("Content", $config)) {
        CL::println("WARNING: You've declared a field \"Content\" in a JSON file.", 0, Colour::Red);
        CL::println("The \"Content\" keyword is reserved for storing the file contents in.", 0, Colour::Red);
        CL::println("The value stored in the JSON file will be ignore.", 0, Colour::Red);
    }

    $data = array_merge($config, $file->meta);  // Renaming to make more semantic sense as we're now working with the "data"
    $data["Content"] = $file->contents;         // Pass in our file contents
    $raw = array();
    if (array_key_exists("DataExtensions", $defaults)) {
        $tuple = DataProcessor::process($data, $defaults["DataExtensions"]);     // Process our data to be filled in
        $data = $tuple["DATA"];
        $raw = $tuple["RAW"];
        CL::printDebug("Processed data", 1, Colour::Green);
    }
    else {
        CL::printDebug("No data extensions declared", 1, Colour::Green);
    }

    $templateFile = Templater::process("../templates/" . $data["Template"] . ".html");    // Generate our template
    CL::printDebug("Processed template: " . $data["Template"], 1, Colour::Green);

    $data["Content"] = Regex::process($data["Content"]); // Now regex it all
    CL::printDebug("Processed Regex", 1, Colour::Green);

    $processedFile = LTM::process($templateFile, $data, $raw);   // Fill in any conditions and optionals
    CL::printDebug("Processed LTM", 1, Colour::Green);

    $file->contents = $processedFile;   // Store the complete processed file back into the file object
    array_push($files, $file);  // Add it to the array ready to be exported!
}

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
