<?php
function deconstructData(array $dataArray, bool $final, string $cid, $originalFilename = "0", string $sid = "0", string $lang = "de"): array {
    if ($final) $mode = "final";
    else $mode = "draft";
    $deconstructedData = [];

    // Extract top-level information
    $deconstructedData[0] = [
        implode('_', [$sid, $originalFilename, $cid, $lang, $mode]),
        $dataArray[0][1],
        $dataArray[0][2],
        $dataArray[0][3],
        $dataArray[0][4],
        $dataArray[0][5],
    ];

    $deconstructedData[1] = [
        $dataArray[1][0],
    ];

    // Dictionary to map new keywords to old keywords
    $keywordMapping = [
        "info" => "description",
        "textfeld" => "free_text",
        "oder" => "single_choice",
        "und" => "multiple_choice",
        "gruppe" => "dropdown",
        "img" => "picture",
    ];

    // Loop through the questions and transform them
    for ($i = 2, $n = count($dataArray); $i < $n; $i++) {
        $questionData = $dataArray[$i];

        // Extract the keyword and follow-up count from the new keyword
        if (preg_match('/([a-z]+)(\d*)/', $questionData[0], $matches)) {
            $newKeyword = $matches[1];
            $followUpCount = intval($matches[2]);
        } else {
            $newKeyword = $questionData[0];
            $followUpCount = 0;
        }

        // Map the keyword
        $oldKeyword = $keywordMapping[$newKeyword];

        // Create a new array for the deconstructed question data
        $oldQuestionData = [$oldKeyword];

        // Copy the remaining data
        for ($k = 1, $m = count($questionData); $k < $m; $k++) {
            $oldQuestionData[] = $questionData[$k];
        }

        // Add the deconstructed question data to the deconstructed data
        $deconstructedData[] = $oldQuestionData;

        // Add the "is_follow_up" flag for follow-up questions
        for ($j = 0; $j < $followUpCount; $j++) {
            $i++;
            $followUpData = $dataArray[$i];
            $oldKeyword = $keywordMapping[$followUpData[0]];
            $oldQuestionData = [$oldKeyword, "is_follow_up"];
            for ($k = 1, $m = count($followUpData); $k < $m; $k++) {
                $oldQuestionData[] = $followUpData[$k];
            }
            $deconstructedData[] = $oldQuestionData;
        }
    }

    // Add empty value where no "is_follow_up" flag is
    for ($i = 2; $i < count($deconstructedData); $i++) {
        if ($deconstructedData[$i][1] !== "is_follow_up")
            array_splice($deconstructedData[$i], 1, 0, '');
    }

    return $deconstructedData;
}
/*
$in = '[["0","Umfragetitel","Untertitel","Beschreibung","Weitere Beschreibung","Mitwirkende"],["students"],["und1","Was ist Deine Lieblingsfarbe?","Rot","Gr\u00fcn","Blau","Lila"],["oder","Was ist Dein Lieblingstier","Hund","Katze","Maus"],["und","Was ist Deine Lieblingsfarbe?","Rot","Gr\u00fcn","Blau","Lila"],["oder","Was ist Dein Lieblingstier","Hund","Katze","Maus"]]';
echo json_encode(deconstructData(json_decode($in), false, "63bc9077d16ef104631330"), true);
*/
?>