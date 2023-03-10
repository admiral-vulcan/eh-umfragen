<?php
require_once ("convertTimeFormatInString.php");

if (!isset($surveys)) $surveys = [];
if (!isset($thisSurveyNumber)) $thisSurveyNumber = [];
$target = "all"; //studs or empl or both or all
if (str_contains(strtolower($surveys[$thisSurveyNumber][1][0]), "stud") && str_contains(strtolower($surveys[$thisSurveyNumber][1][0]), "mitarb")) $target = "both";
elseif (str_contains(strtolower($surveys[$thisSurveyNumber][1][0]), "stud")) $target = "studs";
elseif (str_contains(strtolower($surveys[$thisSurveyNumber][1][0]), "mitarb")) $target = "empl";
elseif (str_contains(strtolower($surveys[$thisSurveyNumber][1][0]), "all")) $target = "all";

if ($target === "studs") $targettext = "Studierende der EH-Ludwigsburg";
elseif ($target === "mitarb") $targettext = "Mitarbeitende der EH-Ludwigsburg";
elseif ($target === "both") $targettext = "Studierende und Mitarbeitende der EH-Ludwigsburg";
else $targettext = "Alle Personen";

echo "<section id='intro'><header>"; // class='not-selectable'
$thisSurveyNumber = -1;
for ($i = 0; $i < sizeof($surveys); $i++) {
    if (array_search(str_replace("_", " ", $_GET["survey"]), $surveys[$i][0]) === 1) {
        $thisSurveyNumber = $i;
    }
}

$thisid = utf8Encode($surveys[$thisSurveyNumber][0][0]);
$inactivesince = get_inactivesince($thisid);
$inactivesince = date("d. ", $inactivesince) . translate(date("F", $inactivesince), "en", "de") . date(" Y", $inactivesince) . " um " . date("H:i", $inactivesince) . " Uhr.";
if (strtolower($GLOBALS["lang"]) == "en") $inactivesince = convertTimeFormatInString($inactivesince);
$since = get_since($thisid);
$since = date("d. ", $since) . translate(date("F", $since), "en", "de") . date(" Y", $since) . " um " . date("H:i", $since) . " Uhr.";
if (strtolower($GLOBALS["lang"]) == "en") $since = convertTimeFormatInString($since);
$wasactive = $inactivesince - $since;
$wasactive = date("d. ", $wasactive) . translate(date("F", $wasactive), "en", "de") . date(" Y", $wasactive) . " um " . date("H:i", $wasactive) . " Uhr.";
if (strtolower($GLOBALS["lang"]) == "en") $wasactive = convertTimeFormatInString($wasactive);
$hasresults = get_hasresults($thisid);
$backview = false;
if (isset($_GET["forceresults"]) && $_GET["forceresults"] == 1) $hasresults = 1;
if (isset($_GET["backview"]) && $_GET["backview"] == 1) {
    $hasresults = 0;
    $backview = true;
}
echo "<h1>eh-umfragen.de - " . translate("Eure Umfragen", "de", $GLOBALS["lang"]) . "</h1>";
echo zitat("lebenswirklichkeit");

?>


<?php
$title = translate($surveys[$thisSurveyNumber][0][1], "de", $GLOBALS["lang"]);
$titleDE = $surveys[$thisSurveyNumber][0][1];
$description1 = translate($surveys[$thisSurveyNumber][0][2], "de", $GLOBALS["lang"]);
$description2 = translate($surveys[$thisSurveyNumber][0][3], "de", $GLOBALS["lang"]);
$description3 = translate($surveys[$thisSurveyNumber][0][4], "de", $GLOBALS["lang"]);
$description4 = translate($surveys[$thisSurveyNumber][0][5], "de", $GLOBALS["lang"]);
echo "<h2>#" . $thisid . " " . $title;
if ($hasresults == 1) {
    $results = loadResults($thisid);
    echo translate(": Ergebnisse, n=" . $results["n"], "de", $GLOBALS["lang"]);
}
if (isset($_GET["draft"]) && $_GET["draft"] == "1") echo " (Entwurf, bitte nicht ausf??llen!)";
if ($backview) echo " (" . translate("R??ckschau einer geschlossenen Umfrage, bitte nicht ausf??llen!", "de", $GLOBALS["lang"]) . ")";
echo "</h2>";

if ($description1 != "") {
    echo "<h3>" . $description1 . "</h3>";
}

if ($description3 != "") {
    if ($description2 != "") {
        echo "<h3>" . $description2 . "</h3>";
    }
} else {
    if ($description2 != "") {
        echo $description2 . "<br>";
    }
}
if ($description3 != "") {
    echo $description3 . "<br>";


    if ($description4 != "") {
        echo $description4 . "<br>";
    }
    echo "";
}

echo translate("Zielgruppe: " . $targettext, "de", $GLOBALS["lang"]) . "<br>";
echo translate("Ge??ffnet seit dem " . $since, "de", $GLOBALS["lang"]) . "<div id='choose_anchor'></div>";
if ($inactivesince > 0) echo translate("Geschlossen seit dem " . $inactivesince, "de", $GLOBALS["lang"]) . "<br>";
if ($hasresults == 1) echo "<div class='printmenot'><br><a href='/?survey=" . $titleDE . "&backview=1'>" . translate("Geschlossene Umfrage nochmals anzeigen", "de", $GLOBALS["lang"]) . "</a>&emsp;||&emsp;<a onclick=\"printWithoutWeather(); window.print('%SCRIPTURL{view}%/%BASEWEB%/%BASETOPIC%?cover=print'); return false;\" style=\"cursor: pointer;\">" . translate("Diese Seite drucken", "de", $GLOBALS["lang"]) . "</a>&emsp;||&emsp;<a href='" . $results["file"] . "'>" . translate("Rohdaten herunterladen", "de", $GLOBALS["lang"]) . "</a><br><br>" . translate("Infos: <br>Diese Seite ist f??r die Darstellung am PC / Laptop optimiert. <br>Der Druck gelingt im hellen Design am besten.", "de", $GLOBALS["lang"]) . "</div>";
if ($backview) echo "<div class='printmenot'><br><a href='/?survey=" . $titleDE . "'>".translate("Zur??ck zur Auswertung", "de", $GLOBALS["lang"]) . "</a><br><br>".translate("Infos: <br>Diese Seite ist eine R??ckschau der urspr??nglichen Umfrage. Sie kann nicht mehr abgegeben werden.", "de", $GLOBALS["lang"]) . "</div>";
echo "<br>";
?>

