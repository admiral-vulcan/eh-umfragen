<?php

use EHUmfragen\DatabaseModels\Results;
use EHUmfragen\DatabaseModels\Creators;
use EHUmfragen\DatabaseModels\Surveys;
use EHUmfragen\DatabaseModels\Responses;
use EHUmfragen\DatabaseModels\Questions;
use EHUmfragen\DatabaseModels\QuestionChoices;

//require_once ("convertTimeFormatInString.php");                                                     /** ???????????????????????? */
function formatGermanDate($myTime): false|string {
    // Erstellt ein neues DateTime-Objekt und setzt die Zeitzone
    $dateTime = new DateTime($myTime, new DateTimeZone('Europe/Berlin'));

    // Erstellt einen neuen IntlDateFormatter
    $formatter = new IntlDateFormatter(
        'de_DE',
        IntlDateFormatter::LONG,  // Datum in Langform
        IntlDateFormatter::SHORT, // Uhrzeit in Kurzform
        'Europe/Berlin',
        IntlDateFormatter::GREGORIAN,
        'd. MMMM yyyy, H:mm \'Uhr\'' // benutzerdefiniertes Format
    );

    // Formatiert das Datum und die Uhrzeit
    $formattedDate = $formatter->format($dateTime);

    return $formattedDate;
}


