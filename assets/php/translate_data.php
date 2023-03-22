<?php
//translate_data.php
include_once ("../../translate.php");

// Set a time limit for the script execution (in seconds)
set_time_limit(60); // 1 minute

// Check the Content-Length header to limit the input size
$contentLength = (int) $_SERVER['CONTENT_LENGTH'];
$maxContentLength = 10 * 1024 * 1024; // 10 MB

if ($contentLength > $maxContentLength) {
    http_response_code(413); // Payload Too Large
    echo json_encode(array('error' => 'Input size exceeds the allowed limit (10 MB)'));
    exit;
}

$dataArray = json_decode(file_get_contents("php://input"), true);

// Use existing translate engine translate(Text, source language, target language);
$translatedText = translate($dataArray[0], $dataArray[1], $dataArray[2]);

// Return translated text as JSON
header('Content-Type: application/json');
echo json_encode(array('translatedText' => $translatedText));
?>