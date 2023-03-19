<?php
/**
 * surveys[][][]
 * Dim1: Umfragedatei, Dim2: Reihe, Dim3: Spaltenkästchen
 * [datei][0][0] = Titel
 * [datei][0][1] = Beschreibung
 * [datei][0][2] = id# (wenn bereits in Datenbank!)
 * [datei][1...][0] = Fragetyp
 * [datei][1...][1] = Frage
 * [datei][1...][2...] = Optionen
 */

function loadResults($sid_or_sname) {
    if (intval($sid_or_sname) > 0) {
        $output["name"] = get_survey_name($sid_or_sname);
        $output["sid"] = $sid_or_sname;
    }
    elseif (strlen($sid_or_sname) > 3) {
        $output["name"] = $sid_or_sname;
        $output["sid"] = get_survey_id($sid_or_sname);
    }
    else return -1;

    $results = [];
    $files = glob("results/*.csv");
    $fileNotFound = true;
    for ($i = 0; $i < sizeof($files); $i++) {
        $j = 0; //this row
        if (str_contains(strtolower($files[$i]), strtolower($output["name"]))) {
            $fileNotFound = false;
            $handle = fopen($files[$i], "r");
            $output["file"] = $files[$i];
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                $columns = count($data);
                if ($j <= 1) {
                    for ($c = 0; $c < $columns; $c++) {
                        if ($data[$c] === "") {
                            $columns = $c;
                            break;
                        }
                    }
                }
                for ($c = 0; $c < $columns; $c++) {
                    if (isset($data[$c]) && $data[$c] != "") $results[$j][$c] = utf8Encode($data[$c]);
                    else $results[$j][$c] = 0;
                }
                $j++;
            }
            fclose($handle);
            break;
        }
    }

    $output["countOfQuestions"] =  sizeof($results[1]) - 2 ;
    for ($j = 1; $j < sizeof($results); $j++) {
        for ($k = 0; $k < sizeof($results[$j]); $k++) {
            if ($j == 1) {  //here we get Questions [0] and answers [1..n]
                $output["QNA"][$k] = explode("; ", clearQuestion($results[$j][$k]));
                //
            }
            else {
                if (str_contains($results[$j][$k],"; ")) {
                    $output["multiresults"][$k][$j - 2] = explode("; ", clearQuestion($results[$j][$k])); //multi choice
                    $output["results"][$k][$j - 2] = -2;
                }
                else { //only one choice
                    $output["results"][$k][$j - 2] = clearQuestion($results[$j][$k]);
                }
            }
        }
    }
    $results = null;
    $nTest = $j - 2;

    for ($i = 0; $i < $output["countOfQuestions"]; $i++) {
        $output["counts"][$i] = array_count_values($output["results"][$i]);
        for ($j = 0; $j < sizeof($output["results"][$i]); $j++)
            if ($output["results"][$i][$j] == -2)
                for ($k = 0; $k < sizeof($output["multiresults"][$i][$j]); $k++)
                    $output["multicounts"][$i][intval($output["multiresults"][$i][$j][$k])] += 1;
    }

    $n = 0;
    for ($i= 0; $i < sizeof($output["counts"]) - 1; $i++) {
        $free = 0;
        $n = 0;
        $output["type"][$i] = get_type($output["sid"], $i);
        for ($j = 0; $j < sizeof($output["QNA"][$i]) - 1; $j++) {
            if ($output["QNA"][$i][1] != "offene Frage") {
                if (!isset($output["counts"][$i][$j])) $output["counts"][$i][$j] = 0;
                if (!isset($output["multicounts"][$i][$j])) $output["multicounts"][$i][$j] = 0;
                $output["counts"]["assign"][$i][$output["QNA"][$i][$j + 1]] = $output["counts"][$i][$j];
                $output["counts"]["assign"][$i][$output["QNA"][$i][$j + 1]] += $output["multicounts"][$i][$j];
                $n += $output["counts"][$i][$j];
            }
            else { //offene Fragen
                $output["counts"]["assign"][$i][0] = -1;
                for ($k = 0; $k < sizeof($output["results"][$i]); $k++) {
                    if ($output["results"][$i][$k] != 0) {
                        $free++;
                    }
                    $output["open"][$i][$k] = $output["results"][$i][$k];
                    $n++;
                }
                $output["open"][$i]["count"] = $free;
            }
        }
    }

    if ($fileNotFound) alert("Auswertung fehlt", "Die Auswertungsdatei fehlt.", "error", "true", "loadresult error 1: CSV missing: " . $output["name"]);
    for ($i = 4; $i < sizeof($output["QNA"])-2; $i++) {
        if ($output["QNA"][$i][1] !== "offene Frage" && sizeof($output["QNA"][$i]) < 3) {
            alert("Auswertung beschädigt", "Die Auswertungsdatei ist beschädigt.", "error", "true", "loadresult error 2: wrong size of QNA at iteration: " . $i);
            echo ": Fehler: Die Auswertungsdatei ist beschädigt.";
            $output = -1;
            break;
        }
    }

    if ($nTest == $n) $output["n"] = $n;
    else return -1;
    return $output;
}

