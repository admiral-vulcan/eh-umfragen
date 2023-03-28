<?php
$start = 0;
require_once ("assets/php/getVars.php");
session_start();
$ver_str = "0.9.7";                      //0.9.5
$ver_float = verToInt($ver_str);     //0.95
$ver_int = intval($ver_float);     //0
$version = $ver_str;                    //legacy reasons, but it's nice to have

require_once ("utf8Encode.php");
require_once ("message_drawer.php");
require_once ("translate.php");
require_once ("head.php");
require_once ("assets/php/deconstruct_data.php");
require_once ("hdd_handler.php");
require_once ("zitate.php");
require_once ("gitignore/code.php");
require_once ("sanitize.php");
require_once ("sendmail.php");
require_once("gitignore/dbcred.php");
require_once("assets/php/classes/DatabaseHandler.php");
require_once ("database_com.php");
require_once ("session_handler.php");
require_once ("loadsurveys.php");
require_once ("loadresults.php");
if (isset($_GET["draft"]) && $_GET["draft"] == "1") $draft = "&draft=1";
else  $draft = "";
if (!isset($surveys)) $surveys = [];
if ( isset($_GET["survey"]) ) {

    $thisSurveyNumber = -1;
    for ($i = 0; $i < sizeof($surveys); $i++) {
        if (array_search(str_replace("_", " ", $_GET["survey"]), $surveys[$i][0]) === 1) {
            $thisSurveyNumber = $i;
        }
    }
    $title = "eh-umfragen.de - " . translate($surveys[$thisSurveyNumber][0][1], "de", $GLOBALS["lang"]);
    $description1 = $surveys[$thisSurveyNumber][0][2];
    $description1 = translate($surveys[$thisSurveyNumber][0][2], "de", $GLOBALS["lang"]);
}
require_once ("graphdrawer.php");
require_once("assets/php/skyandweatherHandler.php");

function secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a Tage und %h Stunden'); // und %i Minuten %s Sekunden
}
if ($GLOBALS["testDomain"]) {
    $testInfo = alert("Potentiell fehlerhaltige Testversion", "
    Du befindest Dich auf der Test-Domain test.eh-umfragen.de. 
    <br>Hier wird laufend neuer Code ausprobiert, der unter Umständen nicht richtig oder gar nicht funktioniert. 
    <br>Diese Domain wird auch laufend und ohne Vorwarnung aktualisiert. 
    <br>Dabei können Fehler aufkommen, die die Seite unbrauchbar machen. 
    <br>Falls Du unabsichtlich hier angekommen bist, gehe bitte zur produktiven Domain zurück: <a href='https://www.eh-umfragen.de'>www.eh-umfragen.de</a>.
    ", "info", false);
    echo '<div style="position: fixed; bottom: 0; width: 100%; z-index: 20000;"><h2 style="text-align: center;"><a style="cursor: pointer;" onclick="showAlert(' . $testInfo . ')">' .
 translate("Potentiell fehlerhaltige Testversion", 'de', $GLOBALS['lang'])
 . '</a></h2></div>';
}
?>
<body class="is-preload" id="top">
<!-- this is the img fullscreen mode -->
<div  class="frosted-container"></div>
<!-- Wrapper -->
<div id="wrapper">
    <!-- Header -->
    <header id="topBar">
        <nav class="main" style="min-height: 4em;">
            <ul>
                <li class="menu">
                    <a class="fa-bars" id="mainmenu" href="#menu" tabindex="2" aria-label="<?php echo translate("Menü", "de", $GLOBALS["lang"]); ?>"><?php echo translate("Menü", "de", $GLOBALS["lang"]); ?></a
                </li>
            </ul>
        </nav>
        <nav class="main">
            <ul>
                <li>
                    <?php
                    $profilePic = getProfilePic();
                    if (isset($_SESSION['creator_id']) && $_SESSION['creator_id'] != "")
                        echo '
                        <picture class="clickableIMG" style="height: 98%;  vertical-align: top;" >
                    <source srcset="' . $profilePic['path'] . '.avif" type="image/avif">
                        <img src="' . $profilePic['path'] . '.' . $profilePic['ext'] . '" alt="' . translate($profilePic['alt'], 'de', $GLOBALS['lang']) . '" class="clickableIMG" style="height: 98%;  vertical-align: top;" onclick="window.location.href=\'/?creator=profile\'">
                        </picture>
                        ';
                    else echo '
                        <picture class="clickableIMG" style="height: 98%;  vertical-align: top;" >
                    <source srcset="' . $profilePic['path'] . '.avif" type="image/avif">
                    <img src="' . $profilePic['path'] . '.' . $profilePic['ext'] . '" alt="' . translate($profilePic['alt'], 'de', $GLOBALS['lang']) . '" class="clickableIMG" style="height: 98%;  vertical-align: top;" onclick="window.location.href=\'/\'">
                        </picture>
                    ';
                    ?>
                </li>
            </ul>
        </nav>
        <nav class="main">
            <ul>
                <li>
                </li>
            </ul>
        </nav>
        <nav class="main">
            <div class="color_scheme_container">
                <label for="color_scheme" class="color_scheme"><?php echo translate("Design", "de", $GLOBALS["lang"]); ?><!-- &emsp;&emsp; --></label>
                <select tabindex="3" aria-label="<?php echo translate("Designauswahl", "de", $GLOBALS["lang"]); ?>" name="color_scheme" class="color_scheme_select" id="color_scheme">
                    <option value="auto" id="auto" <?php if ($GLOBALS["color_scheme"] === "auto") echo "selected"; ?>>Auto</option>
                    <option value="light" id="light" <?php if ($GLOBALS["color_scheme"] === "light") echo "selected"; ?>><?php echo translate("Hell", "de", $GLOBALS["lang"]); ?></option>
                    <option value="dark" id="dark" <?php if ($GLOBALS["color_scheme"] === "dark") echo "selected"; ?>><?php echo translate("Dunkel", "de", $GLOBALS["lang"]); ?></option>
                    <option value="contrast" id="contrast" <?php if ($GLOBALS["color_scheme"] === "contrast") echo "selected"; ?>><?php echo translate("Hochkontrast", "de", $GLOBALS["lang"]); ?></option>
                </select>
            </div>
        </nav>
        <nav class="main">
            <ul>
                <li>
                </li>
            </ul>
        </nav>
        <div id="weather-button">
            <div id="weather_temperature" class="weather_temperature">
                <?php echo $GLOBALS['temperature'] //$stationName?>
            </div>
            <form class="not-selectable">
                <input  aria-label="<?php echo translate("E.H. Wetter anzeigen", "de", $GLOBALS["lang"]); ?>" type="checkbox" name="weather_checkbox" id="weather_checkbox">
                <label class="weather_scheme" for="weather_checkbox">
                    <?php echo translate("EH-Wetter", "de", $GLOBALS["lang"]); ?>
                </label>
            </form>
        </div>
        <form class="not-selectable">
        </form>
        <nav class="links">
            <ul>
                <li>
                </li>
            </ul>
        </nav>
    </header>
    <!-- Menu -->
    <section class="not-selectable" id="menu">
        <!-- Links -->
        <section style="padding-left: 1em;">
            <h2><picture>
                    <source srcset="images/logo.avif" type="image/avif">
                    <img src="images/logo.png" alt="<?php echo translate($profilePic['alt'], 'de', $GLOBALS['lang']); ?>" style='padding-left: 1.5em; padding-top: 1.5em; width: 30%; text-align: right; vertical-align: middle;'>
                </picture>
                <?php //echo translate("Menü", "de", $GLOBALS["lang"]); ?>
            </h2>
            <div class="language_container">
                <div class="language_select_container">
                    <label for="language_select" class="language_label"><div class="language_label_div"><?php echo translate("Sprache", "de", $GLOBALS["lang"]); ?></div></label> <!--  -->
                    <select tabindex="3" aria-label="<?php echo translate("Sprachauswahl", "de", $GLOBALS["lang"]); ?>" name="language_select" class="language_select">
                        <option class="language" value="lang_auto" <?php if ($GLOBALS["lang"] === "") echo "selected"; ?> >Auto</option>
                        <option class="language" value="de" <?php if ($GLOBALS["lang"] === "de") echo "selected"; ?>><?php echo translate("Deutsch", "de", "de"); ?></option>
                        <option class="language" value="en" <?php if ($GLOBALS["lang"] === "en") echo "selected"; ?>><?php echo translate("Englisch", "de", "en"); ?></option>
                        <option class="language" value="fr" <?php if ($GLOBALS["lang"] === "fr") echo "selected"; ?>><?php echo translate("Französisch", "de", "fr"); ?></option>
                        <option class="language" value="it" <?php if ($GLOBALS["lang"] === "it") echo "selected"; ?>><?php echo translate("Italienisch", "de", "it"); ?></option>
                        <option class="language" value="ro" <?php if ($GLOBALS["lang"] === "ro") echo "selected"; ?>><?php echo translate("Rumänisch", "de", "ro"); ?></option>
                        <option class="language" value="pl" <?php if ($GLOBALS["lang"] === "pl") echo "selected"; ?>><?php echo translate("Polnisch", "de", "pl"); ?></option>
                        <option class="language" value="es" <?php if ($GLOBALS["lang"] === "es") echo "selected"; ?>><?php echo translate("Spanisch", "de", "es"); ?></option>
                        <option class="language" value="ru" <?php if ($GLOBALS["lang"] === "ru") echo "selected"; ?>><?php echo translate("Russisch", "de", "ru"); ?></option>
                        <option class="language" value="tr" <?php if ($GLOBALS["lang"] === "tr") echo "selected"; ?>><?php echo translate("Türkisch", "de", "tr"); ?></option>
                        <!--
                        <option class="language" value="pt" <?php if ($GLOBALS["lang"] === "pt") echo "selected"; ?>><?php echo translate("Portugiesisch", "de", "pt"); ?></option>
                        <option class="language" value="da" <?php if ($GLOBALS["lang"] === "da") echo "selected"; ?>><?php echo translate("Dänisch", "de", "da"); ?></option>
                        <option class="language" value="el" <?php if ($GLOBALS["lang"] === "el") echo "selected"; ?>><?php echo translate("Griechisch", "de", "el"); ?></option>
                        <option class="language" value="fi" <?php if ($GLOBALS["lang"] === "fi") echo "selected"; ?>><?php echo translate("Finnisch", "de", "fi"); ?></option>
                        <option class="language" value="sv" <?php if ($GLOBALS["lang"] === "sv") echo "selected"; ?>><?php echo translate("Schwedisch", "de", "sv"); ?></option>
                        <option class="language" value="nl" <?php if ($GLOBALS["lang"] === "nl") echo "selected"; ?>><?php echo translate("Niederländisch", "de", "nl"); ?></option>
                        -->
                    </select>
                </div>
            </div>
                <h3><?php echo translate("Startseite", "de", $GLOBALS["lang"]); ?></h3>
                <a href="/">
                <p style=''><?php echo translate("Zurück zur Startseite", "de", $GLOBALS["lang"]); ?></p>
            </a>
            <br>
            <h3><?php echo translate("Creator-Bereich", "de", $GLOBALS["lang"]); ?></h3>
            <?php
            if (!isset($_SESSION['creator_id'])) {
                if ($GLOBALS["testDomain"]) {
                    ?>
                    <a href='?creator=challenge'>
                        <p><?php echo translate("Login", "de", $GLOBALS["lang"]); ?></p>
                    </a>
                    <?php
                }
                else {
                    ?>
                    <a style="cursor: not-allowed;">
                        <p>
                            <abbr style="border-bottom: none !important; cursor: inherit !important; text-decoration: none !important;" title="<?php echo translate("Der Creator-Bereich ist noch nicht fertig.", "de", $GLOBALS["lang"]); ?>" >
                                <?php echo translate("Login", "de", $GLOBALS["lang"]); ?>
                            </abbr>
                        </p>
                    </a>
                    <?php
                }
            }
            else {
                ?>
                <a href='?creator=profile'>
                    <p><?php echo translate("Profil", "de", $GLOBALS["lang"]); ?></p>
                </a>
                <a href='?creator=creator'>
                    <p><?php echo translate("Creator", "de", $GLOBALS["lang"]); ?></p>
                </a>
                <a href='?creator=logout'>
                    <p><?php echo translate("Logout", "de", $GLOBALS["lang"]); ?></p>
                </a>
                <?php
            }
            ?>
            <br><h3><?php echo translate("Umfragen", "de", $GLOBALS["lang"]); ?></h3>
            <?php
            for ($i = sizeof($surveys) - 1; $i >= 0 ; $i--) {
                $thisid = utf8Encode($surveys[$i][0][0]);
                if (get_hasresults($thisid) == 1) $activestate = "Ergebnisse";
                elseif (get_active($thisid) == 0) $activestate = "geschlossen";
                else $activestate = "offen";
                echo "<a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . $draft . "'>
                    <p>" . "#" . $thisid . " ". translate($surveys[$i][0][1], "de", $GLOBALS["lang"]) . "</p></a>&emsp;➥". translate($surveys[$i][0][2], "de", $GLOBALS["lang"]) . "<br><br><br>";
            }
            ?>
        </section>
    </section>

    <!-- Main    -->
    <div id="main">
        <?php
        if ( !isset($_GET["survey"]) && !isset($_GET["content"]) && !isset($_GET["creator"]) ) include ("greeting.php");
        elseif (isset($_POST["content"]) && $_POST["content"] === "sendsurvey") include ("sendsurvey.php");
        elseif (isset($_GET["content"])) include ($_GET["content"].".php");
        elseif (isset($_GET["creator"])) include ("login.php");
        elseif (isset($_GET["survey"])) include "survey.php";
        ?>

        <!-- About -->
        <?php
        if (isset($_GET["logout"])) session_destroy();
        require ("about.php");
        if (strtolower($GLOBALS["lang"]) != "de") {
            echo "<br><p>" . translate("Diese Seite wurde live von <a href='https://www.deepl.com/' target='_blank' rel='nofollow'>deepL</a> übersetzt.", "de", $GLOBALS["lang"]) . "</p><br>";
        }
        ?>
        <!-- Footer -->
        <div style="white-space: normal;">
            <section id="footer">
                <ul class="icons">
                    <!--
                        <li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
                        -->
                    <li><a href="https://github.com/admiral-vulcan/eh-umfragen/" class="icon brands fa-github" target="_blank" rel="nofollow"><span class="label">Github</span></a></li>
                    <li><a href="mailto:kontakt@eh-umfragen.de" class="icon solid fa-envelope"><span class="label">E-Mail</span></a></li>
                </ul>
                <div class="copyright">
                <p>eh-umfragen.de v. <?php echo $version; ?> &copy; Felix Rau, Miriam Brieger, Lena Weigelt 2023<br><br><a href="mailto:kontakt@eh-umfragen.de"><?php echo translate("Kontakt", "de", $GLOBALS["lang"]); ?></a> &ensp; &ensp; <a href="?content=impressum" target="_blank"><?php echo translate("Impressum", "de", $GLOBALS["lang"]); ?></a> &ensp; &ensp; <a href="?content=agb" target="_blank"><?php echo translate("AGB", "de", $GLOBALS["lang"]); ?></a> &ensp; &ensp; <a href="?content=cookies" target="_blank"><?php echo translate("Cookies", "en", $GLOBALS["lang"]); ?></a> &ensp; &ensp; <a href="?content=datenschutz" target="_blank"><?php echo translate("Datenschutz", "de", $GLOBALS["lang"]); ?></a> &ensp; &ensp; <a href="?content=lizenz" target="_blank"><?php echo translate("Lizenz", "de", $GLOBALS["lang"]); ?></a> &ensp; &ensp; <a href="?content=secureinfo" target="_blank"><?php echo translate("Übertragung", "de", $GLOBALS["lang"]); ?></a> &ensp; &ensp; <a href="?content=passwordinfo" target="_blank"><?php echo translate("Passwortspeicherung", "de", $GLOBALS["lang"]); ?></a> &ensp; &ensp; <a href="?content=mailinfo" target="_blank"><?php echo translate("Mailnutzung", "de", $GLOBALS["lang"]); ?></a><br><br><br>
                    <?php echo translate('Quellen:', 'de', $GLOBALS['lang']); ?>
                    <br><a href="https://html5up.net" target="_blank" rel="nofollow">Future Imperfect by HTML5 UP</a>,
                    <br><a href="https://pixabay.com/vectors/survey-icon-survey-icon-2316468/" target="_blank" rel="nofollow">Survey Icon</a>,
                    <br><a href="https://www.deepl.com/" target="_blank" rel="nofollow">DeepL</a>,
                    <br><a href="https://unsplash.com" target="_blank" rel="nofollow">Unsplash</a>,
                    <br><a href="https://fontawesome.com" target="_blank" rel="nofollow">Font Awesome</a>,
                    <br><a href="https://jquery.com" target="_blank" rel="nofollow">jQuery</a>,
                    <br><a href="https://github.com/ajlkn/responsive-tools" target="_blank" rel="nofollow">Responsive Tools</a>,
                    <br><a href="https://colorbrewer2.org/#type=qualitative&scheme=Set3&n=12" target="_blank" rel="nofollow">COLORBREWER</a>,
                    <br><a href="https://github.com/WebDevSHORTS/Parallax-Star-Background" target="_blank" rel="nofollow">Parallax Star background</a>,
                    <br><a href="https://www.schattenbaum.net/php/kreisdiagramm_mit_gd-lib.php" target="_blank" rel="nofollow">Kreisdiagramm</a>,
                    <br><a href="http://www.ulrichmierendorff.com/software/antialiased_arcs.html" target="_blank" rel="nofollow">Antialiased Filled Arcs</a>,
                    <br><a href="https://datenschutz-generator.de/" target="_blank" rel="nofollow">Datenschutz-Generator.de von Dr. Thomas Schwenke</a>,
                    <br><br><a href="/LICENSE.md" target="_blank"><?php echo translate('Apache Lizenz (Textfassung, Englisch), Version 2.0', 'de', $GLOBALS['lang']); ?></a>
                    <?php
                    if ( isset($_GET["content"]) && (
                        $_GET["content"] === "impressum" or
                        $_GET["content"] === "agb" or
                        $_GET["content"] === "cookies" or
                        $_GET["content"] === "datenschutz" or
                        $_GET["content"] === "secureinfo" or
                        $_GET["content"] === "passwordinfo" or
                        $_GET["content"] === "mailinfo"
                        )
                    ) echo "<br><br><br><br><br><br>";
                    ?>
                </p></div>
            </section>
        </div>
    </div>
</div>
<!-- Scripts
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/browser.min.js"></script>
<script src="assets/js/breakpoints.min.js"></script>
<script src="assets/js/util.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/src/futureimperfact.js"></script>
-->
<script src="assets/js/suncalc.js"></script>
<script src="assets/js/all.min.js"></script>
<script src="assets/js/weather_handler.js"></script>
<script src="assets/js/replaceAvifHandler.js"></script>
<script src="assets/js/creator.js"></script>
</body>
<?php
require_once ("color_scheme_handler.php");
if ($GLOBALS["testDomain"]) {
    /*
    echo "<br> " . $GLOBALS['weathertext'] . " bei ";
    echo $GLOBALS['temperature'] . "<br>";
    */
    $end = hrtime(true);
    $elapsed = intval(($end - $start)/1000000);
    echo "<br> Serverseitige Seitenladezeit: $elapsed ms.<br><br><br><br><br><br>";
}
if (isset($_GET["tabclose"]) && $_GET["tabclose"] == "1") echo "<br><br><br><br><br><br>";
    echo "</html>";
?>