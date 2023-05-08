<?php
use EHUmfragen\DatabaseModels\Surveys;
$allSurveys = new Surveys();
$allSurveyIds = $allSurveys->getAllSurveyIds();
?>
<section id="">
    <header>
        <h1>eh-umfragen.de - <?php echo translate("Eure Umfragen", "de", $GLOBALS["lang"]); ?></h1>
        <?php echo zitat("antworten"); ?>
        <br>
        <h2>
            <?php

            //active surveys
            echo translate("Aktuelle Umfragen", "de", $GLOBALS["lang"]); ?></h2>
        <?php
        if (isset($_GET["draft"]) && $_GET["draft"] == "1") $draft = "&draft=1";
        else  $draft = "";
        $any_active = false;
        foreach ($allSurveyIds as $survey_id) {
            $survey = $allSurveys->getSurvey($survey_id);
            if ($survey["is_active"] && $survey["is_visible"]) {
                $any_active = true;
                $activestate = "offen seit ". $survey["activated_at"];
                $activestate = translate($activestate, "de", $GLOBALS["lang"]);
                echo "<a href='?survey=" . urlencode($survey["title"]) . $draft . "' rel='nofollow'><h3>" .
                    "&emsp;&emsp;#" . $survey_id . " ".
                    translate($survey["title"], "de", $GLOBALS["lang"]) . " (".
                    $activestate
                    .")</h3><p>&emsp;&emsp;" . translate($survey["subtitle"], "de", $GLOBALS["lang"]) . "</p></a>";
            }
        }
        if (!$any_active) echo translate("<p>&emsp;&emsp;Schau bald wieder vorbei. Momentan gibt es keine aktiven Umfragen.</p>", "de", $GLOBALS["lang"]);
        echo "<br>";

        //inactive surveys that have results
        echo translate("
        <h2>Umfrageergebnisse</h2>
        ", "de", $GLOBALS["lang"]);
        $any_results = false;
        foreach ($allSurveyIds as $survey_id) {
            $survey = $allSurveys->getSurvey($survey_id);
            if ($survey["has_results"] && $survey["is_visible"]) {
                $any_results = true;
                $resultstate = "hat Ergebnisse seit ". $survey["results_received_at"];
                $resultstate = translate($resultstate, "de", $GLOBALS["lang"]);
                echo "<a href='?survey=" . urlencode($survey["title"]) . $draft . "' rel='nofollow'><h3>" .
                    "&emsp;&emsp;#" . $survey_id . " ".
                    translate($survey["title"], "de", $GLOBALS["lang"]) . " (".
                    $resultstate
                    .")</h3><p>&emsp;&emsp;" . translate($survey["subtitle"], "de", $GLOBALS["lang"]) . "</p></a>";
            }
        }
        if (!$any_results) echo translate("<p>&emsp;&emsp;Schau bald wieder vorbei. Momentan gibt es noch keine Ergebnisse.</p>", "de", $GLOBALS["lang"]);
        echo "<br>";

        //inactive surveys that do not have results
        echo translate("
        <h2>Geschlossene Umfragen - In Auswertung</h2>
        ", "de", $GLOBALS["lang"]);
        $inactive_no_results = false;
        foreach ($allSurveyIds as $survey_id) {
            $survey = $allSurveys->getSurvey($survey_id);
            if ($survey["inactive"] && !$survey["has_results"] && $survey["is_visible"]) {
                $inactive_no_results = true;
                $inactivestate = "ist geschlossen seit ". $survey["inactivated_at"];
                $inactivestate = translate($inactivestate, "de", $GLOBALS["lang"]);
                echo "<a href='?survey=" . urlencode($survey["title"]) . $draft . "' rel='nofollow'><h3>" .
                    "&emsp;&emsp;#" . $survey_id . " ".
                    translate($survey["title"], "de", $GLOBALS["lang"]) . " (".
                    $inactivestate
                    .")</h3><p>&emsp;&emsp;" . translate($survey["subtitle"], "de", $GLOBALS["lang"]) . "</p></a>";
            }
        }
        if (!$inactive_no_results) echo translate("<p>&emsp;&emsp;Momentan sind keine Umfragen in Auswertung.</p>", "de", $GLOBALS["lang"]);
        echo "<br>";
        ?>
    </header>
</section>