<?php
$label_id = 0;
$denum = 0;

echo "</header><form action='' method='post'>";

if (((isset($_GET["draft"]) && $_GET["draft"] == 1) || $thisid != 0 && get_active($thisid) != 0 && !((isset($_GET["forceresults"]) && $_GET["forceresults"] == 1))) || $backview) {      //check if survey is set active

    $lastFollowUpNum = 0;
    $thisFollowUpNum = 0;

    for ($i = 2; $i < sizeof($surveys[$thisSurveyNumber]); $i++) {

        $thisType = extractNumber($surveys[$thisSurveyNumber][$i][0])["string"];
        $thisFollowUpNum = extractNumber($surveys[$thisSurveyNumber][$i][0])["number"];


        if (sizeof($surveys[$thisSurveyNumber][$i]) > 0) { //check if not empty


            if ($thisFollowUpNum > 0 && $lastFollowUpNum == 0) {
                $lastFollowUpNum = $thisFollowUpNum;
                echo "<div class='toggleDiv' style='display: block' id='toggle". $i . "'>";
            }
            elseif ($lastFollowUpNum > 0) {
                echo "<div class='toggleDiv' style='display: none' id='toggle". $i . "'>";
                $lastFollowUpNum--;
            }
            else echo "<div class='toggleDiv' style='display: block' id='toggle". $i . "'>";


            if ($thisType === "gruppe") {
                echo "<input type='hidden' name='" . $i - $denum . "' value='-1' />"; // in case of empty answer
                echo '<div><p class="poll">' .  translate($surveys[$thisSurveyNumber][$i][1], 'de', $GLOBALS["lang"]) . '<select name="' .
                    $i - $denum . '" id="' . $surveys[$thisSurveyNumber][$i][1] .
                    '" title="' . $surveys[$thisSurveyNumber][$i][1] .
                    '" required><option id="none" value="none"  hidden disabled selected value>' . translate('Bitte ausw??hlen', 'de', $GLOBALS["lang"]) . '</option>';
                for ($j = 2; $j < sizeof($surveys[$thisSurveyNumber][$i]); $j++) {
                    echo '<option id="' . $label_id .
                        '" value="' . $j - 2 . '">' .
                        translate($surveys[$thisSurveyNumber][$i][$j], 'de', $GLOBALS["lang"]) . '</option>';
                    $label_id++;
                }
                echo '</p></optgroup></select></div>';
            } elseif ($thisType === "oder") {
                echo "<input type='hidden' name='" . $i - $denum . "' value='-1' />"; // in case of empty answer
                echo '<div><p class="poll">' . translate($surveys[$thisSurveyNumber][$i][1], 'de', $GLOBALS["lang"]) . '<br>';
                for ($j = 2; $j < sizeof($surveys[$thisSurveyNumber][$i]); $j++) {
                    echo '<input type="radio" id="' . $label_id . '" name="' .
                        $i - $denum . '" value="' . $j - 2 .
                        '" required><label for="' .
                        $label_id . '">' . translate($surveys[$thisSurveyNumber][$i][$j], 'de', $GLOBALS["lang"]) .
                        '</label>';
                    $label_id++;
                }
                echo '</p></div>';
            } elseif ($thisType === "und") {
                //echo "<input type='hidden' name='" . $i - $denum . "' value='-1' />"; // in case of empty answer
                echo '<div><p class="poll">' . translate($surveys[$thisSurveyNumber][$i][1], 'de', $GLOBALS["lang"]) . '<br>';
                for ($j = 2; $j < sizeof($surveys[$thisSurveyNumber][$i]); $j++) {
                    echo '<input type="checkbox" id="' . $label_id . '" name="' .
                        $i - $denum . 'x' . $j . '" value="' . $j - 2 . '"><label for="' .
                        $label_id . '">' . translate($surveys[$thisSurveyNumber][$i][$j], 'de', $GLOBALS["lang"]) .
                        '</label>';
                    $label_id++;
                }
                echo '</p></div>';
            } elseif ($thisType === "textfeld") {
                echo "<input type='hidden' name='" . $i - $denum . "' value='-1' />"; // in case of empty answer
                echo '<div><p class="poll">' . translate($surveys[$thisSurveyNumber][$i][1], 'de', $GLOBALS["lang"]) .
                    '<input placeholder="' . translate('Bitte ausf??llen', 'de', $GLOBALS["lang"]) . '" type="text" id="' .
                    $label_id . '" name="' . $i - $denum .
                    '"></p></div>'; //required?
                $label_id++;
            } elseif ($thisType === "info") {
                echo "<p>" . translate($surveys[$thisSurveyNumber][$i][1], 'de', $GLOBALS["lang"]) . "</p>";
                //$denum++; not working if this is here, don't know y :/
            } elseif ($thisType === "img") {
                echo "    <div class='gallery'>
    
                        <picture>
                    <source srcset='/images/" . $surveys[$thisSurveyNumber][$i][1] . ".avif' type='image/avif'>
    <img class='clickableIMG' src='/images/" . $surveys[$thisSurveyNumber][$i][1] . ".jpg' style='max-height:200px; max-width:200px;' alt='" . str_replace(["-thumb", "-hoch", "-quer"], ["", "", ""], pathinfo($surveys[$thisSurveyNumber][$i][1].".jpg", PATHINFO_FILENAME)) . "'>
    </picture>
    </div>";
                echo "<div id='fullpage' onclick='this.style.display=\"none\";'></div>";
                //$denum++; not working if this is here, don't know y :/
            }
            echo "</div>";
        }
    }
} elseif (get_active($thisid) == 0 && $hasresults == 0) {      //check if survey is set inactive
    echo "<br><br><br><p><b>" . translate("Danke f??r Dein Interesse!</b><br>Diese Umfrage ist geschlossen und war " . secondsToTime($wasactive) . " offen.<br>Schau bald wieder vorbei, wenn unsere Ergebnisse ver??ffentlicht sind.", "de", $GLOBALS["lang"]) . "</p><br><br><br>";
}