$allSurveys = new Surveys();
$allResponses = new Responses();
$allQuestions = new Questions();
$allQuestionChoices = new QuestionChoices();
$allResults = new Results();
$allCreators = new Creators();

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
    $creator_id = $allSurveys->getCreatorId($survey_id);
    $creator_name = $allCreators->getCreatorName($creator_id);

    //check if it has results
    $has_results = $survey["has_results"] == "1";
    $look_back = false;
    if (isset($_GET["force_results"]) && $_GET["force_results"] == 1) $has_results = true;
    if (isset($_GET["look_back"]) && $_GET["look_back"] == 1) {
        $has_results = false;
        $look_back = true;
    }
    if ((!isset($_GET["draft"]) || $_GET["draft"] != "1") && !$survey["is_active"] && !$has_results && !$look_back) {
        echo "<p>" . translate("Diese Umfrage ist nicht freigeschaltet.", "de", $GLOBALS["lang"]) . "</p> <br>";
    }
    else {

    //load target group
    if ($survey["target_group"] === "ehlb_students") $target_group_text = "Studierende der EH-Ludwigsburg";
    elseif ($survey["target_group"] === "ehlb_lecturers") $target_group_text = "Mitarbeitende der EH-Ludwigsburg";
    elseif ($survey["target_group"] === "ehlb_all") $target_group_text = "Studierende und Mitarbeitende der EH-Ludwigsburg";
    elseif ($survey["target_group"] === "no_restriction") $target_group_text = "Alle Personen";
    else $target_group_text = "Andere Gruppe: " . $survey["target_group"]; // TODO handle elsewhere...


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
    if (isset($_GET["draft"]) && $_GET["draft"] == "1") echo " (" . translate("Entwurf, kann nicht abgeschickt werden!", "de", $GLOBALS["lang"]) . ")";
    //am I look back?
    if ($look_back) echo " (" . translate("Rückschau einer geschlossenen Umfrage, sie kann nicht abgeschickt werden!", "de", $GLOBALS["lang"]) . ")";
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
        $creator_name_text = "<p>" . translate("Erstellt von", "de", $GLOBALS["lang"]) . " " . $creator_name . "</p>";
        $activated_at_text = "<p>" . translate("Geöffnet seit dem " . formatGermanDate($survey["activated_at"]), "de", $GLOBALS["lang"]) . "";
        $inactivated_at_text = "<p>" . translate("Geschlossen seit dem " . formatGermanDate($survey["inactivated_at"]), "de", $GLOBALS["lang"]) . "</p>";
        $has_results_text = "<div class='printmenot'><br><a href='/?survey=" . $titleDE . "&look_back=1'>" . translate("Umfrage anzeigen", "de", $GLOBALS["lang"]) . "</a>&emsp;||&emsp;<a onclick=\"printWithoutWeather(); window.print('%SCRIPTURL{view}%/%BASEWEB%/%BASETOPIC%?cover=print'); return false;\" style=\"cursor: pointer;\">" . translate("Diese Seite drucken", "de", $GLOBALS["lang"]) . "</a>&emsp;||&emsp;<a href='downloadCsv.php?survey_id=" . $survey_id . "&mode=results'>" . translate("Rohdaten herunterladen", "de", $GLOBALS["lang"]) . "</a>&emsp;||&emsp;<a href='downloadCsv.php?survey_id=" . $survey_id . "&mode=meta'>" . translate("Metadaten herunterladen", "de", $GLOBALS["lang"]) . "</a><br><br>" . translate("Infos: <br>Diese Seite ist für die Darstellung am PC / Laptop optimiert. <br>Der Druck gelingt im hellen Design am besten.", "de", $GLOBALS["lang"]) . "</div>";
        $look_back_text = "<div class='printmenot'><br><a href='/?survey=" . $titleDE . "'>".translate("Zurück zur Auswertung", "de", $GLOBALS["lang"]) . "</a><br><br>".translate("Infos: <br>Diese Seite ist eine Rückschau der ursprünglichen Umfrage. Sie kann nicht mehr abgegeben werden.", "de", $GLOBALS["lang"]) . "</div>";
    }
    else {
        $creator_name_text = ""; //TODO
        $activated_at_text = "<p>" . translate("In der Umfrage wird hier stehen, seit wann sie <b>geöffnet</b> worden sein wird.", "de", $GLOBALS["lang"]) . "</p>";
        $inactivated_at_text = "<p>" . translate("Sobald die Umfrage <b>geschlossen</b> sein wird, steht hier seit wann.", "de", $GLOBALS["lang"]) . "</p>";
        $has_results_text = "<p>" . translate("Sobald die Umfrage <b>ausgewertet</b> sein wird, findet man unten alle Statistiken. ", "de", $GLOBALS["lang"]) . "</p>";
        $look_back_text = "<p>" . translate("An dieser Stelle wird man die <b>Rohdaten für Excel herunterladen</b>, die Ergebnisse inkl. Grafiken <b>drucken</b> können und hier erscheint ein Link zur <b>ursprünglichen Umfrage</b>, damit man sie nochmals ansehen kann.", "de", $GLOBALS["lang"]) . "</p>";
    }

    echo $creator_name_text;
    if ($survey["target_group"] !== "no_restriction" || $survey["is_visible"]) echo $target_group_text;
    echo $activated_at_text;
    if (($survey["is_active"] != "1" && $survey["inactivated_at"] !== "0000-00-00 00:00:00") || (isset($_GET["draft"]) && $_GET["draft"] == "1")) echo $inactivated_at_text;
    if ($has_results == 1 || (isset($_GET["draft"]) && $_GET["draft"] == "1")) echo $has_results_text;
    if ($look_back || (isset($_GET["draft"]) && $_GET["draft"] == "1")) echo $look_back_text;
    echo "<br><br>";


    $label_id = 0;
    $denum = 0;

    //print survey //TODO required
    echo "</header><form id='thisSurvey' action='' method='post'>";

    //check if we really want to show the survey (is draft view or active or look back or otherwise forced to show
    if ((!$has_results && ((isset($_GET["draft"]) && $_GET["draft"] == 1) || $survey_id != 0 && $survey["is_active"] && !((isset($_GET["force_results"]) && $_GET["force_results"] == 1)))) || $look_back) {

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
                    echo '<div><p class="poll">' . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) . '<select name="' .
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
                    echo '<div><p class="poll">' . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) . '<br>';

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
                    echo '<div><p class="poll">' . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) . '<br>';

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
                    echo '<div><p class="poll">' . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) .
                        '<input placeholder="' . translate('Bitte ausfüllen', 'de', $GLOBALS["lang"]) . '" type="text" id="' .
                        $questions[$i]["id"] . '" name="' . $questions[$i]["id"] .
                        '"></p></div>';
                } elseif ($question_type === "description") {
                    echo "<p>" . translate($questions[$i]["question_text"], 'de', $GLOBALS["lang"]) . "</p>";
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
    elseif ($survey["is_inactive"] && !$has_results) {
        echo "<br><br><br><p><b>" . translate("Danke für Dein Interesse!</b><br>Diese Umfrage ist geschlossen und war " /** TODO wie lange war sie offen? */  . " offen.<br>Schau bald wieder vorbei, wenn unsere Ergebnisse veröffentlicht sind.", "de", $GLOBALS["lang"]) . "</p><br><br><br>";
    }

    if (!((isset($_GET["draft"]) && $_GET["draft"] == "1") || $look_back) && $survey["is_active"] && !((isset($_GET["force_results"]) && $_GET["force_results"] == 1))) {
        echo "<br><br><input type='hidden' name='content' value='sendsurvey' />";
        echo "<input type='hidden' name='target' value='" . $survey["target_group"] . "' />";
        if ($survey["target_group"] === "ehlb_students") {
            echo '<div><p>' . translate('Bitte gib zum Schluss noch Deine studentische E-Mail-Adresse (@studnet.eh-ludwigsburg.de) ein. <br>Du bekommst dann einen Link zur Verifizierung zugesandt.', 'de', $GLOBALS["lang"]) . ' <br>
&emsp;&emsp;<a><span tooltip="' . translate('Nur so können wir sicherstellen, dass nur die Zielgruppe und niemand mehrfach teilnimmt.', 'de', $GLOBALS["lang"]) . '">' . translate('Warum das?', 'de', $GLOBALS["lang"]) . '</span></a><br>
&emsp;&emsp;<a><span tooltip="' . translate('Ja, denn wir speichern nur den Hash-Wert und nicht die Adresse selbst.', 'de', $GLOBALS["lang"]) . '">' . translate('Ist die Umfrage dann noch anonym?<', 'de', $GLOBALS["lang"]) . '/span></a><br>
&emsp;&emsp;<a href="/?content=mailinfo" target="_blank">' . translate('Klicke hier für mehr Informationen.', 'de', $GLOBALS["lang"]) . '</a>
<input placeholder="' . translate('Bitte ausfüllen', 'de', $GLOBALS["lang"]) . '" type="email" id="email" name="email" required></p></div>';
            echo "<input type='hidden' name='survey_id' value='" . $survey_id . "' />";
            echo '<br><input id="submit-btn" type="submit" value="' . translate('Abschicken', 'de', $GLOBALS["lang"]) . '"></form></section>';

        }
        //TODO other target groups
        else {

        }

    }
    elseif (isset($_GET["draft"]) && $_GET["draft"] == "1") {
        echo '<br><input id="submit-btn" type="submit" value="' . translate('Abschicken', 'de', $GLOBALS["lang"]) . '" disabled></form></section>';
    }


    /**
     *
     * This is where the magic begins!
     *
     * aka result-drawer
     *
     * we work with $thisid
     *
     * */

    if ($has_results || ($_GET["force_results"] && intval($_GET["force_results"]) == 1)) {
        $results = $allResults->getResultsBySurveyId($survey_id);
        $responses = $allResponses->getResponsesBy($survey_id);
        $thisName = $survey["title"];

        if (isset($_GET["leftQ"]) && $_GET["leftQ"] == "") unset($_GET["leftQ"]);
        if (isset($_GET["rightQ"]) && $_GET["rightQ"] == "") unset($_GET["rightQ"]);
        if (isset($_GET["leftA"]) && $_GET["leftA"] == "") unset($_GET["leftA"]);
        if (isset($_GET["rightA"]) && $_GET["rightA"] == "") unset($_GET["rightA"]);

        if (isset($_GET["leftQ"]) && $_GET["leftQ"] != "all_results") {
            $leftQ = urldecode($_GET["leftQ"]);
            if (!isset($_GET["leftA"])) $_GET["leftA"] = 0;
            $leftA = urldecode($_GET["leftA"]);
            $resultsLeft = $allResults->getRelativeResultsByChoice($survey_id, $leftQ, $leftA);
        } else {
            $resultsLeft = $results;
        }
        $left_counts = array_values($resultsLeft)[0]["total"];

        if (isset($_GET["rightQ"]) && $_GET["rightQ"] != "all_results") {
            $rightQ = urldecode($_GET["rightQ"]);
            if (!isset($_GET["rightA"])) $_GET["rightA"] = 0;
            $rightA = urldecode($_GET["rightA"]);
            $resultsRight = $allResults->getRelativeResultsByChoice($survey_id, $rightQ, $rightA);
        } else {
            $resultsRight = $results;
        }
        $right_counts = array_values($resultsRight)[0]["total"];

        if (intval($_GET["leftQ"]) > 0 && intval($_GET["leftA"]) > 0 && $left_counts === 0)
            $left_counts = "0; " . translate("Diese Antwort hat niemand ausgewählt.", "de", $GLOBALS["lang"]);
        elseif (intval($_GET["leftQ"]) > 0 && intval($_GET["leftA"]) === 0) $left_counts = "";
        else $left_counts = "" . $left_counts;

        if (intval($_GET["rightQ"]) > 0 && intval($_GET["rightA"]) > 0 && $right_counts === 0)
            $right_counts = "0; " . translate("Diese Antwort hat niemand ausgewählt.", "de", $GLOBALS["lang"]);
        elseif (intval($_GET["rightQ"]) > 0 && intval($_GET["rightA"]) === 0) $right_counts = "";
        else $right_counts = "" . $right_counts;

        ?>
        <div class="printmenot" id="toggle_compare">
            <form>
                <input type="checkbox" name="toggle_compare_visible" id="toggle_compare_visible_yes" ><!--checked, siehe css #right_form display: table-cell; -->
                <label class="toggle_compare_visible" for="toggle_compare_visible_yes" style="padding-top: 5px;"><?php echo translate("Vergleichsansicht anzeigen", "de", $GLOBALS["lang"]); ?></label>
            </form>
        </div>
        <div style="display: table; width: 100%;">
            <div class="table-row">
                <div class="table-cell" id="left_form">
                    <div id='left_choices'>
                        <label for="result_scheme_left_Q" class='printmenot'><?php echo translate("Wähle eine lineare Ansicht aller Ergebnisse oder Ergebnisse in Relation zu einer Antwort.", "de", $GLOBALS["lang"]); ?></label>
                        <select id="result_scheme_left_Q" name="result_scheme_left_Q">
                            <option value="all_results"><?php echo translate("Alle Ergebnisse", "de", $GLOBALS["lang"]); ?></option>
                            <?php
                            foreach ($results as $question_id => $result_data) {
                                $result = $allQuestions->getQuestionById($question_id);
                                $thisQ = translate($result["question_text"], "de", $GLOBALS["lang"]);
                                echo "<option value='" . $question_id . "' ";
                                if (isset($_GET["leftQ"]) && $question_id == $_GET["leftQ"]) echo "selected";
                                echo ">" . $thisQ . "</option>";
                            }

                            echo "</select>";
                            if (isset($_GET["leftQ"]) && $_GET["leftQ"] != "all_results") {
                                echo '<label for="result_scheme_left_A"></label><select id="result_scheme_left_A" name="result_scheme_left_A">';
                                echo '<option value="0" disabled selected>' . translate("Bitte Antwort auswählen", "de", $GLOBALS["lang"]) . '</option>';
                                $choices = $allQuestionChoices->getQuestionChoicesByQuestionId(intval($_GET["leftQ"]));
                                foreach ($choices as $choice_data) {
                                    $thisA = translate($choice_data["choice_text"], "de", $GLOBALS["lang"]);
                                    echo "<option value='" . $choice_data["id"] . "' ";
                                    if (isset($_GET["leftA"]) && $choice_data["id"] == $_GET["leftA"]) echo "selected";
                                    echo ">" . $thisA . "</option>";
                                }
                            }
                            echo "</select>";
                            ?>
                    </div>
                    <div id='left_n'>
                        <br>
                        <p style="bottom: 0;"><b><?php if ($left_counts != "") echo "n=" . $left_counts; ?></b></p><hr>
                    </div>

                    <?php
                    $i = 0;
                    if (intval($left_counts) !== 0 && (((!isset($_GET["leftQ"]) && !isset($_GET["leftA"]))
                            || (intval($_GET["leftQ"]) === 0 && intval($_GET["leftA"]) === 0)
                            || (intval($_GET["leftQ"]) > 0 && intval($_GET["leftA"]) !== 0)
                        ))){
                        foreach ($resultsLeft as $question_id => $result_data) {
                            $result = $allQuestions->getQuestionById($question_id);
                            $questionChoices = $allQuestionChoices->getQuestionChoicesByQuestionId($question_id);
                            $resultsToPrint = array();
                            foreach ($result_data['choices'] as $choice_id => $choice_data) {
                                $resultsToPrint[$choice_data['text']] = $choice_data['count'];
                            }
                            echo PHP_EOL . "<div class='no-break-inside' id='left_" . $i . "'>";

                            // Iterate over the previous questions until a non-description question is encountered
                            $prev_question_id = $question_id - 1;
                            $descriptions = array();
                            while ($prev_question_id >= 0) {
                                $prev_question = $allQuestions->getQuestionById($prev_question_id);
                                if ($prev_question['survey_id'] != $survey_id) {
                                    break; // Stop if the survey ID doesn't match
                                } elseif (in_array($prev_question['question_type'], array('free_text', 'single_choice', 'multiple_choice', 'dropdown'))) {
                                    break; // Stop if a non-description question type is encountered
                                } else {
                                    if ($prev_question['question_type'] == "description") { //we ignore picture for now TODO implement picture
                                        $descriptions[] = $prev_question['question_text'];
                                    }
                                }
                                $prev_question_id--;
                            }

                            // Print the descriptions in the correct order
                            foreach (array_reverse($descriptions) as $description) {
                                echo "<h2>" . translate($description, "de", $GLOBALS["lang"]) . "</h2>";
                            }

                            echo "<hr><h2>" . translate($result["question_text"], "de", $GLOBALS["lang"]) . "</h2>";
                            if ($result["question_type"] !== "free_text") {
                                if ($result["question_type"] == "multiple_choice") echo "<p class='printmenot'>".translate("Mehrfachauswahl", "de", $GLOBALS["lang"])."</p>";
                                else echo "<p class='printmenot'>".translate("Einfachauswahl", "de", $GLOBALS["lang"])."</p>";
                                ?>
                                <form class="printmenot">
                                    <input type="radio" name="diagram_scheme" id="left_circle_<?php echo $i; ?>"
                                           value="1" checked>
                                    <label class="graph_scheme printmenot" for="left_circle_<?php echo $i; ?>"
                                           style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Kreisdiagramm", "de", $GLOBALS["lang"]); ?></label>
                                    <input type="radio" name="diagram_scheme" id="left_rectangle_<?php echo $i; ?>"
                                           value="1">
                                    <label class="graph_scheme printmenot" for="left_rectangle_<?php echo $i; ?>"
                                           style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Säulendiagramm", "de", $GLOBALS["lang"]); ?></label>
                                    <input type="radio" name="diagram_scheme" id="left_both_<?php echo $i; ?>"
                                           value="1">
                                    <label class="graph_scheme printmenot" for="left_both_<?php echo $i; ?>"
                                           style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Beide Diagramme", "de", $GLOBALS["lang"]); ?></label>
                                </form>
                                <?php
                                echo '<br><br>';

                                echo '<img class="light-mode" style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsToPrint) . '" src="data:image/png;base64,' . drawLegendLight($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                                echo '<img class="dark-mode" style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsToPrint) . '" src="data:image/png;base64,' . drawLegendDark($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                                echo '<br><div id="left_circle_graph_' . $i . '">';

                                echo '<img class="light-mode" alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircleLight($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                                echo '<img class="dark-mode" alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircleDark($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                                echo '</div><div id="left_rectangle_graph_' . $i . '" style="display: none;">';

                                echo '<img class="light-mode" alt="'.translate('ein automatisch generiertes Säulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangleLight($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                                echo '<img class="dark-mode" alt="'.translate('ein automatisch generiertes Säulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangleDark($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                                echo '</div><br>';
                                echo PHP_EOL . "</div><hr>";
                                if ($result["question_type"] == "multiple_choice" || $result["question_type"] == "single_choice" || $result["question_type"] == "dropdown") $i++;
                            }
                            else {
                                echo '<div id="left_circle_graph_' . $i . '" style="display: none;"></div>';
                                echo '<div id="left_rectangle_graph_' . $i . '" style="display: none;"></div>';
                                echo '<div id="left_circle_' . $i . '" style="display: none;"></div>';
                                echo '<div id="left_rectangle_' . $i . '" style="display: none;"></div>';
                                echo '<div id="left_both_' . $i . '" style="display: none;"></div>';

                                $count = 0;
                                $this_responses = "";
                                foreach ($result_data['responses'] as $response) {
                                    if (isset($response['response_text']) && trim($response['response_text']) !== '') {
                                        $this_responses .= "<div id='free_text_left_" . $i . "_" . $count + 1 . "'>#" . $count + 1 .": " . "<p><br>" . translate($response['response_text'], 'de', $GLOBALS['lang']) . "</p></div><hr>";
                                        $count++;
                                    }
                                }
                                echo "";
                                echo "<p>n=" . $count . "</p><hr>";
                                echo substr($this_responses, 0, -4); //without the last <hr>
                                echo "";
                                echo "</div><hr>";
                                $i++;
                            }
                        }
                    }
                    else {
                        echo "<div id='left_0'><hr><h2></h2><p></p><br><br><br><br><br><br><br><br></div>";
                    }
                    $questions_count_for_java = $i; //to manage changing graphs
                    ?>
                </div>
                <div class="table-cell" id="right_form">
                    <div id='right_choices'>
                        <label for="result_scheme_right_Q" class='printmenot'><?php echo translate("Wähle eine lineare Ansicht aller Ergebnisse oder Ergebnisse in Relation zu einer Antwort.", "de", $GLOBALS["lang"]); ?></label>
                        <select id="result_scheme_right_Q" name="result_scheme_right_Q">
                            <option value="all_results"><?php echo translate("Alle Ergebnisse", "de", $GLOBALS["lang"]); ?></option>
                            <?php
                            foreach ($results as $question_id => $result_data) {
                                $result = $allQuestions->getQuestionById($question_id);
                                $thisQ = translate($result["question_text"], "de", $GLOBALS["lang"]);
                                echo "<option value='" . $question_id . "' ";
                                if (isset($_GET["rightQ"]) && $question_id == $_GET["rightQ"]) echo "selected";
                                echo ">" . $thisQ . "</option>";
                            }

                            echo "</select>";
                            if (isset($_GET["rightQ"]) && $_GET["rightQ"] != "all_results") {
                                echo '<label for="result_scheme_right_A"></label><select id="result_scheme_right_A" name="result_scheme_right_A">';
                                echo '<option value="0" disabled selected>' . translate("Bitte Antwort auswählen", "de", $GLOBALS["lang"]) . '</option>';
                                $choices = $allQuestionChoices->getQuestionChoicesByQuestionId(intval($_GET["rightQ"]));
                                foreach ($choices as $choice_data) {
                                    $thisA = translate($choice_data["choice_text"], "de", $GLOBALS["lang"]);
                                    echo "<option value='" . $choice_data["id"] . "' ";
                                    if (isset($_GET["rightA"]) && $choice_data["id"] == $_GET["rightA"]) echo "selected";
                                    echo ">" . $thisA . "</option>";
                                }
                            }
                            echo "</select>";
                            ?>
                    </div>
                    <div id='right_n'>
                        <br>
                        <p style="bottom: 0;"><b><?php if ($right_counts != "") echo "n=" . $right_counts; ?></b></p><hr>
                    </div>

                    <?php
                    $i = 0;
                    if (intval($right_counts) !== 0 && (((!isset($_GET["rightQ"]) && !isset($_GET["rightA"]))
                            || (intval($_GET["rightQ"]) === 0 && intval($_GET["rightA"]) === 0)
                            || (intval($_GET["rightQ"]) > 0 && intval($_GET["rightA"]) !== 0)
                        ))){
                        foreach ($resultsRight as $question_id => $result_data) {
                            $result = $allQuestions->getQuestionById($question_id);
                            $questionChoices = $allQuestionChoices->getQuestionChoicesByQuestionId($question_id);
                            $resultsToPrint = array();
                            foreach ($result_data['choices'] as $choice_id => $choice_data) {
                                $resultsToPrint[$choice_data['text']] = $choice_data['count'];
                            }
                            echo PHP_EOL . "<div class='no-break-inside' id='right_" . $i . "'>";

                            // Iterate over the previous questions until a non-description question is encountered
                            $prev_question_id = $question_id - 1;
                            $descriptions = array();
                            while ($prev_question_id >= 0) {
                                $prev_question = $allQuestions->getQuestionById($prev_question_id);
                                if ($prev_question['survey_id'] != $survey_id) {
                                    break; // Stop if the survey ID doesn't match
                                } elseif (in_array($prev_question['question_type'], array('free_text', 'single_choice', 'multiple_choice', 'dropdown'))) {
                                    break; // Stop if a non-description question type is encountered
                                } else {
                                    if ($prev_question['question_type'] == "description") { //we ignore picture for now TODO implement picture
                                        $descriptions[] = $prev_question['question_text'];
                                    }
                                }
                                $prev_question_id--;
                            }

                            // Print the descriptions in the correct order
                            foreach (array_reverse($descriptions) as $description) {
                                echo "<h2>" . translate($description, "de", $GLOBALS["lang"]) . "</h2>";
                            }



                            echo "<hr><h2>" . translate($result["question_text"], "de", $GLOBALS["lang"]) . "</h2>";
                            if ($result["question_type"] !== "free_text") {
                                if ($result["question_type"] == "multiple_choice") echo "<p>".translate("Mehrfachauswahl", "de", $GLOBALS["lang"])."</p>";
                                else echo "<p>".translate("Einfachauswahl", "de", $GLOBALS["lang"])."</p>";
                                ?>
                                <form>
                                    <input type="radio" name="diagram_scheme" id="right_circle_<?php echo $i; ?>"
                                           value="1" checked>
                                    <label class="graph_scheme printmenot" for="right_circle_<?php echo $i; ?>"
                                           style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Kreisdiagramm", "de", $GLOBALS["lang"]); ?></label>
                                    <input type="radio" name="diagram_scheme" id="right_rectangle_<?php echo $i; ?>"
                                           value="1">
                                    <label class="graph_scheme printmenot" for="right_rectangle_<?php echo $i; ?>"
                                           style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Säulendiagramm", "de", $GLOBALS["lang"]); ?></label>
                                    <input type="radio" name="diagram_scheme" id="right_both_<?php echo $i; ?>"
                                           value="1">
                                    <label class="graph_scheme printmenot" for="right_both_<?php echo $i; ?>"
                                           style="padding-top: 0.5em; font-size: 0.8em; font-weight: 700; margin: 0 0 1em 0; color: var(--label-color);"><?php echo translate("Beide Diagramme", "de", $GLOBALS["lang"]); ?></label>
                                </form>
                                <?php
                                echo '<br><br>';

                                echo '<img class="light-mode" style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsToPrint) . '" src="data:image/png;base64,' . drawLegendLight($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                                echo '<img class="dark-mode" style="margin-top: -3em" alt="' . translate('Legende:', 'de', $GLOBALS['lang']) . ' ' . getAlt($resultsToPrint) . '" src="data:image/png;base64,' . drawLegendDark($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                                echo '<br><div id="right_circle_graph_' . $i . '">';

                                echo '<img class="light-mode" alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircleLight($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                                echo '<img class="dark-mode" alt="'.translate('ein automatisch generiertes Kreisdiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawCircleDark($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                                echo '</div><div id="right_rectangle_graph_' . $i . '" style="display: none;">';

                                echo '<img class="light-mode" alt="'.translate('ein automatisch generiertes Säulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangleLight($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';
                                echo '<img class="dark-mode" alt="'.translate('ein automatisch generiertes Säulendiagramm', 'de', $GLOBALS['lang']).'" src="data:image/png;base64,' . drawRectangleDark($resultsToPrint) . '" onerror="this.onerror=null;this.src=\'images/error_img.png\';this.alt=\''.translate('Hier ist wohl ein Fehler passiert.', 'de', $GLOBALS['lang']).' \';" />';

                                echo "</div><br>";
                                echo PHP_EOL . "</div><hr>";
                                if ($result["question_type"] == "multiple_choice" || $result["question_type"] == "single_choice" || $result["question_type"] == "dropdown") $i++;
                            }
                            else {
                                echo '<div id="right_circle_graph_' . $i . '" style="display: none;"></div>';
                                echo '<div id="right_rectangle_graph_' . $i . '" style="display: none;"></div>';
                                echo '<div id="right_circle_' . $i . '" style="display: none;"></div>';
                                echo '<div id="right_rectangle_' . $i . '" style="display: none;"></div>';
                                echo '<div id="right_both_' . $i . '" style="display: none;"></div>';

                                $count = 0;
                                $this_responses = "";
                                foreach ($result_data['responses'] as $response) {
                                    if (isset($response['response_text']) && trim($response['response_text']) !== '') {
                                        $this_responses .= "<div id='free_text_right_" . $i . "_" . $count + 1 . "'>#" . $count + 1 .": " . "<p><br>" . translate($response['response_text'], 'de', $GLOBALS['lang']) . "</p></div><hr>";
                                        $count++;
                                    }
                                }
                                echo "";
                                echo "<p>n=" . $count . "</p><hr>";
                                echo substr($this_responses, 0, -4); //without the last <hr>
                                echo "";
                                echo "</div><hr>";
                                $i++;
                            }
                        }
                    }
                    else {
                        echo "<div id='right_0'><hr><h2></h2><p></p><br><br><br><br><br><br><br><br></div>";
                    }
                    $questions_count_for_java = $i; //to manage changing graphs
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

            // Utility function to get the current URL's query parameters as an object
            function getQueryParams() {
                const urlParams = new URLSearchParams(window.location.search);
                let params = {};
                for (const [key, value] of urlParams.entries()) {
                    params[key] = value;
                }
                return params;
            }

            // Utility function to generate a new URL with updated parameters
            function generateUrl(params) {
                const baseUrl = window.location.protocol + "//" + window.location.host;
                const urlParams = new URLSearchParams(params).toString();
                return `${baseUrl}?${urlParams}#choose_anchor`;
            }

            // Event handler functions
            function result_scheme_left_Q() {
                const params = getQueryParams();
                params.leftQ = document.getElementById("result_scheme_left_Q").value;
                params.leftA = 0;
                window.location = generateUrl(params);
            }

            function result_scheme_left_A() {
                const params = getQueryParams();
                params.leftA = document.getElementById("result_scheme_left_A").value;
                window.location = generateUrl(params);
            }

            function result_scheme_right_Q() {
                const params = getQueryParams();
                params.rightQ = document.getElementById("result_scheme_right_Q").value;
                params.rightA = 0;
                window.location = generateUrl(params);
            }

            function result_scheme_right_A() {
                const params = getQueryParams();
                params.rightA = document.getElementById("result_scheme_right_A").value;
                window.location = generateUrl(params);
            }

            // Assigning event handlers
            document.getElementById('result_scheme_left_Q').onchange = result_scheme_left_Q;
            if (document.getElementById('result_scheme_left_A') !== null) {
                document.getElementById('result_scheme_left_A').onchange = result_scheme_left_A;
            }

            document.getElementById('result_scheme_right_Q').onchange = result_scheme_right_Q;
            if (document.getElementById('result_scheme_right_A') !== null) {
                document.getElementById('result_scheme_right_A').onchange = result_scheme_right_A;
            }


            var left_choices = document.getElementById('left_choices');
            var right_choices = document.getElementById('right_choices');

            if (left_choices.clientHeight > right_choices.clientHeight) {
                right_choices.style.height = left_choices.clientHeight.toString() + "px";
            } else if (left_choices.clientHeight < right_choices.clientHeight) {
                left_choices.style.height = right_choices.clientHeight.toString() + "px";
            }


            let resultsCount = parseInt(<?php echo $questions_count_for_java - 1; ?>);

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


            let left_counts = parseInt(<?php echo $questions_count_for_java; ?>);

            window.addEventListener("load", function() {
                <?php
                for ($i = 0; $i < $questions_count_for_java; $i++) {
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
                    adjustHeight();
                }
                function rectangleLeft<?php echo $i; ?>() {
                    document.getElementById("left_circle_graph_<?php echo $i; ?>").style.display = "none";
                    document.getElementById("left_rectangle_graph_<?php echo $i; ?>").style.display = "block";
                    adjustHeight();
                }
                function bothLeft<?php echo $i; ?>() {
                    document.getElementById("left_circle_graph_<?php echo $i; ?>").style.display = "block";
                    document.getElementById("left_circle_graph_<?php echo $i; ?>").style.float = "left";
                    document.getElementById("left_rectangle_graph_<?php echo $i; ?>").style.display = "block";
                    adjustHeight();
                }


                <?php
                }
                ?>

                <?php
                for ($i = 0; $i < $questions_count_for_java; $i++) {
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
                    adjustHeight();
                }
                function rectangleRight<?php echo $i; ?>() {
                    document.getElementById("right_circle_graph_<?php echo $i; ?>").style.display = "none";
                    document.getElementById("right_rectangle_graph_<?php echo $i; ?>").style.display = "block";
                    adjustHeight();
                }
                function bothRight<?php echo $i; ?>() {
                    document.getElementById("right_circle_graph_<?php echo $i; ?>").style.display = "block";
                    document.getElementById("right_circle_graph_<?php echo $i; ?>").style.float = "right";
                    document.getElementById("right_rectangle_graph_<?php echo $i; ?>").style.display = "block";
                    adjustHeight();
                }


                <?php
                }
                ?>
            });
        </script>
        <div style="clear: both"></div>

        <?php
    }
}
}
?>




