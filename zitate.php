<?php
function zitat($string) {
    $string = strtolower($string);
    $zitat = "";

    if ($string == "lebenswirklichkeit") $zitat = "
<p><i>Die Wissenschaft ist Teil der Lebenswirklichkeit; es ist das Was, das Wie und Warum von allem in unserer Erfahrung.</i><br>Rachel Carson</p>
<br>
";
    elseif ($string == "antworten") $zitat = "
<p><i>Wer neue Antworten will, muss neue Fragen stellen.</i><br>Johann Wolfgang von Goethe</p>
<br>
";

    return translate($zitat, "de", $GLOBALS["lang"]);
}
?>