if (!((isset($_GET["draft"]) && $_GET["draft"] == "1") || $backview) && get_active($thisid) != 0 && !((isset($_GET["forceresults"]) && $_GET["forceresults"] == 1))) {
    echo "<br><br><input type='hidden' name='content' value='sendsurvey' />";
    echo "<input type='hidden' name='target' value='" . $target . "' />";
    echo '<div><p>' . translate('Bitte gib zum Schluss noch Deine studentische E-Mail-Adresse (@studnet.eh-ludwigsburg.de) ein.', 'de', $GLOBALS["lang"]) . ' <br>
&emsp;&emsp;<a><span tooltip="' . translate('Nur so k??nnen wir sicherstellen, dass nur Studierende der EH teilnehmen und niemand mehrfach teilnimmt.', 'de', $GLOBALS["lang"]) . '">' . translate('Warum das?', 'de', $GLOBALS["lang"]) . '</span></a><br>
&emsp;&emsp;<a><span tooltip="' . translate('Ja, denn wir speichern nur den Hash-Wert und nicht die Adresse selbst.', 'de', $GLOBALS["lang"]) . '">' . translate('Ist die Umfrage dann noch anonym?<', 'de', $GLOBALS["lang"]) . '/span></a><br>
&emsp;&emsp;<a href="/?content=mailinfo" target="_blank">' . translate('Klicke hier f??r mehr Informationen.', 'de', $GLOBALS["lang"]) . '</a>
<input placeholder="' . translate('Bitte ausf??llen', 'de', $GLOBALS["lang"]) . '" type="email" id="email" name="email" required></p></div>';
    echo "<input type='hidden' name='sid' value='" . $surveys[$thisSurveyNumber][0][0] . "' />";
    echo '<br><input type="submit" value="' . translate('Abschicken', 'de', $GLOBALS["lang"]) . '"></form></section>';



    /* TODO
     * Man muss eine neue Umfrage #0 erstmal komplett ??ffnen, damit sie in die Datenbank aufgenommen wird. BESCHEUERT!^^
     *
    */



    for ($i = 0; $i < sizeof($surveys); $i++) {
        $id = -1;
        $id_file = intval(utf8Encode($surveys[$i][0][0]));
        $id_db = get_survey_id($surveys[$i][0][1]);

        if ($id_file === $id_db) $id = $id_file;

        else {
            if ($id_file < 1 || $id_db < 1) $id = set_survey_id($surveys[$i][0][1]);
            else $id = $id_db;
            $surveys[$i][0][0] = $id;
            $files = glob("surveys/*.csv");
            $handle = fopen($files[$i], "w");
            for ($j = 0; $j < sizeof($surveys[$i]); $j++) {
                for ($k = 0; $k < sizeof($surveys[$i][$j]); $k++) {
                    $surveys[$i][$j][$k] = iconv("UTF-8", "Windows-1252", $surveys[$i][$j][$k]);
                }
                fputcsv($handle, $surveys[$i][$j], ";");
            }
            fclose($handle);
            chmod('results/' . $files[$i], 0775);

            $types[] = "";
            $questions[] = "";
            $options[] = "";
            $denum = 0;
            for ($j = 1; $j < sizeof($surveys[$i]); $j++) {
                if ($surveys[$i][$j][0] == "gruppe" or $surveys[$i][$j][0] == "und" or $surveys[$i][$j][0] == "oder" or $surveys[$i][$j][0] == "textfeld") {
                    $types[$j - 1 - $denum] = utf8Encode($surveys[$i][$j][0]);
                    //echo $types[$j-1];
                    $questions[$j - 1 - $denum] = utf8Encode($surveys[$i][$j][1]);
                    if (sizeof($surveys[$i][$j]) <= 2) $options[$j - 1 - $denum] = "offene Frage";
                    else {
                        for ($k = 2; $k < sizeof($surveys[$i][$j]); $k++) {
                            $options[$j - 1 - $denum] .= utf8Encode($surveys[$i][$j][$k]);
                            if ($surveys[$i][$j][$k + 1] != null) $options[$j - 1 - $denum] .= "; ";
                        }
                    }
                } else $denum++;
            }
            create_survey($surveys[$i][0][0], sizeof($questions), $types, $questions, $options);
        }
    }
}


/**
 * This is where the magic begins!
 *
 * aka result-drawer
 *
 * we work with $thisid
 *
 * */
