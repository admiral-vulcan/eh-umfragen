<?php

use EHUmfragen\DatabaseModels\Surveys;
use EHUmfragen\DatabaseModels\Responses;
use EHUmfragen\DatabaseModels\Questions;
use EHUmfragen\DatabaseModels\QuestionChoices;

require_once ("convertTimeFormatInString.php");                                                     /** ???????????????????????? */

$allSurveys = new Surveys();
$allResponses = new Responses();
$allQuestions = new Questions();
$allQuestionChoices = new QuestionChoices();

$survey_id = $allSurveys->getSurveysIdsBy(htmlspecialchars($_GET["survey"]), "title")[0];

echo "<section id='intro'><header>";
echo "<h1>eh-umfragen.de - " . translate("Eure Umfragen", "de", $GLOBALS["lang"]) . "</h1>";
echo zitat("lebenswirklichkeit");

if ($survey_id === null) {
    //if survey not found
    echo "<head><meta http-equiv='Refresh' content='0; url=/?error_survey_not_found=" . $_GET["survey"] . "' /></head>";
}
else {
    //if survey found: load survey
    $survey = $allSurveys->getSurvey($survey_id);

    //load target group
    if ($survey["target_group"] === "ehlb_students") $target_group_text = "Studierende der EH-Ludwigsburg";
    elseif ($survey["target_group"] === "ehlb_lecturers") $target_group_text = "Mitarbeitende der EH-Ludwigsburg";
    elseif ($survey["target_group"] === "ehlb_all") $target_group_text = "Studierende und Mitarbeitende der EH-Ludwigsburg";
    elseif ($survey["target_group"] === "no_restriction") $target_group_text = "Alle Personen";
    else $target_group_text = "Andere Gruppe: " . $survey["target_group"]; // TODO handle elsewhere...

    //check if it has results
    $has_results = $survey["has_results"];
    $look_back = false;
    if (isset($_GET["force_results"]) && $_GET["force_results"] == 1) $has_results = true;
    if (isset($_GET["look_back"]) && $_GET["look_back"] == 1) {
        $has_results = false;
        $look_back = true;
    }

    //load titles, descriptions
    $title = translate($survey["title"], "de", $GLOBALS["lang"]);
    $titleDE = $survey["title"];
    $description1 = translate($survey["description"], "de", $GLOBALS["lang"]);
    $description2 = translate($survey["subdescription"], "de", $GLOBALS["lang"]);

    //print tiles, descriptions, description
    echo "<h2>#" . $survey_id . " " . $title;
    if ($has_results) {
        $responses_count = $allResponses->countUniqueUsersBySurveyId($survey_id);
        //$results = loadResults($survey_id);
        echo translate(": Ergebnisse, n=" . $responses_count, "de", $GLOBALS["lang"]);
    }
    //am I draft?
    if (isset($_GET["draft"]) && $_GET["draft"] == "1") echo " (Entwurf, bitte nicht ausfüllen!)";
    //am I look back?
    if ($look_back) echo " (" . translate("Rückschau einer geschlossenen Umfrage, bitte nicht ausfüllen!", "de", $GLOBALS["lang"]) . ")";
    echo "</h2>";

    if ($description1 != "") {
        echo "<h3>" . $description1 . "</h3>";
    }

    if ($description2 != "") {
        echo "<h4>" . $description2 . "</h4>";
    }
    echo "<br>";
    echo "<div id='choose_anchor'></div>";

    $target_group_text = "<p>" . translate("Zielgruppe: " . $target_group_text, "de", $GLOBALS["lang"]) . "</p>";
    if (!isset($_GET["draft"]) || $_GET["draft"] !== "1") {
        $activated_at_text = "<p>" . translate("Geöffnet seit " . $survey["activated_at"], "de", $GLOBALS["lang"]) . "";
        $inactivated_at_text = "<p>" . translate("Geschlossen seit " . $survey["inactivated_at"], "de", $GLOBALS["lang"]) . "</p>";
        $has_results_text = "<div class='printmenot'><br><a href='/?survey=" . $titleDE . "&look_back=1'>" . translate("Geschlossene Umfrage nochmals anzeigen", "de", $GLOBALS["lang"]) . "</a>&emsp;||&emsp;<a onclick=\"printWithoutWeather(); window.print('%SCRIPTURL{view}%/%BASEWEB%/%BASETOPIC%?cover=print'); return false;\" style=\"cursor: pointer;\">" . translate("Diese Seite drucken", "de", $GLOBALS["lang"]) . "</a>&emsp;||&emsp;<a href='" /*. $results["file"]*/ . "'>" . translate("Rohdaten herunterladen", "de", $GLOBALS["lang"]) . "</a><br><br>" . translate("Infos: <br>Diese Seite ist für die Darstellung am PC / Laptop optimiert. <br>Der Druck gelingt im hellen Design am besten.", "de", $GLOBALS["lang"]) . "</div>";
        $look_back_text = "<div class='printmenot'><br><a href='/?survey=" . $titleDE . "'>".translate("Zurück zur Auswertung", "de", $GLOBALS["lang"]) . "</a><br><br>".translate("Infos: <br>Diese Seite ist eine Rückschau der ursprünglichen Umfrage. Sie kann nicht mehr abgegeben werden.", "de", $GLOBALS["lang"]) . "</div>";
    }
    else {
        $activated_at_text = "<p>" . translate("In der Umfrage wird hier stehen, seit wann sie <b>geöffnet</b> worden sein wird.", "de", $GLOBALS["lang"]) . "</p>";
        $inactivated_at_text = "<p>" . translate("Sobald die Umfrage <b>geschlossen</b> sein wird, steht hier seit wann.", "de", $GLOBALS["lang"]) . "</p>";
        $has_results_text = "<p>" . translate("Sobald die Umfrage <b>ausgewertet</b> sein wird, findet man unten alle Statistiken. ", "de", $GLOBALS["lang"]) . "</p>";
        $look_back_text = "<p>" . translate("An dieser Stelle wird man die <b>Rohdaten für Excel herunterladen</b>, die Ergebnisse inkl. Grafiken <b>drucken</b> können und hier erscheint ein Link zur <b>ursprünglichen Umfrage</b>, damit man sie nochmals ansehen kann.", "de", $GLOBALS["lang"]) . "</p>";
    }

    echo $target_group_text;
    echo $activated_at_text;
    if ($survey["inactivated_at"] !== "0000-00-00 00:00:00" || (isset($_GET["draft"]) && $_GET["draft"] == "1")) echo $inactivated_at_text;
    if ($has_results == 1 || (isset($_GET["draft"]) && $_GET["draft"] == "1")) echo $has_results_text;
    if ($look_back || (isset($_GET["draft"]) && $_GET["draft"] == "1")) echo $look_back_text;
    echo "<br><br>";


    $label_id = 0;
    $denum = 0;

    //print survey //TODO required
    echo "</header><form action='' method='post'>";

    //check if we really want to show the survey (is draft view or active or look back or otherwise forced to show
    if (((isset($_GET["draft"]) && $_GET["draft"] == 1) || $survey_id != 0 && $survey["is_active"] && !((isset($_GET["force_results"]) && $_GET["force_results"] == 1))) || $look_back) {

        //loop through all questions and answers
        $lastFollowUpNum = 0;
        $thisFollowUpNum = 0;

        $questions = $allQuestions->getQuestionsBySurveyId($survey_id);
        $questionChoices = $allQuestionChoices->getQuestionChoicesBySurveyId($survey_id);

        //if ($questions[0]["follow_up_question_id"] > 0) echo "true";
        //else echo "false";

        for ($i = 0; $i < sizeof($questions); $i++) {

            $question_id = $questions[$i]["id"];
            $question_type = $questions[$i]["question_type"];
            $is_follow_up = $questions[$i]["follow_up_question_id"] > 0;


            if (true) { //obsolete check if not empty sizeof($allSurveys[$thisSurveyNumber][$i]) > 0


                if (!$is_follow_up) {
                    echo "<div class='toggleDiv' style='display: block' id='toggle" . $i + 1 . "'>";
                } else echo "<div class='toggleDiv' style='display: block' id='toggle" . $i + 1 . "'>";


                if ($question_type === "dropdown") {
                    echo "<input type='hidden' name='" . $questions[$i]["id"] . "' value='-1' />"; // in case of empty answer
                    echo '<div><p class="poll">#' . $i + 1 . ': ' . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) . '<select name="' .
                        $questions[$i]["id"] . '" id="' . $questions[$i]["id"] .
                        '" title="' . $questions[$i]["question_text"] .
                        '" required><option id="none" value="none"  hidden disabled selected value>' . translate('Bitte auswählen', 'de', $GLOBALS["lang"]) . '</option>';

                    $choices = $allQuestionChoices->getQuestionChoicesByQuestionId($question_id);
                    for ($j = 0; $j < sizeof($choices); $j++) {
                        echo '<option id="' . $choices[$j]["id"] .
                            '" value="' . $choices[$j]["id"] . '">' .
                            translate($choices[$j]["choice_text"], 'de', $GLOBALS["lang"]) . '</option>';
                    }
                    echo '</p></optgroup></select></div>';
                } elseif ($question_type === "single_choice") {
                    echo "<input type='hidden' name='" . $questions[$i]["id"] . "' value='-1' />"; // in case of empty answer
                    echo '<div><p class="poll">#' . $i + 1 . ': ' . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) . '<br>';

                    $choices = $allQuestionChoices->getQuestionChoicesByQuestionId($question_id);
                    for ($j = 0; $j < sizeof($choices); $j++) {
                        echo '<input type="radio" id="' . $choices[$j]["id"] . '" name="' .
                            $questions[$i]["id"] . '" value="' . $choices[$j]["id"] .
                            '" required><label for="' .
                            $choices[$j]["id"] . '">' . translate($choices[$j]["choice_text"], 'de', $GLOBALS["lang"]) .
                            '</label>';
                    }
                    echo '</p></div>';
                } elseif ($question_type === "multiple_choice") {
                    echo '<div><p class="poll">#' . $i + 1 . ': ' . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) . '<br>';

                    $choices = $allQuestionChoices->getQuestionChoicesByQuestionId($question_id);
                    for ($j = 0; $j < sizeof($choices); $j++) {
                        echo '<input type="checkbox" id="' . $choices[$j]["id"] . '" name="' .
                            $questions[$i]["id"] . '" value="' . $choices[$j]["id"] . '"><label for="' .
                            $choices[$j]["id"] . '">' . translate($choices[$j]["choice_text"], 'de', $GLOBALS["lang"]) .
                            '</label>';
                    }
                    echo '</p></div>';
                } elseif ($question_type === "free_text") {
                    echo "<input type='hidden' name='" . $questions[$i]["id"] . "' value='-1' />"; // in case of empty answer
                    echo '<div><p class="poll">#' . $i + 1 . ': ' . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) .
                        '<input placeholder="' . translate('Bitte ausfüllen', 'de', $GLOBALS["lang"]) . '" type="text" id="' .
                        $questions[$i]["id"] . '" name="' . $questions[$i]["id"] .
                        '"></p></div>';
                } elseif ($question_type === "description") {
                    echo "<p>#" . $i + 1 . ": " . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) . "</p>";
                    //$denum++; not working if this is here, don't know y :/
                } elseif ($question_type === "picture") {
                    echo "    <div class='gallery'>
    
                        <picture>
                    <source srcset='/images/" . $questions[$i]["question_text"] . ".avif' type='image/avif'>
    <img class='clickableIMG' src='/images/" . $questions[$i]["question_text"] . ".jpg' style='max-height:200px; max-width:200px;' alt='" . str_replace(["-thumb", "-hoch", "-quer"], ["", "", ""], pathinfo($questions[$i]["question_text"] . ".jpg", PATHINFO_FILENAME)) . "'>
    </picture>
    </div>";
                    echo "<div id='fullpage' onclick='this.style.display=\"none\";'></div>";
                }
                echo "</div>";
            }
        }


    }
    //check if survey is set inactive and has no results yet
    elseif ($survey["is_inactive"] && !$survey["has_results"]) {
        echo "<br><br><br><p><b>" . translate("Danke für Dein Interesse!</b><br>Diese Umfrage ist geschlossen und war " /** TODO wie lange war sie offen? */  . " offen.<br>Schau bald wieder vorbei, wenn unsere Ergebnisse veröffentlicht sind.", "de", $GLOBALS["lang"]) . "</p><br><br><br>";
    }

