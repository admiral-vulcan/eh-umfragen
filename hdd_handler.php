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

function writeCSVFile(bool $final, string $name, string $cid, array $content) {
    // Set the directory based on the value of $final
    $dir = $final ? 'surveys' : 'surveys-test';

    // Ensure the target directory exists
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    // Create the filename with the current date, cid, and name
    $date = date('Y-m-d');
    $filename = "{$date}-{$cid}-{$name}.csv";
    $filepath = "{$dir}/{$filename}";

    // Open the file in write mode
    $file = fopen($filepath, 'w');

    // Add UTF-8 BOM
    fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Iterate through the content array and write each row to the file
    foreach ($content as $row) {
        $line = '';
        $maxIndex = max(array_keys($row));
        for ($i = 0; $i <= $maxIndex; $i++) {
            $cellValue = isset($row[$i]) ? str_replace(';', '%3B', $row[$i]) : '';
            $line .= $cellValue . ';';
        }
        $line .= "\r\n";
        fputs($file, $line);
    }

    // Close the file
    fclose($file);

    // Set the permissions
    chmod($filepath, 0664);

    // Change the group
    chgrp($filepath, 1001);
}

//testing
/*
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
*/
?>
