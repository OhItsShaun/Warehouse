<?php

// Version RC-0.1-a
define("DEBUG", false);  // If we're in debug then we don't care about overwriting certain data

function __autoload($class_name) {
    include 'Classes/class.' . $class_name . '.php';
}

/**
 * Parse a JSON file and, if specified, merge it with an existing array (useful for chowing down config.json's)
 * @param  string $filePath              The filepath of the JSON file to decode
 * @param  array [$mergeWith = array()]  An array to merge the results with
 * @return array The Parsed Json file
 */
function getJSON ($filePath, $mergeWith = array()) {
    if (file_exists($filePath)) {
        $configJSON = file_get_contents($filePath);
        $configDecode = json_decode($configJSON, true);
        return array_merge($mergeWith, $configDecode);
    }
    else {
        return array();
    }
}

/**
 * Print a line of text to the CLI with a new line character.
 * @param string $string The text to be printed to the CLI
 */
function println ($text) {
    print $text . "\n";
}

/**
 * Quickway to print text if we're in debug
 * @param string $text Text to print to console
 */
function printDebug ($text) {
    if (DEBUG) {
        println($text);
    }
}

if (!is_dir("source")) {    // Check if we have a source folder to work through, otherwise exit
    println("There's no source folder? What site am I meant to generate?!");
    exit;
}


if (is_dir('upload')) {     // Overwrite the contents of an already existing upload folder
    if (!DEBUG) {           // We don't care about overwriting in debug mode, otherwise trigger a warning
        println("\033[31mAn upload folder already exists.");
        println("\033[31mThis process will overwrite ALL data in the upload folder.");
        println("\033[0mType 'yes' to contoinue. Any other input will terminate the program.");
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if(trim($line) != 'yes'){
            println("ABORTING!\n");
            exit;
        }
    }
    Secretary::deleteDirectory('upload');
}
mkdir('upload');
printDebug("Created Upload Folder");

/*
 *  This is where all the magic happens.
 *
 */
chdir("source");

$files = [];
$markdowns = Secretary::rglob("*.md");
printDebug("Found " . count($markdowns) . " Markdown Files");
printDebug("----------------------------------------------");

foreach ($markdowns as $markdown) {

    printDebug("Processing: " . $markdown);

    $file = new File($markdown);

    $configPath = "";                      // Load in the content of the JSON file
    $config = array();
    foreach ($file->pathQueue as $directory) {
        $configPath = ($configPath == "" ? "" : dirname($configPath) . "/") . $directory . "/config.json";
        $config = getJSON($configPath, $config);
        printDebug("    > Loaded Config at: " . $configPath);
    }

    if (array_key_exists("Content", $config)) {
        println("\033[31mWARNING: You've declared a field \"Content\" in a JSON file.");
        println("\033[31m         The \"Content\" keyword is reserved for storing the file contents in.");
        println("\033[31m         The value stored in the JSON file will be ignore.");
        println("\033[0m");
    }

    $data = array_merge($config, $file->meta);  // Renaming to make more semantic sense as we're now working with the "data"
    $data["Content"] = $file->contents;         // Pass in our file contents

    $data = DataProcessor::process($data);     // Process our data to be filled in
    printDebug("    > Processed data");

    $templateFile = Templater::process("../templates/" . $data["Template"] . ".md");    // Generate our template
    printDebug("    > Processed template: " . $data["Template"] . ".md");

    $data["Content"] = Regex::process($data["Content"]); // Now regex it all
    printDebug("    > Processed Regex");

    $processedFile = LTM::process($templateFile, $data);   // Fill in any conditions and optionals
    printDebug("    > Processed LTM");

    $file->contents = $processedFile;   // Store the complete processed file back into the file object
    array_push($files, $file);  // Add it to the array ready to be exported!
}

chdir("../upload");

/*
 *  We've converted all the files, now lets start the exporting
 */

printDebug("Exporting: ");
foreach ($files as $file) {
    printDebug("    > " . $file->path);
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

printDebug("Copying " . count($remaining) . " misc files to the upload folder.");
printDebug("Copying: ");

foreach ($remaining as $filepath) {
    printDebug("    > " . $filepath);

    $fileContents = file_get_contents($filepath);   // This is a really dirty way to do it, and creates a nasty memory foot print - alternatives?
    chdir("../upload");                             // Change to our upload
    if(!file_exists(dirname($filepath))) {          // If the directory path for the file doesn't already exist, recursively make it
        mkdir(dirname($filepath), 0777, TRUE);
    }
    file_put_contents(dirname($filepath) . "/" . basename($filepath), $fileContents);   // We then put the file contents there as a clone

    chdir("../source"); // Move back to the source for the next possible file
}

println("Done!");

// That wasn't so bad, right?

?>