function loadRelativeResults($sid_or_sname, $needleQuestion, $needleValue): int|array
{
    if (is_int($sid_or_sname)) {
        $output["name"] = get_survey_name($sid_or_sname);
        $output["sid"] = $sid_or_sname;
    } elseif (is_string($sid_or_sname)) {
        $output["name"] = $sid_or_sname;
        $output["sid"] = get_survey_id($sid_or_sname);
    }
    else return -1;

    $results = [];
    $files = glob("results/*.csv");

    for ($i = 0; $i < sizeof($files); $i++) {
        $j = 0; //this row
        if (str_contains(strtolower($files[$i]), strtolower($output["name"]))) {
            $handle = fopen($files[$i], "r");
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                $columns = count($data);
                if ($j <= 1) {
                    for ($c = 0; $c < $columns; $c++) {
                        if ($data[$c] === "") {
                            $columns = $c;
                            break;
                        }
                    }}
                for ($c = 0; $c < $columns; $c++) {
                    if (isset($data[$c]) && $data[$c] != "") $results[$j][$c] = utf8Encode($data[$c]);
                    else $results[$j][$c] = 0;
                }
                $j++;
            }
            fclose($handle);
            break;
        }
    }

    $output["countOfQuestions"] =  sizeof($results[1]) - 2 ;
    for ($i = 0; $i < $output["countOfQuestions"]; $i++) {
        $output["QNA"][$i] = explode("; ", clearQuestion($results[1][$i])); //here we get Questions [0] and answers [1..n]
    }

    $denum = 0;
    for ($i = 2; $i < sizeof($results); $i++) {
        if ($results[$i][$needleQuestion] == $needleValue || str_contains_in_concat($results[$i][$needleQuestion], $needleValue)) {
            for ($j = 0; $j < sizeof($results[$i])-2; $j++) {
                if (str_contains($results[$i][$j],"; ")) {
                    $output["multiresults"][$j][$i - 2] = explode("; ", clearQuestion($results[$i][$j])); //multi choice
                    $output["results"][$j][$i - 2] = -2;
                }
                else $output["results"][$j][$i - 2 - $denum] = clearQuestion($results[$i][$j]); //only one choice
            }
        }
        else $denum++;
    }
    $results = null;


    for ($i = 0; $i < $output["countOfQuestions"]; $i++) {
        if (!is_array($output["results"][$i])) {
            $output["relative"]["answer"] = -1;
            return $output;
        }
        else {
            $output["counts"][$i] = array_count_values($output["results"][$i]);
            for ($j = 0; $j < sizeof($output["results"][$i]); $j++)
                if ($output["results"][$i][$j] == -2)
                    for ($k = 0; $k < sizeof($output["multiresults"][$i][$j]); $k++)
                        $output["multicounts"][$i][intval($output["multiresults"][$i][$j][$k])] += 1;
        }
    }

    for ($i = 0; $i < sizeof($output["counts"]) - 1; $i++) {
        $free = 0;
        $n = 0;
        $output["type"][$i] = get_type($output["sid"], $i);
        for ($j = 0; $j < sizeof($output["QNA"][$i]) - 1; $j++) {
            if ($output["QNA"][$i][1] != "offene Frage") {
                if (!isset($output["counts"][$i][$j])) $output["counts"][$i][$j] = 0;
                if (!isset($output["multicounts"][$i][$j])) $output["multicounts"][$i][$j] = 0;
                $output["counts"]["assign"][$i][$output["QNA"][$i][$j + 1]] = $output["counts"][$i][$j];
                $output["counts"]["assign"][$i][$output["QNA"][$i][$j + 1]] += $output["multicounts"][$i][$j];
                $n += $output["counts"][$i][$j];
            } else { //offene Fragen
                $output["counts"]["assign"][$i][0] = -1;
                for ($k = 0; $k < sizeof($output["results"][$i]); $k++) {
                    if ($output["results"][$i][$k] != 0) {
                        $free++;
                    }
                    $output["open"][$i][$k] = $output["results"][$i][$k];
                    $n++;
                }
                $output["open"][$i]["count"] = $free;
            }
        }
        $output["n"] = $n;
    }
    $output["relative"]["question"] = $output["QNA"][$needleQuestion][0];
    $output["relative"]["answer"] = $output["QNA"][$needleQuestion][$needleValue + 1];

    return $output;
}

function str_contains_in_concat($haystack, $needle) {
    return in_array($needle, explode("; ", $haystack));
}
//loadRelativeResults("präsenzseminare", 7, 5)["results"][5][7];

function clearQuestion($string) {
    //this function clears the question from the strings "single:", "multi:", and "free:"
    return
        str_replace("free: ", "",
            str_replace("single: ", "",
                str_replace("multi: ", "",
                    str_replace("free:", "",
                        str_replace("single:", "",
                            str_replace("multi:", "",
                                $string))))));
}

function getSurveyHeads($surveys, $thisSurveyNumber) {
    for ($i = 2; $i < sizeof($surveys[$thisSurveyNumber]); $i++) {

        if (sizeof($surveys[$thisSurveyNumber][$i]) > 0 ) { //check if not empty
            $thisSurveys["types"][$i] = $surveys[$thisSurveyNumber][$i][0] ;
            $thisSurveys["heads"][$i] = $surveys[$thisSurveyNumber][$i][1] ;
        }
    }
    return $thisSurveys;
}

if (isset($_GET["sid"]) && $_GET["sid"] != "") {
    loadResults($_GET["sid"]);
}
?>