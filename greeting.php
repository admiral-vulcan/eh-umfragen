<section id="">
    <header>
        <h1>EH-Umfragen.de - Eure Umfragen</h1>
        <p><i>Wer neue Antworten will, muss neue Fragen stellen.</i><br>Johann Wolfgang von Goethe</p>
        <br>
        <h2>Aktuelle Umfragen</h2>
        <?php
        if (!isset($surveys)) $surveys = [];
        if (sizeof($surveys) > 0) {
            for ($i = sizeof($surveys) - 1; $i >= 0 ; $i--) {
                $thisid = utf8Encode($surveys[$i][0][0]);
                if (get_active($thisid) != 0) {
                    $activestate = "offen seit ". date("d. m. Y, H:i", get_since($thisid)) . " Uhr";
                    echo "<a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . "' rel='nofollow'><h3>" .
                        "&emsp;&emsp;#" . $thisid . " ".
                        $surveys[$i][0][1] . " (".
                        $activestate
                        .")</h3><p>&emsp;&emsp;" . $surveys[$i][0][2] . "</p></a>";
                }
            }
        } else echo "<p>&emsp;&emsp;Schau bald wieder vorbei. Momentan gibt es keine aktiven Umfragen.</p>";
        ?>
        <br>
        <h2>Umfragenergebnisse</h2>
        <?php
        $hasresults = 0;
        for ($i = sizeof($surveys) - 1; $i >= 0 ; $i--) {
            $thisid = utf8Encode($surveys[$i][0][0]);
            if (get_active($thisid) == 0 && get_hasresults($thisid) == 1) {
                $hasresults++;
                $inactivesince = get_inactivesince($thisid);
                $since = get_since($thisid);
                $wasactive = $inactivesince - $since;
                $activestate = "war " . secondsToTime($wasactive) . " offen";
                echo "<a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . "' rel='nofollow'><h3>" .
                    "&emsp;&emsp;#" . $thisid . " ".
                    $surveys[$i][0][1] . " (" .
                    $activestate
                    . ")</h3><p>&emsp;&emsp;" . $surveys[$i][0][2] . "</p></a>";
            }
        }
        if ($hasresults === 0) echo "<p>&emsp;&emsp;Schau bald wieder vorbei. Momentan gibt es noch keine Ergebnisse.</p><br>";
        ?>
        <br>
        <h2>Geschlossene Umfragen - In Auswertung</h2>
        <?php
        $ineval = 0;
        for ($i = sizeof($surveys) - 1; $i >= 0 ; $i--) {
            $thisid = utf8Encode($surveys[$i][0][0]);
            if (get_active($thisid) == 0 && get_hasresults($thisid) == 0) {
                $ineval++;
                $activestate = "geschlossen seit " . date("d. m. Y, H:i", get_inactivesince($thisid)) . " Uhr";
                echo "<a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . "' rel='nofollow'><h3>" .
                    "&emsp;&emsp;#" . $thisid . " ".
                    $surveys[$i][0][1] . " (" .
                    $activestate
                    . ")</h3><p>&emsp;&emsp;" . $surveys[$i][0][2] . "</p></a>";
            }
        }
        if ($ineval === 0) echo "<p>&emsp;&emsp;Momentan sind keine Umfragen in Auswertung.</p><br><br>";
        ?>
    </header>
</section>