if ($hasresults == 1) {
    $thisName = urldecode($_GET["survey"]);
    if (isset($_GET["leftQ"]) && $_GET["leftQ"] == "") unset($_GET["leftQ"]);
    if (isset($_GET["rightQ"]) && $_GET["rightQ"] == "") unset($_GET["rightQ"]);
    if (isset($_GET["leftA"]) && $_GET["leftA"] == "") unset($_GET["leftA"]);
    if (isset($_GET["rightA"]) && $_GET["rightA"] == "") unset($_GET["rightA"]);


    if (isset($_GET["leftQ"]) && $_GET["leftQ"] != "all_results") {
        $leftQ = urldecode($_GET["leftQ"]);
        if (!isset($_GET["leftA"])) $_GET["leftA"] = 0;
        $leftA = urldecode($_GET["leftA"]);
        $resultsLeft = loadRelativeResults($thisName, $leftQ, $leftA);

    } else $resultsLeft = $results;


    if (isset($_GET["rightQ"]) && $_GET["rightQ"] != "all_results") {
        $rightQ = urldecode($_GET["rightQ"]);
        if (isset($_GET["rightA"])) {
            $rightA = urldecode($_GET["rightA"]);
            $resultsRight = loadRelativeResults($thisName, $rightQ, $rightA);
        } else $resultsRight = $results;
    } else $resultsRight = $results;

    if (!isset($resultsLeft["n"])) $resultsLeft["n"] = "0; " . translate("Diese Option hat niemand ausgew??hlt.", "de", $GLOBALS["lang"]); // Wenn niemand so geantwortet hat.
    if (!isset($resultsRight["n"])) $resultsRight["n"] = "0; " . translate("Diese Option hat niemand ausgew??hlt.", "de", $GLOBALS["lang"]); // Wenn niemand so geantwortet hat.

    $thisSurveyHead = getSurveyHeads(loadSurveys(), $thisSurveyNumber);
    ?>


    <div style="display: table; width: 100%;">
        <div class="table-row">
            <div class="table-cell" id="left_form">
                <div id='left_choices'>
                    <label for="result_scheme_left_Q" class='printmenot'><?php echo translate("W??hle eine lineare Ansicht aller Ergebnisse oder Ergebnisse in Relation zu einer Antwort.", "de", $GLOBALS["lang"]); ?></label>
                    <select id="result_scheme_left_Q" name="result_scheme_left_Q">
                        <option value="all_results"><?php echo translate("Alle Ergebnisse", "de", $GLOBALS["lang"]); ?></option>
                        <?php
                        for ($i = 0; $i < sizeof($resultsLeft["QNA"]); $i++) {
                            if ($resultsLeft["QNA"][$i][1] != "offene Frage" && $resultsLeft["QNA"][$i][0] != "Umfrage abgegeben" && $resultsLeft["QNA"][$i][0] != "E-Mail ist validiert") { //dirty hack, never mind
                                $thisQ = translate($resultsLeft["QNA"][$i][0], "de", $GLOBALS["lang"]);
                                echo "<option value='" . $i . "' ";
                                if (isset($_GET["leftQ"]) && $i == $_GET["leftQ"]) echo "selected";
                                echo ">" . $thisQ . "</option>";
                            }
                        }
                        ?>

                    </select>
                    <?php
                    if (isset($_GET["leftQ"]) && $_GET["leftQ"] != "all_results") {
                        ?>
                        <select id="result_scheme_left_A" name="result_scheme_left_A">
                            <?php
                            for ($i = 1; $i < sizeof($resultsLeft["QNA"][$_GET["leftQ"]]); $i++) {
                                $thisA = translate($resultsLeft["QNA"][$_GET["leftQ"]][$i], "de", $GLOBALS["lang"]);
                                echo "<option value='" . $i - 1 . "' ";
                                if (isset($_GET["leftA"]) && $i - 1 == $_GET["leftA"]) echo "selected";
                                echo ">" . $thisA . "</option>";
                                ?>
                                <?php
                            }
                            ?>
                        </select>
                        <?php
                    }

                    ?>
                </div>
                <div id='left_n'>
                    <br>
                    <p style="bottom: 0;"><b>n=<?php echo $resultsLeft["n"]; ?></b></p><hr>
                </div>

                <?php
                $left_counts = -1;
                if (!isset($resultsLeft["relative"]["answer"]) || $resultsLeft["relative"]["answer"] != -1) {
                    $left_counts = sizeof($resultsLeft["counts"]) - 1;

                    $x = 0;
                    $foundInfo = false;
                    $headAt = 0;

                    for ($i = 0; $i < $left_counts; $i++) {
                        if (!isset($resultsLeft["counts"]["assign"][$i][0]) || $resultsLeft["counts"]["assign"][$i][0] != -1) { //&& $resultsLeft["QNA"][$i][0] != $resultsLeft["relative"]["question"]
                            echo "<div id='left_" . $i . "'>";

                            while ($x < sizeof($thisSurveyHead["heads"])) {
                                $x++;
                                if (isset($resultsLeft["QNA"][$i][0]) && isset($thisSurveyHead["heads"][$x]) && isEq($resultsLeft["QNA"][$i][0], $thisSurveyHead["heads"][$x])) {
                                    $headAt = $x;
                                    break;
                                }
                            }
                            while ($x >= 0) {
                                $x--;
                                if ($thisSurveyHead["types"][$x] === "info") {
                                    $foundInfo = true;
                                    break;
                                }
                                elseif (
                                    $thisSurveyHead["types"][$x] === "gruppe" ||
                                    $thisSurveyHead["types"][$x] === "oder" ||
                                    $thisSurveyHead["types"][$x] === "und" ||
                                    $thisSurveyHead["types"][$x] === "textfeld"
                                ) {
                                    $foundInfo = false;
                                     $x = $headAt;
                                    break;
                                }
                            }
                            if ($foundInfo) {
                                $thisHead = "";
                                while ( isset($thisSurveyHead["types"][$x-1]) &&
                                $thisSurveyHead["types"][$x-1] === "info" &&
                                $x < sizeof($thisSurveyHead["heads"])
                            ) $x--;
                                while (
                                        $thisSurveyHead["types"][$x] === "info" &&
                                        $x < sizeof($thisSurveyHead["heads"])
                                ) {
                                    $thisHead .= $thisSurveyHead["heads"][$x] . "<br>";
                                    $x++;
                                }
                                $x = $headAt;
                                echo translate($thisHead, "de", $GLOBALS["lang"]);
                            }

                            echo "<hr><h2>" . translate($resultsLeft["QNA"][$i][0], "de", $GLOBALS["lang"]) . "</h2>";
                            if ($resultsLeft["type"][$i] == "multi") echo "<p>".translate("Mehrfachauswahl", "de", $GLOBALS["lang"])."</p>";
                            else echo "<p>".translate("Einfachauswahl", "de", $GLOBALS["lang"])."</p>"; //($resultsLeft["type"][$i] == "single")
                            ?>
                            <form>
                                <input type="radio" name="diagram_scheme" id="left_circle_<?php echo $i; ?>"
                                       value="1" checked>
                                <label class="graph_scheme" for="left_circle_<?php echo $i; ?>"
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Kreisdiagramm", "de", $GLOBALS["lang"]); ?></label>
                                <input type="radio" name="diagram_scheme" id="left_rectangle_<?php echo $i; ?>"
                                       value="1">
                                <label class="graph_scheme" for="left_rectangle_<?php echo $i; ?>"
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("S??ulendiagramm", "de", $GLOBALS["lang"]); ?></label>
                                <input type="radio" name="diagram_scheme" id="left_both_<?php echo $i; ?>"
                                       value="1">
                                <label class="graph_scheme" for="left_both_<?php echo $i; ?>"
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Beide Diagramme", "de", $GLOBALS["lang"]); ?></label>
                            </form>
                            <?php
                            if ($i == 0) echo "<br><br><br>"; //stupid workaround-bugfix-hack. o_O
                            echo '<br><img style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsLeft["counts"]["assign"][$i]) . '" src="data:image/png;base64,' . drawLegend($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                            echo '<br><div id="left_circle_graph_' . $i . '"><img alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircle($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" /></div>';
                            echo '<div id="left_rectangle_graph_' . $i . '" style="display: none"><img alt="'.translate('ein automatisch generiertes S??ulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangle($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" /></div>';
                            echo "<br>";
                            echo "</div><hr>";
                        } else {
                            echo "<div id='left_" . $i . "'>";
                            echo '<div id="left_circle_graph_' . $i . '" style="display: none"></div>';
                            echo '<div id="left_rectangle_graph_' . $i . '" style="display: none"></div>';
                            echo '<div id="left_circle_' . $i . '" style="display: none"></div>';
                            echo '<div id="left_rectangle_' . $i . '" style="display: none"></div>';
                            echo '<div id="left_both_' . $i . '" style="display: none"></div>';
                            echo "<hr><h2>" . translate($resultsLeft["QNA"][$i][0], "de", $GLOBALS["lang"]) . "</h2>";
                            if ($resultsLeft["open"][$i]["count"] == 1) echo "<h2>" . $resultsLeft["open"][$i]["count"] . " " . translate("Antwort", "de", $GLOBALS["lang"]) . ":</h2>";
                            echo "<h2>" . $resultsLeft["open"][$i]["count"] . " " . translate("Antworten", "de", $GLOBALS["lang"]) . ":</h2>";
                            $k = 0;
                            for ($j = 0; $j < sizeof($resultsLeft["open"][$i]); $j++) {
                                if (isset($resultsLeft["open"][$i][$j]) && $resultsLeft["open"][$i][$j] != 0) {
                                    $k++;
                                    echo "<p>#" . $k . ": " . translate($resultsLeft["open"][$i][$j], "de", $GLOBALS["lang"]) . "</p><br>";
                                }
                            }
                            echo "<br>";
                            echo "</div><hr>";
                        }
                    }/*
                    for ($i = 0; $i < sizeof($resultsLeft["counts"]) - 1; $i++) {
                        if ($resultsLeft["counts"]["assign"][$i][0] == -1) {
                            echo "<div id='left_" . $i . "'>";
                            echo "<hr><h2>" . $resultsLeft["QNA"][$i][0] . "</h2>";
                            echo "<h2>" . $resultsLeft["open"][$i]["count"] . " Antworten:</h2>";
                            $k = 0;
                            for ($j = 0; $j < sizeof($resultsLeft["open"][$i]); $j++) {
                                if ($resultsLeft["open"][$i][$j] != 0) {
                                    $k++;
                                    echo "<p>#" . $k . ": " . $resultsLeft["open"][$i][$j] . "</p><br>";
                                }
                            }
                            echo "<br>";
                            echo "</div><hr>";
                        }
                    }*/
                } else echo "<div id='left_0'><hr></div>"
                ?>

            </div>
            <div class="table-cell" id="right_form">
                <div id='right_choices'>
                    <label for="result_scheme_right_Q" class='printmenot'><?php echo translate("W??hle eine lineare Ansicht aller Ergebnisse oder Ergebnisse in Relation zu einer Antwort.", "de", $GLOBALS["lang"]); ?></label>
                    <select id="result_scheme_right_Q" name="result_scheme_right_Q">
                        <option value="all_results"><?php echo translate("Alle Ergebnisse", "de", $GLOBALS["lang"]); ?></option>
                        <?php
                        for ($i = 0; $i < sizeof($resultsRight["QNA"]); $i++) {
                            if ($resultsRight["QNA"][$i][1] != "offene Frage" && $resultsRight["QNA"][$i][0] != "Umfrage abgegeben" && $resultsRight["QNA"][$i][0] != "E-Mail ist validiert") { //das muss sauberer gehen aber hey, wenns tut
                                $thisQ = $resultsRight["QNA"][$i][0];
                                echo "<option value='" . $i . "' ";
                                if (isset($_GET["rightQ"]) && $i == $_GET["rightQ"]) echo "selected";
                                echo ">" . $thisQ . "</option>";
                            }
                        }
                        ?>

                    </select>
                    <?php
                    if (isset($_GET["rightQ"]) && $_GET["rightQ"] != "all_results") {
                        ?>
                        <select id="result_scheme_right_A" name="result_scheme_right_A">
                            <?php
                            for ($i = 1; $i < sizeof($resultsRight["QNA"][$_GET["rightQ"]]); $i++) {
                                $thisA = $resultsRight["QNA"][$_GET["rightQ"]][$i];
                                echo "<option value='" . $i - 1 . "' ";
                                if (isset($_GET["rightA"]) && $i - 1 == $_GET["rightA"]) echo "selected";
                                echo ">" . $thisA . "</option>";
                                ?>
                                <?php
                            }
                            ?>
                        </select>
                        <?php
                    }

                    ?>
                </div>
                <div id='right_n'>
                    <br>
                    <p style="bottom: 0;"><b>n=<?php echo $resultsRight["n"]; ?></b></p><hr>
                </div>

                <?php
                $right_counts = -1;
                if (!isset($resultsRight["relative"]["answer"]) || $resultsRight["relative"]["answer"] != -1) {
                    $right_counts = sizeof($resultsRight["counts"]) - 1;

                    $x = 0;
                    $foundInfo = false;
                    $headAt = 0;

                    for ($i = 0; $i < $right_counts; $i++) {
                        if (!isset($resultsRight["counts"]["assign"][$i][0]) || $resultsRight["counts"]["assign"][$i][0] != -1) { //&& $resultsRight["QNA"][$i][0] != $resultsRight["relative"]["question"]
                            echo "<div id='right_" . $i . "'>";

                            while ($x < sizeof($thisSurveyHead["heads"])) {
                                $x++;
                                if (isset($resultsRight["QNA"][$i][0]) && isset($thisSurveyHead["heads"][$x]) && isEq($resultsRight["QNA"][$i][0], $thisSurveyHead["heads"][$x])) {
                                    $headAt = $x;
                                    break;
                                }
                            }
                            while ($x >= 0) {
                                $x--;
                                if ($thisSurveyHead["types"][$x] === "info") {
                                    $foundInfo = true;
                                    break;
                                }
                                elseif (
                                    $thisSurveyHead["types"][$x] === "gruppe" ||
                                    $thisSurveyHead["types"][$x] === "oder" ||
                                    $thisSurveyHead["types"][$x] === "und" ||
                                    $thisSurveyHead["types"][$x] === "textfeld"
                                ) {
                                    $foundInfo = false;
                                    $x = $headAt;
                                    break;
                                }
                            }
                            if ($foundInfo) {
                                $thisHead = "";
                                while ( isset($thisSurveyHead["types"][$x-1]) &&
                                    $thisSurveyHead["types"][$x-1] === "info" &&
                                    $x < sizeof($thisSurveyHead["heads"])
                                ) $x--;
                                while (
                                    $thisSurveyHead["types"][$x] === "info" &&
                                    $x < sizeof($thisSurveyHead["heads"])
                                ) {
                                    $thisHead .= $thisSurveyHead["heads"][$x] . "<br>";
                                    $x++;
                                }
                                $x = $headAt;
                                echo translate($thisHead, "de", $GLOBALS["lang"]);
                            }

                            echo "<hr><h2>" . translate($resultsRight["QNA"][$i][0], "de", $GLOBALS["lang"]) . "</h2>";
                            if ($resultsRight["type"][$i] == "multi") echo "<p>".translate("Mehrfachauswahl", "de", $GLOBALS["lang"])."</p>";
                            else echo "<p>".translate("Einfachauswahl", "de", $GLOBALS["lang"])."</p>"; //($resultsRight["type"][$i] == "single")
                            ?>
                            <form>
                                <input type="radio" name="diagram_scheme" id="right_circle_<?php echo $i; ?>"
                                       value="1" checked>
                                <label class="graph_scheme" for="right_circle_<?php echo $i; ?>"
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Kreisdiagramm", "de", $GLOBALS["lang"]); ?></label>
                                <input type="radio" name="diagram_scheme" id="right_rectangle_<?php echo $i; ?>"
                                       value="1">
                                <label class="graph_scheme" for="right_rectangle_<?php echo $i; ?>"
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("S??ulendiagramm", "de", $GLOBALS["lang"]); ?></label>
                                <input type="radio" name="diagram_scheme" id="right_both_<?php echo $i; ?>"
                                       value="1">
                                <label class="graph_scheme" for="right_both_<?php echo $i; ?>"
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Beide Diagramme", "de", $GLOBALS["lang"]); ?></label>
                            </form>
                            <?php
                            if ($i == 0) echo "<br><br><br>"; //stupid workaround-bugfix-hack. o_O
                            echo '<br><img style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsRight["counts"]["assign"][$i]) . '" src="data:image/png;base64,' . drawLegend($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                            echo '<br><div id="right_circle_graph_' . $i . '"><img alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircle($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" /></div>';
                            echo '<div id="right_rectangle_graph_' . $i . '" style="display: none"><img alt="'.translate('ein automatisch generiertes S??ulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangle($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" /></div>';
                            echo "<br>";
                            echo "</div><hr>";
                        } else {
                            echo "<div id='right_" . $i . "'>";
                            echo '<div id="right_circle_graph_' . $i . '" style="display: none"></div>';
                            echo '<div id="right_rectangle_graph_' . $i . '" style="display: none"></div>';
                            echo '<div id="right_circle_' . $i . '" style="display: none"></div>';
                            echo '<div id="right_rectangle_' . $i . '" style="display: none"></div>';
                            echo '<div id="right_both_' . $i . '" style="display: none"></div>';
                            echo "<hr><h2>" . translate($resultsRight["QNA"][$i][0], "de", $GLOBALS["lang"]) . "</h2>";
                            if ($resultsRight["open"][$i]["count"] == 1) echo "<h2>" . $resultsRight["open"][$i]["count"] . " " . translate("Antwort", "de", $GLOBALS["lang"]) . ":</h2>";
                            echo "<h2>" . $resultsRight["open"][$i]["count"] . " " . translate("Antworten", "de", $GLOBALS["lang"]) . ":</h2>";
                            $k = 0;
                            for ($j = 0; $j < sizeof($resultsRight["open"][$i]); $j++) {
                                if (isset($resultsRight["open"][$i][$j]) && $resultsRight["open"][$i][$j] != 0) {
                                    $k++;
                                    echo "<p>#" . $k . ": " . translate($resultsRight["open"][$i][$j], "de", $GLOBALS["lang"]) . "</p><br>";
                                }
                            }
                            echo "<br>";
                            echo "</div><hr>";
                        }
                    }/*
                    for ($i = 0; $i < sizeof($resultsRight["counts"]) - 1; $i++) {
                        if ($resultsRight["counts"]["assign"][$i][0] == -1) {
                            echo "<div id='right_" . $i . "'>";
                            echo "<hr><h2>" . $resultsRight["QNA"][$i][0] . "</h2>";
                            echo "<h2>" . $resultsRight["open"][$i]["count"] . " Antworten:</h2>";
                            $k = 0;
                            for ($j = 0; $j < sizeof($resultsRight["open"][$i]); $j++) {
                                if ($resultsRight["open"][$i][$j] != 0) {
                                    $k++;
                                    echo "<p>#" . $k . ": " . $resultsRight["open"][$i][$j] . "</p><br>";
                                }
                            }
                            echo "<br>";
                            echo "</div><hr>";
                        }
                    }*/
                } else echo "<div id='right_0'><hr></div>"
                ?>

            </div>
        </div>
    </div>

    <script src="assets/js/src/js.cookie.min.js"></script>
    <script type="application/javascript">
        let forceresults = parseInt("<?php if ($_GET["forceresults"] && intval($_GET["forceresults"]) == 1) echo 1; else echo 0; ?>");
        if (forceresults === 1) {
            forceresults = "&forceresults=1";
        }
        else forceresults = "";

        document.getElementById('result_scheme_left_Q').onchange = result_scheme_left_Q;
        if (document.getElementById('result_scheme_left_A') !== null) {
            document.getElementById('result_scheme_left_A').onchange = result_scheme_left_A;
        }

        function result_scheme_left_Q() {
            window.location = '<?php echo "https://" . $_SERVER['HTTP_HOST'] . "?survey=" . $thisName . "&rightQ=" . $_GET["rightQ"] . "&rightA=" . $_GET["rightA"] . "&leftQ=" ?>' + document.getElementById("result_scheme_left_Q").value + "&leftA=0" + forceresults + "#choose_anchor";
        }

        function result_scheme_left_A() {
            window.location = '<?php echo "https://" . $_SERVER['HTTP_HOST'] . "?survey=" . $thisName . "&rightQ=" . $_GET["rightQ"] . "&rightA=" . $_GET["rightA"] . "&leftQ=" . $_GET["leftQ"] . "&leftA=" ?>' + document.getElementById("result_scheme_left_A").value + forceresults + "#choose_anchor";
        }


        document.getElementById('result_scheme_right_Q').onchange = result_scheme_right_Q;
        if (document.getElementById('result_scheme_right_A') !== null) {
            document.getElementById('result_scheme_right_A').onchange = result_scheme_right_A;
        }

        function result_scheme_right_Q() {
            window.location = '<?php echo "https://" . $_SERVER['HTTP_HOST'] . "?survey=" . $thisName . "&leftQ=" . $_GET["leftQ"] . "&leftA=" . $_GET["leftA"] . "&rightQ=" ?>' + document.getElementById("result_scheme_right_Q").value + "&rightA=0" + forceresults + "#choose_anchor";
        }

        function result_scheme_right_A() {
            window.location = '<?php echo "https://" . $_SERVER['HTTP_HOST'] . "?survey=" . $thisName . "&leftQ=" . $_GET["leftQ"] . "&leftA=" . $_GET["leftA"] . "&rightQ=" . $_GET["rightQ"] . "&rightA=" ?>' + document.getElementById("result_scheme_right_A").value + forceresults + "#choose_anchor";
        }

        var left_choices = document.getElementById('left_choices');
        var right_choices = document.getElementById('right_choices');

        if (left_choices.clientHeight > right_choices.clientHeight) {
            right_choices.style.height = left_choices.clientHeight.toString() + "px";
        } else if (left_choices.clientHeight < right_choices.clientHeight) {
            left_choices.style.height = right_choices.clientHeight.toString() + "px";
        }


        let resultsCount = parseInt(<?php echo sizeof($results["counts"]) - 1; ?>);

        let left = [];
        let right = [];
        for (let i = 0; i < resultsCount; i++) {
            left[i] = document.getElementById('left_' + i.toString());
            right[i] = document.getElementById('right_' + i.toString());

            if (left[i].clientHeight > right[i].clientHeight) {
                right[i].style.height = left[i].clientHeight.toString() + "px";
            } else if (left[i].clientHeight < right[i].clientHeight) {
                left[i].style.height = right[i].clientHeight.toString() + "px";
            }

        }


        let left_counts = parseInt(<?php echo $left_counts; ?>);

        window.addEventListener("load", function() {
            <?php
            for ($i = 0; $i < $left_counts; $i++) {
            ?>

            var left_circle<?php echo $i; ?> = document.getElementById('left_circle_<?php echo $i; ?>');
            var left_rectangle<?php echo $i; ?> = document.getElementById('left_rectangle_<?php echo $i; ?>');
            var left_both<?php echo $i; ?> = document.getElementById('left_both_<?php echo $i; ?>');

            left_circle<?php echo $i; ?>.onclick = circleLeft<?php echo $i; ?>;
            left_rectangle<?php echo $i; ?>.onclick = rectangleLeft<?php echo $i; ?>;
            left_both<?php echo $i; ?>.onclick = bothLeft<?php echo $i; ?>;

            function circleLeft<?php echo $i; ?>() {
                document.getElementById("left_circle_graph_<?php echo $i; ?>").style.display = "block";
                document.getElementById("left_circle_graph_<?php echo $i; ?>").style.float = "unset";
                document.getElementById("left_rectangle_graph_<?php echo $i; ?>").style.display = "none";
            }
            function rectangleLeft<?php echo $i; ?>() {
                document.getElementById("left_circle_graph_<?php echo $i; ?>").style.display = "none";
                document.getElementById("left_rectangle_graph_<?php echo $i; ?>").style.display = "block";
            }
            function bothLeft<?php echo $i; ?>() {
                document.getElementById("left_circle_graph_<?php echo $i; ?>").style.display = "block";
                document.getElementById("left_circle_graph_<?php echo $i; ?>").style.float = "left";
                document.getElementById("left_rectangle_graph_<?php echo $i; ?>").style.display = "block";
            }


            <?php
            }
            ?>

            <?php
            for ($i = 0; $i < $right_counts; $i++) {
            ?>

            var right_circle<?php echo $i; ?> = document.getElementById('right_circle_<?php echo $i; ?>');
            var right_rectangle<?php echo $i; ?> = document.getElementById('right_rectangle_<?php echo $i; ?>');
            var right_both<?php echo $i; ?> = document.getElementById('right_both_<?php echo $i; ?>');

            right_circle<?php echo $i; ?>.onclick = circleRight<?php echo $i; ?>;
            right_rectangle<?php echo $i; ?>.onclick = rectangleRight<?php echo $i; ?>;
            right_both<?php echo $i; ?>.onclick = bothRight<?php echo $i; ?>;

            function circleRight<?php echo $i; ?>() {
                document.getElementById("right_circle_graph_<?php echo $i; ?>").style.display = "block";
                document.getElementById("right_circle_graph_<?php echo $i; ?>").style.float = "unset";
                document.getElementById("right_rectangle_graph_<?php echo $i; ?>").style.display = "none";
            }
            function rectangleRight<?php echo $i; ?>() {
                document.getElementById("right_circle_graph_<?php echo $i; ?>").style.display = "none";
                document.getElementById("right_rectangle_graph_<?php echo $i; ?>").style.display = "block";
            }
            function bothRight<?php echo $i; ?>() {
                document.getElementById("right_circle_graph_<?php echo $i; ?>").style.display = "block";
                document.getElementById("right_circle_graph_<?php echo $i; ?>").style.float = "right";
                document.getElementById("right_rectangle_graph_<?php echo $i; ?>").style.display = "block";
            }


            <?php
            }
            ?>






//this must be here, color_scheme_handler wouldn't work otherwise :/
            var ex1 = document.getElementById('auto');
            var ex2 = document.getElementById('light');
            var ex3 = document.getElementById('dark');
            var ex4 = document.getElementById('contrast');

            ex1.onclick = auto;
            ex2.onclick = light;
            ex3.onclick = dark;
            ex4.onclick = contrast;
        });
    </script>
    <div style="clear: both"></div>

    <?php
}

function isEq($str1, $str2) {
    //returns if two strings are equal, even if their charsets differ or if they have new lines

    $str1 = strval($str1);
    $str2 = strval($str2);

    $str1 = strtolower($str1);
    $str2 = strtolower($str2);

    $str1 = utf8Encode($str1);
    $str2 = utf8Encode($str2);

    $str1 = trim(preg_replace('/\s+/', '', $str1));
    $str2 = trim(preg_replace('/\s+/', '', $str2));

    return ($str1 == $str2);
}
function extractNumber($string) {
    $number = 0;
    preg_match('/\d+$/', $string, $matches);
    if (isset($matches[0])) $number = intval($matches[0]);
    $string = preg_replace('/\d+$/', '', $string);
    return array(
        "string" => $string,
        "number" => $number
    );
}
?>




<script type="application/javascript">



    // Get all elements with the class "toggleDiv"
    var toggleElements = document.getElementsByClassName("toggleDiv");

    // Add an event listener to each input element to listen for changes
    for(var i = 0; i < toggleElements.length; i++) {
        var inputs = toggleElements[i].getElementsByTagName("input");
        for(var j = 0; j < inputs.length; j++) {
            inputs[j].addEventListener("change", toggleDiv);
        }
        var selects = toggleElements[i].getElementsByTagName("select");
        for(var j = 0; j < selects.length; j++) {
            selects[j].addEventListener("change", toggleDiv);
        }

    }

    // The function that will be called when an input element changes
    function toggleDiv() {
        var divId = this.name; // The id of the div to toggle is the same as the name of the input element
        var divIdArr = divId.split("x");
        divId = divIdArr[0];
        var divToToggle = document.getElementById("toggle" + divId);
        console.log(divId);
        if (divToToggle.nextElementSibling.nodeType === Node.ELEMENT_NODE) {
            if (divToToggle.nextElementSibling.classList.contains("toggleDiv")) {
                divToToggle.nextElementSibling.style.display = "block";
            }
        }
    }



</script>