if (!((isset($_GET["draft"]) && $_GET["draft"] == "1") || $look_back) && $survey["is_active"] && !((isset($_GET["force_results"]) && $_GET["force_results"] == 1))) {
    echo "<br><br><input type='hidden' name='content' value='sendsurvey' />";
    echo "<input type='hidden' name='target' value='" . $survey["target_group"] . "' />";
    echo '<div><p>' . translate('Bitte gib zum Schluss noch Deine studentische E-Mail-Adresse (@studnet.eh-ludwigsburg.de) ein.', 'de', $GLOBALS["lang"]) . ' <br>
&emsp;&emsp;<a><span tooltip="' . translate('Nur so können wir sicherstellen, dass nur Studierende der EH teilnehmen und niemand mehrfach teilnimmt.', 'de', $GLOBALS["lang"]) . '">' . translate('Warum das?', 'de', $GLOBALS["lang"]) . '</span></a><br>
&emsp;&emsp;<a><span tooltip="' . translate('Ja, denn wir speichern nur den Hash-Wert und nicht die Adresse selbst.', 'de', $GLOBALS["lang"]) . '">' . translate('Ist die Umfrage dann noch anonym?<', 'de', $GLOBALS["lang"]) . '/span></a><br>
&emsp;&emsp;<a href="/?content=mailinfo" target="_blank">' . translate('Klicke hier für mehr Informationen.', 'de', $GLOBALS["lang"]) . '</a>
<input placeholder="' . translate('Bitte ausfüllen', 'de', $GLOBALS["lang"]) . '" type="email" id="email" name="email" required></p></div>';
    echo "<input type='hidden' name='survey_id' value='" . $allSurveys[$thisSurveyNumber][0][0] . "' />";
    echo '<br><input type="submit" value="' . translate('Abschicken', 'de', $GLOBALS["lang"]) . '"></form></section>';


}

