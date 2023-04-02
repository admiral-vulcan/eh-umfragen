<?php

require_once ('autoload.php');
require_once 'classes/DatabaseModels/Results.php';

use EHUmfragen\DatabaseModels\Results;

$allResults = new Results();
if (isset($_GET["mode"]) && isset($_GET["survey_id"])) {
    if ($_GET["mode"] === "results") {
        $download = $allResults->downloadResultsbySurveyId($_GET["survey_id"]);
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . iconv("UTF-8", "WINDOWS-1252", str_replace(' ', '_', $download['title'])) . '_Ergebnisse.csv"');
        echo $download['csv'];
    }
    elseif ($_GET["mode"] === "meta") {
        $download = $allResults->downloadMetasbySurveyId($_GET["survey_id"]);
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . iconv("UTF-8", "WINDOWS-1252", str_replace(' ', '_', $download['title'])) . '_Metadaten.csv"');
        echo $download['csv'];
    }
}
?>