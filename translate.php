<?php
require_once ("gitignore/deepLcred.php");
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/

if (!isset($_COOKIE['firstTimeLang']) || $_COOKIE['firstTimeLang'] != $GLOBALS["lang"]) {

    if ($GLOBALS["lang"] == "en") alert(translate("Experimentelle Übersetzungsfunktion", "de", $GLOBALS["lang"]), translate("Diese Seite ist auf Deutsch geschrieben und wird automatisch mit <a href='https://www.deepl.com/' target='_blank' rel='nofollow'>DeepL</a> übersetzt. Möglicherweise ist aktuell noch nicht alles übersetzt. Diese Funktion kann zudem beim ersten Laden einer neuen Sprache sehr lange Ladezeiten haben und potenziell Fehler, wie unbeabsichtigte Formulierungen, Widersprüche, Doppeldeutigkeiten oder ähnliche verursachen. Deshalb distanzieren wir uns von der Übersetzung und bieten Euch im Menü immer an, die Sprache zur <a href='/?lang=de'>deutschen Originalversion</a> zu wechseln.<br>Diese Warnung wird nur ein Mal pro Sprachwechsel angezeigt.", "de", $GLOBALS["lang"]), "warning");
    elseif ($GLOBALS["lang"] != "de") alert(translate("Experimentelle Übersetzungsfunktion", "de", $GLOBALS["lang"]), translate("Diese Seite ist auf Deutsch geschrieben und wird automatisch mit <a href='https://www.deepl.com/' target='_blank' rel='nofollow'>DeepL</a> übersetzt. Möglicherweise ist aktuell noch nicht alles übersetzt. Diese Funktion kann zudem beim ersten Laden einer neuen Sprache sehr lange Ladezeiten haben und potenziell Fehler, wie unbeabsichtigte Formulierungen, Widersprüche, Doppeldeutigkeiten oder ähnliche verursachen. Deshalb distanzieren wir uns von der Übersetzung und bieten Euch im Menü immer an, die Sprache zur <a href='/?lang=de'>deutschen Originalversion</a> zu wechseln.<br>Oder wechsle alternativ zur <a href='/?lang=en'>englischen</a> Version, da diese recht gut funktioniert.<br>Diese Warnung wird nur ein Mal pro Sprachwechsel angezeigt.", "de", $GLOBALS["lang"]), "warning");

    setcookie("firstTimeLang", $GLOBALS["lang"], time() + (86400 * 30 * 365), "/"); //86400 is 1 day
}

function translate($source_text, $source_lang = "en", $target_lang = "de") {
    $source_lang = strtoupper($source_lang);
    $target_lang = strtoupper($target_lang);

    if ($source_lang == $target_lang) return $source_text;

    $textHash = hash('sha256', $source_lang.$target_lang.$source_text);

    if (apcu_exists($textHash)) {
        //if (preg_match("/[0-9]/", $source_text)) apcu_delete($textHash); //delete this entry
        //if (str_contains($source_text, "Goethe")) apcu_delete($textHash); //delete this entry
        //if ($source_text == "3. Computerraum (A-Gebäude, 2. OG)") apcu_delete($textHash); //delete this entry
        //if ($target_lang == "FR") apcu_delete($textHash); //delete if certain language
        return apcu_fetch($textHash);
    }

    $source_text = removeNewLines($source_text);
    if ($target_lang == "EN") $source_text = replaceDotWithColon($source_text);


    $stripHtmlTags = stripHtmlTags($source_text);
    $source_text = $stripHtmlTags["str"];
    $startingTags = $stripHtmlTags["startingTags"];
    $closingTags = $stripHtmlTags["closingTags"];

    $numPres = numeralPreserve($source_text);
    $source_text = $numPres["str"];
    $numPres = $numPres["num"];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api-free.deepl.com/v2/translate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "auth_key=" . $GLOBALS["deepLcred"] . "&text=".$source_text."&source_lang=".$source_lang."&target_lang=".$target_lang); //pro version:  . "&formality=personal" or formal

    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch) && $_SERVER['HTTP_HOST'] === "test.eh-umfragen.de") {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    $translatedWords = json_decode($result, true); // Decode the word
    $result = $translatedWords['translations'][0]['text']; // Search the word
    $result = preg_replace("/<br>\./", "<br>", $result); //dirty hack for some languages... try not to translate <br> in future versions! :(
    $result = $numPres . $result;

    if (str_contains($source_text, "?lang=de")) {
        $result = preg_replace("/\?lang=[a-zA-Z]{2}/", "$1?lang=de", $result, 1);
        $result = str_replace("?lang=".strtolower($target_lang), "?lang=en", $result);
    }


    apcu_store($textHash, $result);
    return $startingTags.$result.$closingTags; // Display the word
}