<script type="application/javascript">
    function scrollToElement(element) {
        const headerOffset = 200;
        const elementPosition = element.getBoundingClientRect().top + window.scrollY;
        const offsetPosition = elementPosition - headerOffset;

        window.scrollTo({
            top: offsetPosition,
            behavior: "smooth"
        });

        // Searches the parent <p> element
        const parentElement = element.parentNode;

        // Adds the highlight class to the parent <p> element
        parentElement.classList.add("highlight");

        // Removes the highlight class after 5 seconds
        setTimeout(function() {
            parentElement.classList.remove("highlight");
        }, 5000);
    }

    document.getElementById("submit-btn").addEventListener("click", function(event) {
        event.preventDefault(); // Prevents the form from being submitted regardless of validation

        const requiredFields = document.querySelectorAll("[required]");
        let firstInvalidField = null;

        for (const field of requiredFields) {
            if (!field.checkValidity()) {
                firstInvalidField = field;
                break;
            }
        }

        if (firstInvalidField) {
            setTimeout(function() {
                scrollToElement(firstInvalidField);
            }, 100);
        } else {
            // Adds a function to manually submit the form when all required fields are filled in
            document.getElementById("thisSurvey").submit();
        }
    });




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
        //console.log(divId);

        if (divToToggle && divToToggle.nextElementSibling) {
            if (divToToggle.nextElementSibling.nodeType === Node.ELEMENT_NODE) {
                if (divToToggle.nextElementSibling.classList.contains("toggleDiv")) {
                    divToToggle.nextElementSibling.style.display = "block";
                }
            }
        }
    }


    function adjustHeight() {
        var leftDivs = document.querySelectorAll("[id^='left_']");
        var rightDivs = document.querySelectorAll("[id^='right_']");

        var smallerLength = leftDivs.length;
        if (rightDivs.length < smallerLength) smallerLength = rightDivs.length;

        for (var i = 0; i < smallerLength; i++) {
            // Reset heights to "auto" before determining the maximum height
            leftDivs[i].style.height = "auto";
            rightDivs[i].style.height = "auto";
        }
        for (var i = 0; i < smallerLength; i++) {
            var leftDivHeight = leftDivs[i].clientHeight;
            var rightDivHeight = rightDivs[i].clientHeight;

            var maxHeight = Math.max(leftDivHeight, rightDivHeight);
            leftDivs[i].style.height = maxHeight + "px";
            rightDivs[i].style.height = maxHeight + "px";
        }
    }

    function adjustFreeText() {
        var leftDivs = document.querySelectorAll("[id^='free_text_left_']");
        var rightDivs = document.querySelectorAll("[id^='free_text_right_']");

        var smallerLength = leftDivs.length;
        if (rightDivs.length < smallerLength) smallerLength = rightDivs.length;

        for (var i = 0; i < smallerLength; i++) {
            // Reset heights to "auto" before determining the maximum height
            leftDivs[i].style.height = "auto";
            rightDivs[i].style.height = "auto";
        }

        for (var i = 0; i < smallerLength; i++) {
            var leftDivHeight = leftDivs[i].clientHeight;
            var rightDivHeight = rightDivs[i].clientHeight;

            var maxHeight = Math.max(leftDivHeight, rightDivHeight);
            leftDivs[i].style.height = maxHeight + "px";
            rightDivs[i].style.height = maxHeight + "px";
        }
    }

    document.addEventListener("DOMContentLoaded", function(event) {
        adjustHeight();
        adjustFreeText();
        setTimeout(function() {
            adjustHeight();
            adjustFreeText();
        }, 500);
        setTimeout(function() {
            adjustHeight();
            adjustFreeText();
        }, 1000);
        setTimeout(function() {
            adjustFreeText();
        }, 1500);
        setTimeout(function() {
            adjustFreeText();
        }, 2000);
        setTimeout(function() {
            adjustFreeText();
        }, 2500);
        setTimeout(function() {
            adjustFreeText();
        }, 3000);
        setTimeout(function() {
            adjustFreeText();
        }, 3500);
    });
</script>