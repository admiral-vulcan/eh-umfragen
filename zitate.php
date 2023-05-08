<?php
function zitat($string) {
    $string = strtolower($string);

    $zitatText = "";
    $zitatAutorIn ="";
    if ($string == "lebenswirklichkeit") {
        $zitatText = "Die Wissenschaft ist Teil der Lebenswirklichkeit; es ist das Was, das Wie und Warum von allem in unserer Erfahrung.";
        $zitatAutorIn = "Rachel Carson";
    }
    elseif ($string == "antworten") {
        $zitatText = "Wer neue Antworten will, muss neue Fragen stellen.";
        $zitatAutorIn = "Johann Wolfgang von Goethe";
    }
    elseif ($string == "neugierde") {
        $zitatText = "Die Neugierde der Kinder ist der Wissensdurst nach Erkenntnis, darum sollte man diese in ihnen fördern und ermutigen.";
        $zitatAutorIn = "John Locke";
    }

    return "<div class='quote'><p><i>" . translate($zitatText, "de", $GLOBALS["lang"]) . "</i><br>" . $zitatAutorIn . "</p><br></div>";
}
?>