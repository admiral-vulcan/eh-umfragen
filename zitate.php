<?php
function zitat($string) {
    $string = strtolower($string);

    $zitat =  "<div class='quote'>";
    if ($string == "lebenswirklichkeit") $zitat .= "<p><i>Die Wissenschaft ist Teil der Lebenswirklichkeit; es ist das Was, das Wie und Warum von allem in unserer Erfahrung.</i><br>Rachel Carson</p><br>";
    elseif ($string == "antworten") $zitat .= "<p><i>Wer neue Antworten will, muss neue Fragen stellen.</i><br>Johann Wolfgang von Goethe</p><br>";
    elseif ($string == "neugierde") $zitat .= "<p><i>Die Neugierde der Kinder ist der Wissensdurst nach Erkenntnis, darum sollte man diese in ihnen fördern und ermutigen.</i><br>John Locke</p><br>";
    $zitat .=  "</div>";

    return translate($zitat, "de", $GLOBALS["lang"]);
}
?>