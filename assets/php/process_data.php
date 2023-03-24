<?php
// process_data.php

include_once ("../../hdd_handler.php");
include_once ("../../translate.php");


// Set a time limit for the script execution (in seconds)
set_time_limit(60); // 1 minute

// Check the Content-Length header to limit the input size
$contentLength = (int) $_SERVER['CONTENT_LENGTH'];
$maxContentLength = 30 * 1024 * 1024; // 30 MB

if ($contentLength > $maxContentLength) {
    http_response_code(413); // Payload Too Large
    echo json_encode(array('error' => 'Input size exceeds the allowed limit (30 MB)'));
    exit;
}

$dataArray = json_decode(file_get_contents("php://input"), true);

// Extract user language and isFinal flag from dataArray[0][0]
list($originalSid, $originalFilename, $userCID, $userLang, $isFinalString) = explode('_', $dataArray[0][0]);
$isFinal = $isFinalString === "final";
$originalYear = "";
$originalMonth = "";
$originalDay = "";
$originalName = "";

if ($originalFilename != "0") {
    //looks like this: 2023-03-23-EinSchoenerTitel-63bc9077d16ef104631330.csv
    list($originalYear, $originalMonth, $originalDay, $originalName, $userCID) = explode('-', $originalFilename); //overwrite cid, because it should be the creators, not the contributors!
    $userCID = str_replace('.csv', '', $userCID);
}

// Create a new array to hold the transformed data
$surveyData = [];

// Copy the top-level information
$surveyData[0] = [
    $originalSid,
    $dataArray[0][1],
    $dataArray[0][2],
    $dataArray[0][3],
    $dataArray[0][4],
    $dataArray[0][5],
];

$surveyData[1] = [
    $dataArray[1][0],
];

// Dictionary to map old keywords to new keywords
$keywordMapping = [
    "description" => "info",
    "free_text" => "textfeld",
    "single_choice" => "oder",
    "multiple_choice" => "und",
    "dropdown" => "gruppe",
    "picture" => "img",
];

// Loop through the questions and transform them
for ($i = 2, $n = count($dataArray); $i < $n; $i++) {
    $questionData = $dataArray[$i];

    // Map the keyword
    $newKeyword = $keywordMapping[$questionData[0]];

    // Count follow-up questions
    $followUpCount = 0;
    for ($j = $i + 1; $j < $n && $dataArray[$j][1] === "is_follow_up"; $j++) {
        $followUpCount++;
    }

    // Update the keyword with the follow-up count
    if ($followUpCount > 0)
        $newKeyword .= $followUpCount;

    // Create a new array for the transformed question data
    $newQuestionData = [$newKeyword];

    // Remove the "is_follow_up" flag and copy the remaining data
    for ($k = 2, $m = count($questionData); $k < $m; $k++) {
        $newQuestionData[] = $questionData[$k];
    }

    // Add the transformed question data to the survey data
    $surveyData[] = $newQuestionData;
}

if ($userLang !== "de") {
    for ($i = 1, $n = count($surveyData[0]); $i < $n; $i++) {
        $surveyData[0][$i] = translate($surveyData[0][$i], $userLang, "de");
    }

    for ($i = 2, $n = count($surveyData); $i < $n; $i++) {
        for ($j = 1, $m = count($surveyData[$i]); $j < $m; $j++) {
            $surveyData[$i][$j] = translate($surveyData[$i][$j], $userLang, "de");
        }
    }
}

writeCSVFile($isFinal, $surveyData[0][1], $userCID, $originalFilename, $surveyData);
?>