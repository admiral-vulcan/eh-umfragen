<?php
$url =  "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$this_uri = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
$GLOBALS["testDomain"] = $_SERVER['HTTP_HOST'] === "test.eh-umfragen.de";
function getDefaultLanguage() {
    $validLanguages = array("en", "de", "fr", "es", "it", "pt", "ru", "pl", "nl", "tr", "el", "sv", "da", "fi", "ro");
    if (isset($_GET["lang"]) && in_array($_GET["lang"], $validLanguages)) {
        setcookie('language', $_GET["lang"], time() + (86400 * 30 * 365), "/"); // 86400 = 1 day
        return $_GET["lang"];
    } elseif (isset($_COOKIE["language"]) && in_array($_COOKIE["language"], $validLanguages)) {
        return $_COOKIE["language"];
    } else {
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (in_array($language, $validLanguages)) {
            return $language;
        } else {
            return "en";
        }
    }
}
$GLOBALS["lang"] = getDefaultLanguage();
function verToInt($str) {
    $pos = strpos($str,'.');
    if ($pos !== false) {
        $str = substr($str,0,$pos+1) . str_replace('.','',substr($str,$pos+1));
    }
    return floatval($str);
}
if ($GLOBALS["testDomain"]) {
    $start = hrtime(true);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    if (isset($_GET["warnings"]) && $_GET["warnings"] == "1") error_reporting(E_ALL);
    else error_reporting(E_ERROR);
}
function get_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

        $ip = $_SERVER['HTTP_CLIENT_IP'];

    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    } else {

        $ip = $_SERVER['REMOTE_ADDR'];

    }
    return $ip;
}
function getLanguage($lang_code) {
// switch statement to match the lang_code with the language
    switch ($lang_code) {
        case "de":
            return "Deutsch";
        case "en":
            return "Englisch";
        case "fr":
            return "Französisch";
        case "it":
            return "Italienisch";
        case "ro":
            return "Rumänisch";
        case "pl":
            return "Polnisch";
        case "es":
            return "Spanisch";
        case "ru":
            return "Russisch";
        case "tr":
            return "Türkisch";
        case "pt":
            return "Portugiesisch";
        case "da":
            return "Dänisch";
        case "el":
            return "Griechisch";
        case "fi":
            return "Finnisch";
        case "sv":
            return "Schwedisch";
        case "nl":
            return "Niederländisch";
        default:
            return "Unbekannte Sprache"; // if $lang_code does not match any case, return "Unknown"
    }
}
$GLOBALS["color_scheme"] = "auto";
$GLOBALS["prefers_color_scheme"] = "light";
if ($_COOKIE["color_scheme"]) $GLOBALS["color_scheme"] = $_COOKIE["color_scheme"];
if ($_COOKIE["prefers_color_scheme"]) $GLOBALS["prefers_color_scheme"] = $_COOKIE["prefers_color_scheme"];

if ($GLOBALS["color_scheme"] === "light") $GLOBALS["luminosity"] = "light";
elseif ($GLOBALS["color_scheme"] === "dark" ) $GLOBALS["luminosity"] = "dark";
elseif ($GLOBALS["prefers_color_scheme"] === "dark") $GLOBALS["luminosity"] = "dark";
else $GLOBALS["luminosity"] = "light";

$GLOBALS["luminosity"];
?>

<script type="application/javascript">
    const testDomain = "<?php echo $GLOBALS["testDomain"]; ?>";
    const userLang = "<?php echo $GLOBALS['lang']; ?>";
    const userIP = "<?php echo get_ip(); ?>";
</script>
