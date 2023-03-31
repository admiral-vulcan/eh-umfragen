<?php
/**
 * surveys[][][]
 * Dim1: Umfragedatei, Dim2: Reihe, Dim3: Spaltenkästchen
 * [datei][0][0] = Titel
 * [datei][0][1] = Beschreibung
 * [datei][0][...] = zusätzliche Infos
 * [datei][1...][0] = Fragetyp
 * [datei][1...][1] = Frage
 * [datei][1...][2...] = Optionen
 */

$surveys = [];
if (isset($_GET["draft"]) == "1") $files = glob("survey-drafts/*.csv");
else $files = glob("surveys/*.csv");

for ($i = 0; $i < sizeof($files); $i++) {
    $j = 0; //this row
    $handle = fopen($files[$i], "r");
    $globalsurveys[$i]['filename'] = basename($files[$i]);
    while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
        $columns = count($data);
        for ($c = 0; $c < $columns; $c++) {
            if ($data[$c] === "") {
                $columns = $c;
                break;
            }
        }
        if ($columns !== 0) {
            for ($c = 0; $c < $columns; $c++) {
                $surveys[$i][$j][$c] = utf8Encode($data[$c]);
                if ($c > 0) {
                    if (
                        str_contains("multiple_choice", $surveys[$i][$j][0]) ||
                        str_contains("single_choice", $surveys[$i][$j][0]) ||
                        str_contains("dropdown", $surveys[$i][$j][0]) ||
                        str_contains("free_text", $surveys[$i][$j][0]) ||
                        str_contains("description", $surveys[$i][$j][0])
                    ) $surveys[$i][$j][$c] = translate($surveys[$i][$j][$c], "de", $GLOBALS["lang"]);
                }
            }
            $j++;
        }
    }
    fclose($handle);
}

//for future use
function loadSurveys() {
    $surveys = [];
    if (isset($_GET["draft"]) && $_GET["draft"] == "1") $files = glob("survey-drafts/*.csv");
    else $files = glob("surveys/*.csv");

    for ($i = 0; $i < sizeof($files); $i++) {
        $j = 0; //this row
        $handle = fopen($files[$i], "r");
        while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
            $columns = count($data);
            for ($c = 0; $c < $columns; $c++) {
                if ($data[$c] === "") {
                    $columns = $c;
                    break;
                }
            }
            if ($columns !== 0) {
                for ($c = 0; $c < $columns; $c++) {
                    $surveys[$i][$j][$c] = utf8Encode($data[$c]);
                }
                $j++;
            }
        }
        fclose($handle);
    }
    return $surveys;
}
//testing
/*
echo "Umfragetitel: ";
echo $surveys[0][0][0];
echo "<br />\n";

echo "Beschreibung: ";
echo $surveys[0][0][1];
echo "<br />\n";

echo "Erste Frage: ";
echo $surveys[0][1][0];
echo "<br />\n";

echo "Erste Option der ersten Frage: ";
echo $surveys[0][1][1];
echo "<br />\n";
*/
?>