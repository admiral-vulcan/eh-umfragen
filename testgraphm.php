<section id="intro">
    <header>


        <?php
        //$results = loadResults(2);
        $results = loadRelativeResults("Präsenzseminare", 8, 0);

/*
        echo $results["QNA"][0][0] . "<br>";
        for ($i = 0; $i < sizeof($results["counts"][0]); $i++) {
            echo $results["QNA"][0][$i+1] . ": ";
            echo $results["counts"][0][$i] . "<br>";
            //$results["counts"]["assign"][0][$results["QNA"][0][$i+1]] = $results["counts"][0][$i];
        }
*/
        //echo '<br><img src="data:image/png;base64,' . drawRectangle($stuff) . '" />';

        //$stuff = array("Katze" => 40, "Hund" => 65, "Nagetier" => 100, "Sonstiges" => 13, "blah" => 13, "lol" => 13, "hi" => 13, "sa" => 13, "fhg" => 13, "sfg" => 13, "fhj" => 90, "fg" => 13);



        echo "<h1>" . $results["name"] . " - Ergebnisse, ";
        if (isset($results["relative"])) echo "in Relation zur Frage \"" . $results["relative"]["question"] . "\" und Antwort \"" . $results["relative"]["answer"] . "\", ";
        echo "n=" . $results["n"] . "</h1>";
/*
        for ($i = 0; $i < sizeof($results["counts"]) - 1; $i++) {
            echo "<h2>" . $results["QNA"][$i][0] . "</h2>";
            if ($results["counts"]["assign"][$i][0] != -1) {
                echo '<br><img src="data:image/png;base64,' . drawLegend($results["counts"]["assign"][$i]) . '" />';
                echo '<br><img src="data:image/png;base64,' . drawCircle($results["counts"]["assign"][$i]) . '" />';
                echo '<img src="data:image/png;base64,' . drawRectangle($results["counts"]["assign"][$i]) . '" />';
            }
            else {
                echo "<h2>" . $results["open"][$i]["count"] . " Antworten:</h2>";
                $k = 0;
                for ($j = 0; $j < sizeof($results["open"][$i]); $j++) {
                    if ($results["open"][$i][$j] != 0) {
                        $k++;
                        echo "<p>#" . $k . ": " . $results["open"][$i][$j] . "</p><br>";
                    }
                }
            }
            echo "<hr><br>";
        }
*/

        for ($i = 0; $i < sizeof($results["counts"]) - 1; $i++) {
            if ($results["counts"]["assign"][$i][0] != -1 && $results["QNA"][$i][0] != $results["relative"]["question"]) {
                echo "<h2>" . $results["QNA"][$i][0] . "</h2>";
                echo '<br><img src="data:image/png;base64,' . drawLegend($results["counts"]["assign"][$i]) . '" />';
                echo '<br><img src="data:image/png;base64,' . drawCircle($results["counts"]["assign"][$i]) . '" />';
                echo '<img src="data:image/png;base64,' . drawRectangle($results["counts"]["assign"][$i]) . '" />';
                echo "<hr><br>";
            }
        }
        for ($i = 0; $i < sizeof($results["counts"]) - 1; $i++) {
            if ($results["counts"]["assign"][$i][0] == -1) {
                echo "<h2>" . $results["QNA"][$i][0] . "</h2>";
                echo "<h2>" . $results["open"][$i]["count"] . " Antworten:</h2>";
                $k = 0;
                for ($j = 0; $j < sizeof($results["open"][$i]); $j++) {
                    if ($results["open"][$i][$j] != 0) {
                        $k++;
                        echo "<p>#" . $k . ": " . $results["open"][$i][$j] . "</p><br>";
                    }
                }
                echo "<hr><br>";
            }
        }

        ?>
    </header>
</section>
<script type="text/javascript">
    //location.reload();
    //if (Cookies.get("prefers_color_scheme") === undefined)
    //alert(Cookies.get("prefers_color_scheme"));

</script>