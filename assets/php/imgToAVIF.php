<?php
function toAVIF($inputFile, $outputFile) {
// Set the input and output file paths

// Create a new Imagick object
    $imagick = new Imagick();

// Read the input file
    $imagick->readImage($inputFile);

// Set the output format to AVIF
    $imagick->setFormat('AVIF');

// Write the output file
    $imagick->writeImage($outputFile);

// Destroy the Imagick object
    $imagick->destroy();
}

//toAVIF("../../images/small_logo.png", "../../images/small_logo.avif");
?>