function numeralPreserve($str) {
    $output = array("str" => $str, "num" => "");
    if (preg_match("/(.*?)(?:^|>)([0-9]+)\.([^0-9].*)/", $str, $matches)) {
        if (preg_match("/>([0-9]+)/", $str)) $output["num"] = $matches[1].">".$matches[2] . ". ";
        else $output["num"] = $matches[1].$matches[2] . ". ";
        $output["str"] = $matches[3];
    }
    return $output;
}
function replaceDotWithColon($str) {
    $str = preg_replace('/(\d+)\. /', '$1: ', $str);
    return $str;
}
function removeNewLines($str) {
    return str_replace(array("\r\n", "\r", "\n"), '', $str);
}
function stripHtmlTags($str) {
    $startingTags = "";
    $closingTags = "";

    while (true) {
        $strippedTags = 0;
        preg_match('/^(<[^>]*>)/', $str, $matches);
        if (count($matches) > 0) {
            $startingTags .= $matches[1];
            $str = preg_replace('/^(<[^>]*>)/', '', $str, 1);
            $strippedTags++;
        }
        preg_match('/(<\/[^>]*>$)/', $str, $matches);
        if (count($matches) > 0) {
            $closingTags = $matches[1] . $closingTags;
            $str = preg_replace('/(<\/[^>]*>$)/', '', $str, 1);
            $strippedTags++;
        }
        if ($strippedTags == 0) {
            break;
        }
    }
    return ["str" => $str, "startingTags" => $startingTags, "closingTags" => $closingTags, "strippedTags" => $strippedTags];
}

$waitForTranslation = alert("Please wait", "Please wait for the language to load. If it's the first time the server handles this language's data, it could take a while...", "info", false);

//echo numeralPreserve("3. Computerraum (A-Gebäude, 2. OG)", "EN")["num"];

// usage
// echo translate("How are you today?", "DE");
//echo translate("Hallo, wie geht es dir?", "de", "ro");
/*
$z = "
<p><i>Die Wissenschaft ist Teil der Lebenswirklichkeit; es ist das Was, das Wie und Warum von allem in unserer Erfahrung.</i><br>Rachel Carson</p>
<br>
";
echo stripHtmlTags($z)["str"];

$str = "<p>
1. Außenbereich des A-Gebäudes";
echo numeralPreserve($str, "en")["num"];
echo numeralPreserve($str, "en")["str"];
*/
/*
$stuff = stripHtmlTags("<body><p><b>this is <u>very</u>meaningful</b></p></body>");
echo $stuff["startingTags"];
echo translate($stuff["str"], "en", "fr");
echo $stuff["closingTags"];

echo preg_replace("/\?lang=[a-zA-Z]{2}/", "$1?lang=de", "lol /?lang=fr lol /?lang=fr lol", 1);

*/
?>
<script type="application/javascript">
    window.addEventListener("load", function() {
        // Get the select element
        const select = document.querySelector('.language_select');

        // Listen for change event on the select element
        select.addEventListener('change', function() {
            // Get the selected value
            const selectedValue = this.value;

            showAlert(<?php echo $waitForTranslation; ?>);

            // Wait 200ms before reloading the page
            setTimeout(function() {
                // Get the current URL
                let currentUrl = window.location.href;

                // Check if the selected value is "lang_auto"
                if (selectedValue === "lang_auto") {
                    // Delete the "language" cookie
                    document.cookie = 'language=; expires=-1;';
                    document.cookie = 'firstTimeLang=; expires=-1;';

                    // Strip the "lang" parameter from the URL
                    currentUrl = currentUrl.replace(/[?&]lang=\w+/, "");
                } else {
                    // Check if the URL already contains a query string
                    if (currentUrl.indexOf('?lang=') !== -1) {
                        // If so, replace the existing lang parameter with the new selected value
                        currentUrl = currentUrl.replace(/lang=\w+/, `lang=${selectedValue}`);
                    } else if (currentUrl.indexOf('&lang=') !== -1) {
                        // If so, replace the existing lang parameter with the new selected value
                        currentUrl = currentUrl.replace(/lang=\w+/, `lang=${selectedValue}`);
                    } else if (currentUrl.indexOf('?') === -1) {
                        // If not, append the selected value as a new query string
                        currentUrl += `?lang=${selectedValue}`;
                    } else {
                        // If so, append the selected value to the existing query string
                        currentUrl += `&lang=${selectedValue}`;
                    }
                }

                // Reload the page with the updated URL
                window.location.href = currentUrl;
            }, 200);
        });
    });
</script>
