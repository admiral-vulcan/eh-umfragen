<?php
require_once ("message_drawer.php");
function verToInt($str) {
    $pos = strpos($str,'.');
    if ($pos !== false) {
        $str = substr($str,0,$pos+1) . str_replace('.','',substr($str,$pos+1));
    }
    return floatval($str);
}
if ($_SERVER['HTTP_HOST'] === "test.eh-umfragen.de") {
    $start = hrtime(true);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    if (isset($_GET["warnings"]) && $_GET["warnings"] == "1") error_reporting(E_ALL);
    else error_reporting(E_ERROR);
}
session_start();
$ver_str = "0.9.5";                      //0.9.5
$ver_float = verToInt($ver_str);     //0.95
$ver_int = intval($ver_float);     //0
$version = $ver_str;                    //legacy reasons, but it's nice to have

require_once ("utf8Encode.php");
require_once ("hdd_handler.php");
require_once ("geturl.php");
require_once ("code.php");
require_once ("sanitize.php");
require_once ("get_ip.php");
require_once ("sendmail.php");
require_once ("database_com.php");
require_once ("session_handler.php");
require_once ("loadsurveys.php");
require_once ("loadresults.php");
if (!isset($surveys)) $surveys = [];
if (!isset($color_scheme)) $color_scheme = "auto";
if ( isset($_GET["survey"]) ) {

    $thisSurveyNumber = -1;
    for ($i = 0; $i < sizeof($surveys); $i++) {
        if (array_search(str_replace("_", " ", $_GET["survey"]), $surveys[$i][0]) === 1) {
            $thisSurveyNumber = $i;
        }
    }
    $title = "EH-Umfragen.de - " . $surveys[$thisSurveyNumber][0][1];
    $description = $surveys[$thisSurveyNumber][0][2];
}
require_once ("head.php");
require_once ("color_scheme_handler.php");
require_once ("graphdrawer.php");
require_once("assets/php/skyandweatherHandler.php");


function secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a Tage und %h Stunden'); // und %i Minuten %s Sekunden
}
if ($_SERVER['HTTP_HOST'] === "test.eh-umfragen.de") {
    $testInfo = alert("Potentiell fehlerhaltige Testversion", "
    Du befindest Dich auf der Test-Domain test.eh-umfragen.de. 
    <br>Hier wird laufend neuer Code ausprobiert, der unter Umständen nicht richtig oder gar nicht funktioniert. 
    <br>Diese Domain wird auch laufend und ohne Vorwarnung aktualisiert. 
    <br>Dabei können Fehler aufkommen, die die Seite unbrauchbar machen. 
    <br>Falls Du unabsichtlich hier angekommen bist, gehe bitte zur produktiven Domain zurück: <a href='https://www.eh-umfragen.de'>www.eh-umfragen.de</a>.
    ", "info", false);
 echo '<div style="position: fixed; top: 4em; width: 100%"><h2 style="text-align: center"><a style="cursor: pointer" onclick="showAlert(' . $testInfo . ')">Potentiell fehlerhaltige Testversion</a></h2></div>';
}
?>
<body class="is-preload" id="top">
<!-- this is the img fullscreen mode -->
<div  class="frosted-container"></div>
<!-- Wrapper -->
<div id="wrapper">
    <!-- Header -->
    <header id="header">
        <nav class="main" style="min-height: 4em">
            <ul>
                <li class="menu">
                    <a class="fa-bars" href="#menu" tabindex="1" aria-label="Menü">Menü</a
                </li>
            </>
        </nav>
        <nav class="main">
            <ul>
                <li>
                    <?php
                    $profilePic = getProfilePic();
                    if (isset($_SESSION['cid']) && $_SESSION['cid'] != "")
                        echo '
                        <picture class="clickableIMG" style="height: 100%;  vertical-align: top;" >
                    <source srcset="' . $profilePic['path'] . '.avif" type="image/avif">
                        <img src="' . $profilePic['path'] . '.' . $profilePic['ext'] . '" alt="' . $profilePic['alt'] . '" class="clickableIMG" style="height: 100%;  vertical-align: top;" onclick="window.location.href=\'/?creator=profile\'">
                        </picture>
                        ';
                    else echo '
                        <picture class="clickableIMG" style="height: 100%;  vertical-align: top;" >
                    <source srcset="' . $profilePic['path'] . '.avif" type="image/avif">
                    <img src="' . $profilePic['path'] . '.' . $profilePic['ext'] . '" alt="' . $profilePic['alt'] . '" class="clickableIMG" style="height: 100%;  vertical-align: top;" onclick="window.location.href=\'/\'">
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
                <label for="color_scheme" class="color_scheme"><div>Design&emsp;&emsp;</div></label>
                <select tabindex="3" aria-label="Designauswahl" name="color_scheme" class="color_scheme_select" id="color_scheme">
                    <option value="1" id="auto" <?php if ($color_scheme === "auto") echo "selected" ?>>Auto</option>
                    <option value="2" id="light" <?php if ($color_scheme === "light") echo "selected" ?>>Hell</option>
                    <option value="3" id="dark" <?php if ($color_scheme === "dark") echo "selected" ?>>Dunkel</option>
                    <option value="4" id="contrast" <?php if ($color_scheme === "contrast") echo "selected" ?>>Kontrast</option>
                </select>
            </div>
        </nav>
        <nav class="main">
            <?php if ($color_scheme !== "contrast") { ?>
                <div id="weather_temperature" class="weather_temperature"><?php echo /*. $GLOBALS['weathertext'] . " bei " .*/ $GLOBALS['temperature'] //$stationName?></div>
                <ul>
                    <li>
                        <form class="not-selectable">&emsp;
                            <input  aria-label="E.H. Wetter anzeigen" type="checkbox" name="weather_checkbox" id="weather_checkbox">
                            <label class="weather_scheme" for="weather_checkbox">EH-Wetter</label>
                        </form>
                    </li>
                </ul>
            <?php } ?>
        </nav>
        <form class="not-selectable">
        </form>
        <nav class="links">
            <ul>
                <?php /*
                for ($i = 0; $i < sizeof($surveys); $i++) {
                    echo "<li><a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . "' rel='nofollow'>" .
                        $surveys[$i][0][1] . "</a></li>";
                }*/
                ?>
            </ul>
        </nav>
    </header>
    <!-- Menu -->
    <section class="not-selectable" id="menu">
        <!-- Links -->
        <section style="padding-left: 1em">
            <h2><picture>
                    <source srcset="images/logo.avif" type="image/avif">
                    <img src="images/logo.png" alt="Ein Klemmbrett als Logo" style='padding-left: 1.5em; padding-top: 1.5em; width: 30%; text-align: right; vertical-align: middle;'>
                </picture>
                &emsp;&emsp;Menü
            </h2>
            <a href="/">
                <h3>Startseite</h3>
                <p style=''>Zurück zur Startseite</p>
            </a>
            <br>
            <h3>Creator-Bereich</h3>
            <?php
            if (!isset($_SESSION['cid'])) {
                if ($_SERVER['HTTP_HOST'] === "test.eh-umfragen.de") {
                    ?>
                    <a href='?creator=challenge'>
                        <p>Login</p>
                    </a>
                    <?php
                }
                else {
                    ?>
                    <a style="cursor: not-allowed;">
                        <p>
                            <abbr style="border-bottom: none !important; cursor: inherit !important; text-decoration: none !important;" title="Der Creator-Bereich ist noch nicht fertig." >
                                Login
                            </abbr>
                        </p>
                    </a>
                    <?php
                }
            }
            else {
                ?>
                <a href='?creator=profile'>
                    <p>Profil</p>
                </a>
                <a href='?creator=creator'>
                    <p>Creator</p>
                </a>
                <a href='?creator=logout'>
                    <p>Logout</p>
                </a>
                <?php
            }
            ?>
            <br><h3>Umfragen</h3>
            <?php
            for ($i = sizeof($surveys) - 1; $i >= 0 ; $i--) {
                $thisid = utf8Encode($surveys[$i][0][0]);
                if (get_hasresults($thisid) == 1) $activestate = "Ergebnisse";
                elseif (get_active($thisid) == 0) $activestate = "geschlossen";
                else $activestate = "offen";
                echo "<a href='?survey=" . str_replace(" ", "_", $surveys[$i][0][1]) . "'>
                    <p>" . "#" . $thisid . " ". $surveys[$i][0][1] . "</p></a>&emsp;➥". $surveys[$i][0][2] . "<br><br><br>";
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
        require ("about.php")
        ?>
        <!-- Footer -->
        <div style="white-space: normal;">
            <section id="footer">
                <ul class="icons">
                <!--
                    <li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
                    <li><a href="https://github.com/admiral-vulcan/eh-umfragen/" class="icon brands fa-github" target="_blank" rel="nofollow"><span class="label">Github</span></a></li>
                    <li><a href="mailto:kontakt@eh-umfragen.de" class="icon solid fa-envelope"><span class="label">Email</span></a></li>
                    -->
                </ul>
                <p class="copyright">EH-Umfragen.de v. <?php echo $version; ?> &copy; Felix Rau, Miriam Brieger, Lena Weigelt 2023<br><br><a href="mailto:kontakt@eh-umfragen.de">Kontakt</a> &ensp; &ensp; <a href="?content=impressum" target="_blank">Impressum</a> &ensp; &ensp; <a href="?content=agb" target="_blank">AGB</a> &ensp; &ensp; <a href="?content=cookies" target="_blank">Cookies</a> &ensp; &ensp; <a href="?content=datenschutz" target="_blank">Datenschutz</a> &ensp; &ensp; <a href="?content=secureinfo" target="_blank">Übertragung</a> &ensp; &ensp; <a href="?content=passwordinfo" target="_blank">Passwortspeicherung</a> &ensp; &ensp; <a href="?content=mailinfo" target="_blank">Mailnutzung</a><br><br><br>
                    Quellen:
                    <br><br><a href="https://html5up.net" target="_blank" rel="nofollow">Future Imperfect by HTML5 UP</a>,
                    <br><br><a href="https://pixabay.com/vectors/survey-icon-survey-icon-2316468/" target="_blank" rel="nofollow">Survey Icon</a>,
                    <br><br><a href="https://unsplash.com" target="_blank" rel="nofollow">Unsplash</a>,
                    <br><br><a href="https://fontawesome.com" target="_blank" rel="nofollow">Font Awesome</a>,
                    <br><br><a href="https://jquery.com" target="_blank" rel="nofollow">jQuery</a>,
                    <br><br><a href="https://github.com/ajlkn/responsive-tools" target="_blank" rel="nofollow">Responsive Tools</a>,
                    <br><br><a href="https://colorbrewer2.org/#type=qualitative&scheme=Set3&n=12" target="_blank" rel="nofollow">COLORBREWER</a>,
                    <br><br><a href="https://github.com/WebDevSHORTS/Parallax-Star-Background" target="_blank" rel="nofollow">Parallax Star background</a>,
                    <br><br><a href="https://www.schattenbaum.net/php/kreisdiagramm_mit_gd-lib.php" target="_blank" rel="nofollow">Kreisdiagramm</a>,
                    <br><br><a href="http://www.ulrichmierendorff.com/software/antialiased_arcs.html" target="_blank" rel="nofollow">Antialiased Filled Arcs</a>,
                    <br><br><a href="https://datenschutz-generator.de/" target="_blank" rel="nofollow">Datenschutz-Generator.de von Dr. Thomas Schwenke</a>,
                    <br><br><br>Lizenziert unter der Apache Lizenz, Version 2.0
                    <br><br>Licensed under the Apache License, version 2.0
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
                </p>
            </section>
            <!-- this was sidebar for some reason o_O
            </section>
            -->
        </div>

    </div>
</div>
<!-- Scripts
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/browser.min.js"></script>
<script src="assets/js/breakpoints.min.js"></script>
<script src="assets/js/util.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/futureimperfact.js"></script>
-->
<script src="assets/js/suncalc.js"></script>
<script src="assets/js/all.min.js"></script>
<script src="assets/js/weather_handler.js"></script>
<script src="assets/js/replaceAvifHandler.js"></script>
</body>
<?php
if ($_SERVER['HTTP_HOST'] === "test.eh-umfragen.de") {
    /*
    echo "<br> " . $GLOBALS['weathertext'] . " bei ";
    echo $GLOBALS['temperature'] . "<br>";
    */
    $end = hrtime(true);
    $elapsed = intval(($end - $start)/1000000);
    echo "<br> Serverseitige Seitenladezeit: $elapsed ms.<br><br><br>";
}
?>
</html>