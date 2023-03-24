<?php
/**TODO
 * Get everything harddrive HERE!!
 *
 */

function getProfilePic() {
    if (isset($_SESSION['cid'])) {
        $filename = $_SESSION['cid'];
        // Search for file with the matching filename in /images/creatorPics
        foreach (glob("./images/creatorPics/{$filename}.*") as $file) {
            // Get the file extension
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            // Get the file path without the extension
            $path = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME);
            // If a file is found, return it along with the 'Profilbild' alt text and the file extension
            if ($ext !== 'avif') return array('path' => $path, 'ext' => $ext, 'alt' => 'Profilbild');
        }
    }
    // If no file is found or cid is not set, return the default image with the 'Ein Klemmbrett als Logo' alt text
    return array('path' => 'images/logo', 'ext' => 'png', 'alt' => 'Ein Klemmbrett als Logo');
}

function writeCSVFile(bool $final, string $name, string $cid, string $originalFilename, array $content) {
    $requestUri = $_SERVER['REQUEST_URI'];
    // Set the directory based on the value of $final
    $dir = $final ? 'surveys' : 'survey-drafts';

    // Ensure the target directory exists
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    if ($originalFilename != 0) $filename = $originalFilename;
    else {
        // Create the filename with the current date, cid, and name
        $timezone = new DateTimeZone('Europe/Berlin');
        $formattedDate = "YYYY-MM-DD";
        try {
            $date = new DateTime('now', $timezone);
            $formattedDate = $date->format('Y-m-d');
        } catch (Exception $e) {
            if ($GLOBALS["testDomain"] && !strpos($requestUri, 'assets/php')) echo $e;
        }
        $nameToFile = processTitleString($name);
        $filename = "{$formattedDate}-{$nameToFile}-{$cid}.csv";
    }

    if (strpos($requestUri, 'assets/php'))
        $filepath = "../../{$dir}/{$filename}";
    else $filepath = "{$dir}/{$filename}";

    // Open the file in write mode
    $file = fopen($filepath, 'w');

    // Add UTF-8 BOM
    //fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Iterate through the content array and write each row to the file
    foreach ($content as $row) {
        $line = '';
        $maxIndex = max(array_keys($row));
        for ($i = 0; $i <= $maxIndex; $i++) {
            $cellValue = isset($row[$i]) ? str_replace(';', '%3B', $row[$i]) : '';
            $line .= $cellValue . ';';
        }
        $line .= "\r\n";
        $line = iconv("UTF-8", "WINDOWS-1252", $line);
        fputs($file, $line);
    }

    // Close the file
    fclose($file);

    // Set the permissions
    chmod($filepath, 0664);

    // Change the group
    chgrp($filepath, 1001);

    //return the filename for JS
    //echo $filename;

    //return the name for JS
    echo str_replace(' ', '_', $name);
}

function readCSVFile(bool $final, string $filename): array {
    $requestUri = $_SERVER['REQUEST_URI'];
    // Set the directory based on the value of $final
    $dir = $final ? 'surveys' : 'survey-drafts';

    // Set the file path based on the request URI
    if (strpos($requestUri, 'assets/php')) {
        $filepath = "../../{$dir}/{$filename}";
    } else {
        $filepath = "{$dir}/{$filename}";
    }

    // Open the file in read mode
    $file = fopen($filepath, 'r');

    // Read the content of the file into an array
    $content = [];
    while (($row = fgetcsv($file, 0, ';')) !== false) {
        // Iterate through the row, decoding URL-encoded semicolons and converting the encoding
        foreach ($row as &$cell) {
            $cell = str_replace('%3B', ';', $cell);
            $cell = iconv("WINDOWS-1252", "UTF-8", $cell);
        }
        unset($cell); // Remove reference to the last element

        // Remove empty value at the end of the row if it exists
        if (end($row) === '') {
            array_pop($row);
        }

        $content[] = $row;
    }

    // Close the file
    fclose($file);

    return $content;
}

function processTitleString($input) {
    // Replace German special characters
    $replacements = array(
        'ö' => 'oe',
        'ä' => 'ae',
        'ü' => 'ue',
        'Ö' => 'Oe',
        'Ä' => 'Ae',
        'Ü' => 'Ue',
        'ß' => 'ss'
    );
    $input = str_replace(array_keys($replacements), array_values($replacements), $input);

    // Remove all non-alphabetical characters
    $input = preg_replace('/[^a-zA-Z]/', ' ', $input);

    // Convert to UpperCamelCase
    $input = ucwords($input);
    $input = str_replace(' ', '', $input);

    // Truncate the string to the first 100 characters
    $input = substr($input, 0, 100);

    return $input;
}

function getDraftsNames($cid) {
    $draftsNames = [];
    $directory = "survey-drafts";

    if (is_dir($directory)) {
        $files = scandir($directory);

        foreach ($files as $file) {
            // Check if the file is a CSV and contains the CID in its filename
            if (strpos($file, $cid) !== false && pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                $filePath = $directory . '/' . $file;
                $fileHandle = fopen($filePath, 'r');

                if ($fileHandle !== false) {
                    // Read the first line of the file
                    $line = fgetcsv($fileHandle, 0, ';');

                    //get rid of the file extension
                    $file = pathinfo($file, PATHINFO_FILENAME);

                    // Check if the line has at least two cells and add the filename and second cell's value to the result array
                    if (isset($line[1])) {
                        $draftsNames[] = [
                            'filename' => $file,
                            'draftsname' => utf8Encode($line[1])
                        ];
                    }

                    fclose($fileHandle);
                }
            }
        }
    }

    return $draftsNames;
}



/*
//testing writer
$test[0][0] = "My new CSV";
$test[0][1] = "looks great!";
$test[1][0] = "This is the second line";
$test[1][1] = "and still looks great!";
$test[2][0] = "There is even a third line with special chars:";
$test[2][1] = "öäüÖÄÜ";
$test[2][2] = "ß!";
$test[3][0] = "And a fourth line with an omitted cell.";
$test[3][2] = "I wonder; what happens";

writeCSVFile(false, "cool Name", "randomID", $test);

//testing reader
require_once ("assets/php/deconstruct_data.php");
$testFilename = "2023-03-23-Umfragetitel-63bc9077d16ef104631330.csv";
$testCID = str_replace('.csv', '', explode("-", $testFilename)[4]);
$testCSV = readCSVFile(false, $testFilename);
$deconstructData = deconstructData($testCSV, false, $testCID, "de");

//array from csv
//echo json_encode($testCSV, true);

//array to be passed to survey builder
echo json_encode($deconstructData, true);
*/
?>
