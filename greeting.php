<section id="">
    <header>
        <h1>eh-umfragen.de - <?php echo translate("Eure Umfragen", "de", $GLOBALS["lang"]); ?></h1>
        <?php echo zitat("antworten"); ?>
        <br>
        <h2><?php echo translate("Aktuelle Umfragen", "de", $GLOBALS["lang"]); ?></h2>
        <?php
        if (isset($_GET["draft"]) && $_GET["draft"] == "1") $draft = "&draft=1";
        else  $draft = "";
        if (!isset($surveys)) $surveys = [];
        if (sizeof($surveys) > 0) {
            for ($i = sizeof($surveys) - 1; $i >= 0 ; $i--) {
                $thisid = utf8Encode($surveys[$i][0][0]);
                if (get_active($thisid) != 0) {
                    $activestate = "offen seit ". date("d. m. Y, H:i", get_since($thisid)) . " Uhr";
                    $activestate = translate($activestate, "de", $GLOBALS["lang"]);
                    echo "<a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . $draft . "' rel='nofollow'><h3>" .
                        "&emsp;&emsp;#" . $thisid . " ".
                        translate($surveys[$i][0][1], "de", $GLOBALS["lang"]) . " (".
                        $activestate
                        .")</h3><p>&emsp;&emsp;" . translate($surveys[$i][0][2], "de", $GLOBALS["lang"]) . "</p></a>";
                }
            }
        } else echo "<p>&emsp;&emsp;Schau bald wieder vorbei. Momentan gibt es keine aktiven Umfragen.</p>";
        echo "<br>";
        echo translate("
        <h2>Umfragenergebnisse</h2>
        ", "de", $GLOBALS["lang"]);
        $hasresults = 0;
        for ($i = sizeof($surveys) - 1; $i >= 0 ; $i--) {
            $thisid = utf8Encode($surveys[$i][0][0]);
            if (get_active($thisid) == 0 && get_hasresults($thisid) == 1) {
                $hasresults++;
                $inactivesince = get_inactivesince($thisid);
                $since = get_since($thisid);
                $wasactive = $inactivesince - $since;
                $wasactive = translate($wasactive, "de", $GLOBALS["lang"]);
                $activestate = "war " . secondsToTime($wasactive) . " offen";
                $activestate = translate($activestate, "de", $GLOBALS["lang"]);
                echo "<a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . $draft . "' rel='nofollow'><h3>" .
                    "&emsp;&emsp;#" . $thisid . " ".
                    translate($surveys[$i][0][1], "de", $GLOBALS["lang"]) . " (" .
                    $activestate
                    . ")</h3><p>&emsp;&emsp;" . translate($surveys[$i][0][2], "de", $GLOBALS["lang"]) . "</p></a>";
            }
        }
        if ($hasresults === 0)
            echo translate("
<p>&emsp;&emsp;Schau bald wieder vorbei. Momentan gibt es noch keine Ergebnisse.</p><br>
        ", "de", $GLOBALS["lang"]);
        echo "<br>";
        echo translate("
        <h2>Geschlossene Umfragen - In Auswertung</h2>
        ", "de", $GLOBALS["lang"]);
        $ineval = 0;
        for ($i = sizeof($surveys) - 1; $i >= 0 ; $i--) {
            $thisid = utf8Encode($surveys[$i][0][0]);
            if (get_active($thisid) == 0 && get_hasresults($thisid) == 0) {
                $ineval++;
                $activestate = "geschlossen seit " . date("d. m. Y, H:i", get_inactivesince($thisid)) . " Uhr";
                echo translate("
                <a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . $draft . "' rel='nofollow'><h3>" .
                    "&emsp;&emsp;#" . $thisid . " ".
                    $surveys[$i][0][1] . " (" .
                    $activestate
                    . ")</h3><p>&emsp;&emsp;" . $surveys[$i][0][2] . "</p></a>
        ", "de", $GLOBALS["lang"]);
            }
        }
        if ($ineval === 0)
            echo translate("<p>&emsp;&emsp;Momentan sind keine Umfragen in Auswertung.</p><br><br>
        ", "de", $GLOBALS["lang"]);
        ?>
    </header>
</section>