ini_set('display_errors', 0);

echo "<br><br><br><br><b>Hier wird aktuell viel Neues getestet, das noch nicht funktioniert...</b><br><br><a href='https://www.eh-umfragen.de'>Gehe besser zurück zur produktiven Seite www.eh-umfragen.de</a><br><br>";







    for ($i = 0; $i < sizeof($allSurveys); $i++) {
        $id = -1;
        $id_file = intval(utf8Encode($allSurveys[$i][0][0]));
        $id_db = get_survey_id($allSurveys[$i][0][1]);
        if ($id_file === $id_db) $id = $id_file;

        else {
            if ($id_file < 1 || $id_db < 1) $id = set_survey_id($allSurveys[$i][0][1], $globalsurveys[$i]['filename']);
            else $id = $id_db;
            $allSurveys[$i][0][0] = $id;
            $files = glob("surveys/*.csv");
            $handle = fopen($files[$i], "w");
            for ($j = 0; $j < sizeof($allSurveys[$i]); $j++) {
                for ($k = 0; $k < sizeof($allSurveys[$i][$j]); $k++) {
                    $allSurveys[$i][$j][$k] = iconv("UTF-8", "Windows-1252", $allSurveys[$i][$j][$k]);
                }
                fputcsv($handle, $allSurveys[$i][$j], ";");
            }
            fclose($handle);
            chmod('results/' . $files[$i], 0775);

            $types[] = "";
            $questions[] = "";
            $options[] = "";
            $denum = 0;
            for ($j = 1; $j < sizeof($allSurveys[$i]); $j++) {
                if ($allSurveys[$i][$j][0] == "dropdown" or $allSurveys[$i][$j][0] == "multiple_choice" or $allSurveys[$i][$j][0] == "single_choice" or $allSurveys[$i][$j][0] == "free_text") {
                    $types[$j - 1 - $denum] = utf8Encode($allSurveys[$i][$j][0]);
                    //echo $types[$j-1];
                    $questions[$j - 1 - $denum] = utf8Encode($allSurveys[$i][$j][1]);
                    if (sizeof($allSurveys[$i][$j]) <= 2) $options[$j - 1 - $denum] = "offene Frage";
                    else {
                        for ($k = 2; $k < sizeof($allSurveys[$i][$j]); $k++) {
                            $options[$j - 1 - $denum] .= utf8Encode($allSurveys[$i][$j][$k]);
                            if ($allSurveys[$i][$j][$k + 1] != null) $options[$j - 1 - $denum] .= "; ";
                        }
                    }
                } else $denum++;
            }
            create_survey($allSurveys[$i][0][0], sizeof($questions), $types, $questions, $options);
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
if ($has_results == 1) {
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

    if (!isset($resultsLeft["n"])) $resultsLeft["n"] = "0; " . translate("Diese Option hat niemand ausgewählt.", "de", $GLOBALS["lang"]); // Wenn niemand so geantwortet hat.
    if (!isset($resultsRight["n"])) $resultsRight["n"] = "0; " . translate("Diese Option hat niemand ausgewählt.", "de", $GLOBALS["lang"]); // Wenn niemand so geantwortet hat.

    $thisSurveyHead = getSurveyHeads(loadSurveys(), $thisSurveyNumber);
    ?>


    <div style="display: table; width: 100%;">
        <div class="table-row">
            <div class="table-cell" id="left_form">
                <div id='left_choices'>
                    <label for="result_scheme_left_Q" class='printmenot'><?php echo translate("Wähle eine lineare Ansicht aller Ergebnisse oder Ergebnisse in Relation zu einer Antwort.", "de", $GLOBALS["lang"]); ?></label>
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
                        <label for="result_scheme_left_A"></label><select id="result_scheme_left_A" name="result_scheme_left_A">
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
                                if ($thisSurveyHead["types"][$x] === "description") {
                                    $foundInfo = true;
                                    break;
                                }
                                elseif (
                                    $thisSurveyHead["types"][$x] === "dropdown" ||
                                    $thisSurveyHead["types"][$x] === "single_choice" ||
                                    $thisSurveyHead["types"][$x] === "multiple_choice" ||
                                    $thisSurveyHead["types"][$x] === "free_text"
                                ) {
                                    $foundInfo = false;
                                     $x = $headAt;
                                    break;
                                }
                            }
                            if ($foundInfo) {
                                $thisHead = "";
                                while ( isset($thisSurveyHead["types"][$x-1]) &&
                                $thisSurveyHead["types"][$x-1] === "description" &&
                                $x < sizeof($thisSurveyHead["heads"])
                            ) $x--;
                                while (
                                        $thisSurveyHead["types"][$x] === "description" &&
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
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Säulendiagramm", "de", $GLOBALS["lang"]); ?></label>
                                <input type="radio" name="diagram_scheme" id="left_both_<?php echo $i; ?>"
                                       value="1">
                                <label class="graph_scheme" for="left_both_<?php echo $i; ?>"
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Beide Diagramme", "de", $GLOBALS["lang"]); ?></label>
                            </form>
                            <?php
                            if ($i == 0) echo "<br><br><br><br><br><br>"; //stupid workaround-bugfix-hack. o_O
                            echo '<br>';

                            echo '<img class="light-mode" style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsLeft["counts"]["assign"][$i]) . '" src="data:image/png;base64,' . drawLegendLight($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                            echo '<img class="dark-mode" style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsLeft["counts"]["assign"][$i]) . '" src="data:image/png;base64,' . drawLegendDark($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                            echo '<br><div id="left_circle_graph_' . $i . '">';

                            echo '<img class="light-mode" alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircleLight($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                            echo '<img class="dark-mode" alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircleDark($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                            echo '</div><div id="left_rectangle_graph_' . $i . '" style="display: none">';

                            echo '<img class="light-mode" alt="'.translate('ein automatisch generiertes Säulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangleLight($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                            echo '<img class="dark-mode" alt="'.translate('ein automatisch generiertes Säulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangleDark($resultsLeft["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                            echo "</div><br>";
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
                    <label for="result_scheme_right_Q" class='printmenot'><?php echo translate("Wähle eine lineare Ansicht aller Ergebnisse oder Ergebnisse in Relation zu einer Antwort.", "de", $GLOBALS["lang"]); ?></label>
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
                        <label for="result_scheme_right_A"></label><select id="result_scheme_right_A" name="result_scheme_right_A">
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
                                if ($thisSurveyHead["types"][$x] === "description") {
                                    $foundInfo = true;
                                    break;
                                }
                                elseif (
                                    $thisSurveyHead["types"][$x] === "dropdown" ||
                                    $thisSurveyHead["types"][$x] === "single_choice" ||
                                    $thisSurveyHead["types"][$x] === "multiple_choice" ||
                                    $thisSurveyHead["types"][$x] === "free_text"
                                ) {
                                    $foundInfo = false;
                                    $x = $headAt;
                                    break;
                                }
                            }
                            if ($foundInfo) {
                                $thisHead = "";
                                while ( isset($thisSurveyHead["types"][$x-1]) &&
                                    $thisSurveyHead["types"][$x-1] === "description" &&
                                    $x < sizeof($thisSurveyHead["heads"])
                                ) $x--;
                                while (
                                    $thisSurveyHead["types"][$x] === "description" &&
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
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Säulendiagramm", "de", $GLOBALS["lang"]); ?></label>
                                <input type="radio" name="diagram_scheme" id="right_both_<?php echo $i; ?>"
                                       value="1">
                                <label class="graph_scheme" for="right_both_<?php echo $i; ?>"
                                       style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Beide Diagramme", "de", $GLOBALS["lang"]); ?></label>
                            </form>
                            <?php
                            if ($i == 0) echo "<br><br><br>"; //stupid workaround-bugfix-hack. o_O
                            echo '<br>';

                            echo '<img class="light-mode" style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsRight["counts"]["assign"][$i]) . '" src="data:image/png;base64,' . drawLegendLight($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                            echo '<img class="dark-mode" style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsRight["counts"]["assign"][$i]) . '" src="data:image/png;base64,' . drawLegendDark($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                            echo '<br><div id="right_circle_graph_' . $i . '">';

                            echo '<img class="light-mode" alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircleLight($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                            echo '<img class="dark-mode" alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircleDark($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                            echo '</div><div id="right_rectangle_graph_' . $i . '" style="display: none">';

                            echo '<img class="light-mode" alt="'.translate('ein automatisch generiertes Säulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangleLight($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                            echo '<img class="dark-mode" alt="'.translate('ein automatisch generiertes Säulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangleDark($resultsRight["counts"]["assign"][$i]) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                            echo "</div><br>";
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
        let forceresults = parseInt("<?php if ($_GET["force_results"] && intval($_GET["force_results"]) == 1) echo 1; else echo 0; ?>");
        if (forceresults === 1) {
            forceresults = "&force_results=1";
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





/*
//this must be here, color_scheme_handler wouldn't work otherwise :/
            var ex1 = document.getElementById('auto');
            var ex2 = document.getElementById('light');
            var ex3 = document.getElementById('dark');
            var ex4 = document.getElementById('contrast');

            ex1.onclick = auto;
            ex2.onclick = light;
            ex3.onclick = dark;
            ex4.onclick = contrast;*/
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