<?php

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require_once ("assets/php/imageSmoothArc.php");
require_once ("assets/php/imageFilledSmoothArc.php");

$GLOBALS["colorsvibrant"] = array(
    array(140,81,10),
    array(216,179,101),
    array(246,232,195),
    array(199,234,229),
    array(90,180,172),
    array(1,102,94),

    array(215,48,39),
    array(252,141,89),
    array(254,224,144),
    array(224,243,248),
    array(145,191,219),
    array(69,117,180),

    /* colors above are color-blind approved */
    array(31,120,180),
    array(178,223,138),
    array(51,160,44),
    array(251,154,153),
    array(227,26,28),
    array(253,191,111),
    array(255,127,0),
    array(202,178,214),
    array(106,61,154),
    array(255,255,153),
    array(177,89,40),

    array(255,255,179),
    array(190,186,218),
    array(251,128,114),
    array(128,177,211),
    array(253,180,98),
    array(179,222,105),
    array(252,205,229),
    array(217,217,217),
    array(188,128,189),
    array(204,235,197),
    array(255,237,111),

    "scheme" => "dark"
);

$GLOBALS["colorspastel"] = array(
    array(215,48,39),
    array(252,141,89),
    array(254,224,144),
    array(224,243,248),
    array(145,191,219),
    array(69,117,180),
    /* colors above are color-blind approved */

    array(140,81,10),
    array(216,179,101),
    array(246,232,195),
    array(199,234,229),
    array(90,180,172),
    array(1,102,94),

    array(255,255,179),
    array(190,186,218),
    array(251,128,114),
    array(128,177,211),
    array(253,180,98),
    array(179,222,105),
    array(252,205,229),
    array(217,217,217),
    array(188,128,189),
    array(204,235,197),
    array(255,237,111),

    array(31,120,180),
    array(178,223,138),
    array(51,160,44),
    array(251,154,153),
    array(227,26,28),
    array(253,191,111),
    array(255,127,0),
    array(202,178,214),
    array(106,61,154),
    array(255,255,153),
    array(177,89,40),

    "scheme" => "light"
);


function setColors($mode) {
    if ($mode === "dark") {
        $GLOBALS["backgroundColorRed"] = 47;
        $GLOBALS["backgroundColorGreen"] = 20;
        $GLOBALS["backgroundColorBlue"] = 27;

        $GLOBALS["textColorRed"] = 201;
        $GLOBALS["textColorGreen"] = 198;
        $GLOBALS["textColorBlue"] = 198;

        $GLOBALS["colors"] = $GLOBALS["colorsvibrant"];
    } else {
        $GLOBALS["backgroundColorRed"] = 238;
        $GLOBALS["backgroundColorGreen"] = 212;
        $GLOBALS["backgroundColorBlue"] = 200;

        $GLOBALS["textColorRed"] = 60;
        $GLOBALS["textColorGreen"] = 59;
        $GLOBALS["textColorBlue"] = 59;

        $GLOBALS["colors"] = $GLOBALS["colorspastel"];
    }
}
setColors($GLOBALS["luminosity"]);
function drawCircleLight($werte): bool|string {
    setColors("light");
    return drawCircle($werte);
}
function drawRectangleLight($werte): bool|string {
    setColors("light");
    return drawRectangle($werte);
}
function drawLegendLight($werte): bool|string {
    setColors("light");
    return drawLegend($werte);
}

function drawCircleDark($werte): bool|string {
    setColors("dark");
    return drawCircle($werte);
}
function drawRectangleDark($werte): bool|string {
    setColors("dark");
    return drawRectangle($werte);
}
function drawLegendDark($werte): bool|string {
    setColors("dark");
    return drawLegend($werte);
}

function drawCircle($werte): bool|string {

    $anzahl = sizeof($werte);
    if ($anzahl == 0 || $anzahl > 34 ) return -1;
    $breite = 300;
    $hoehe = 300;
    $radius = 150;
    $start_x = $breite / 2;
    $start_y = $hoehe / 2;
    $allvalues = "";

    foreach ($werte as $key => $value) $allvalues .= $value;
    $allvalues .= $GLOBALS["backgroundColorRed"] . $GLOBALS["backgroundColorGreen"] . $GLOBALS["backgroundColorBlue"];
    $allvalues .= "circle";
    $valuesHash = hash('sha256', $allvalues);
    if (apcu_exists($valuesHash)) {
        return apcu_fetch($valuesHash);
    }

    $diagramm = imagecreatetruecolor($breite, $hoehe);
    $hintergrund = imagecolorallocate($diagramm, $GLOBALS["backgroundColorRed"], $GLOBALS["backgroundColorGreen"], $GLOBALS["backgroundColorBlue"]);

    imagecolortransparent($diagramm, $hintergrund);
    imagefill($diagramm, 0, 0, $hintergrund);

    $i = 0;
    $j = 0;
    $winkel = -90;
    //arsort($werte);
    $gesamt = array_sum($werte);
    if ($gesamt == 0) return -1;
    foreach ($werte as $key => $value) {
        $i++;
        $start = $winkel;

        if ($value != 0) {
            $j++;
            $winkel = $start + $value * 360 / $gesamt;      //Uncaught DivisionByZeroError
            $color = imagecolorallocate($diagramm, $GLOBALS["colors"][$i-1][0], $GLOBALS["colors"][$i-1][1], $GLOBALS["colors"][$i-1][2]);
            if ($j == 1 && $winkel == 360-90) imageFilledSmoothArc($diagramm, $start_x, $start_y, $radius*2-10, $radius*2-10, 0, 359, $color, IMG_ARC_PIE);
            else imageFilledSmoothArc($diagramm, $start_x, $start_y, $radius*2-10, $radius*2-10, $start, $winkel, $color, IMG_ARC_PIE);
        }
    }
    ob_start();
    imagepng($diagramm);
    $imgData = ob_get_clean();
    imagedestroy($diagramm);

    $imgBase64 = base64_encode($imgData);
    apcu_store($valuesHash, $imgBase64);

    return base64_encode($imgData);
}

function drawRectangle($werte): bool|string {
    $anzahl = sizeof($werte);
    if ($anzahl == 0 || $anzahl > 34 ) return -1;
    $breite = 300;
    $hoehe = 300;
    $rand_oben = 20;
    $rand_links = 20;
    $punktbreite = 10;
    $abstand = 10;
    $schriftgroesse = 10;
    $allvalues = "";

    foreach ($werte as $key => $value) $allvalues .= $value;
    $allvalues .= $GLOBALS["backgroundColorRed"] . $GLOBALS["backgroundColorGreen"] . $GLOBALS["backgroundColorBlue"];
    $allvalues .= "rectangle";
    $valuesHash = hash('sha256', $allvalues);
    if (apcu_exists($valuesHash)) {
        return apcu_fetch($valuesHash);
    }

    $diagramm = imagecreatetruecolor($breite, $hoehe);
    $hintergrund = imagecolorallocate($diagramm, $GLOBALS["backgroundColorRed"], $GLOBALS["backgroundColorGreen"], $GLOBALS["backgroundColorBlue"]);
    $schriftfarbe = imagecolorallocate($diagramm, $GLOBALS["textColorRed"], $GLOBALS["textColorGreen"], $GLOBALS["textColorBlue"]);

    imagecolortransparent($diagramm, $hintergrund);
    imagefill($diagramm, 0, 0, $hintergrund);

    $i = 0;
    //arsort($werte);
    $gesamt = array_sum($werte);
    if ($gesamt == 0) return -1;
    $maxvalue = max($werte);
    $maxpercent = round($maxvalue * 100 / $gesamt);
    $multiplikator = $hoehe / $maxvalue;
    $versatz = 0;
    $emptyvals = count(array_keys($werte, 0));
    $anzahl -= $emptyvals;
    $start_x = $breite / $anzahl;
    foreach ($werte as $key => $value) {
        $i++;
        if ($value === 0) {
            $versatz++;
        }
        else {
            $color = imagecolorallocate($diagramm, $GLOBALS["colors"][$i-1][0], $GLOBALS["colors"][$i-1][1], $GLOBALS["colors"][$i-1][2]);
            imagefilledrectangle($diagramm,($start_x*($i-1-$versatz)),$hoehe,($start_x*($i-$versatz)-(16-$anzahl)/2),round($value * $multiplikator) * -1 + $hoehe,$color);
        }
    }
    ob_start();
    imagepng($diagramm);
    $imgData = ob_get_clean();
    imagedestroy($diagramm);

    $imgBase64 = base64_encode($imgData);
    apcu_store($valuesHash, $imgBase64);

    return base64_encode($imgData);
}

function drawLegend($werte) {

    $allKeys = "";

    $anzahl = sizeof($werte);
    if ($anzahl == 0 || $anzahl > 34 ) return -1;
    $breite = 200;
    $hoehe = (40 * ($anzahl/2)) + 30;
    $start_x = $breite / $anzahl;
    $rand_oben = 20;
    $rand_links = 20;
    $punktbreite = 10;
    $abstand = 10;
    $schriftgroesse = 10;
    $wordwrapper = 0;
    foreach ($werte as $key => $value) {
        $key = wordwrap($key, 20, "\n");
        $wordwrapper += substr_count( $key, "\n" );
        $allKeys .= $key;
        $allKeys .= $value;
    }

    $allKeys .= $GLOBALS["backgroundColorRed"] . $GLOBALS["backgroundColorGreen"] . $GLOBALS["backgroundColorBlue"];
    $allKeys .= "legend";
    $keysHash = hash('sha256', $GLOBALS["lang"] . $allKeys);
    if (apcu_exists($keysHash)) {
        //apcu_delete($keysHash);
        return apcu_fetch($keysHash);
    }

    $hoehe += ($wordwrapper * 9);
    $diagramm = imagecreatetruecolor($breite, $hoehe);
    $hintergrund = imagecolorallocate($diagramm, $GLOBALS["backgroundColorRed"], $GLOBALS["backgroundColorGreen"], $GLOBALS["backgroundColorBlue"]);
    $schriftfarbe = imagecolorallocate($diagramm, $GLOBALS["textColorRed"], $GLOBALS["textColorGreen"], $GLOBALS["textColorBlue"]);

    imagecolortransparent($diagramm, $hintergrund);
    imagefill($diagramm, 0, 0, $hintergrund);

    $i = 0;
    //arsort($werte);
    $gesamt = array_sum($werte);
    if ($gesamt == 0) return -1;
    $maxvalue = max($werte);
    $wordwrapper = 0;
    foreach ($werte as $key => $value) {
        $i++;
        if ($value != 0) {
            $color = imagecolorallocate($diagramm, $GLOBALS["colors"][$i-1][0], $GLOBALS["colors"][$i-1][1], $GLOBALS["colors"][$i-1][2]);
        }
        else $color = imagecolortransparent($diagramm, $hintergrund);
        $key = wordwrap($key, 20, "\n");
        $key = translate($key, "de", $GLOBALS["lang"]);
        $wordwrapper += substr_count( $key, "\n" );

        //echo $wordwrapper . "<br>";
        $unterkante = $rand_oben + $punktbreite + ($i - 1) * ($punktbreite + $abstand);
        imagefilledrectangle($diagramm, $rand_links, $rand_oben + ($i - 1) * ($punktbreite + $abstand), $rand_links + $punktbreite, $unterkante, $color);
        imagettftext($diagramm, $schriftgroesse, 0, $rand_links + $punktbreite + 5, $unterkante - $punktbreite / 2 + $schriftgroesse / 2, $schriftfarbe, "./assets/x86fonts/Raleway-Medium.ttf", $key . " " . round($value * 100 / $gesamt, 0) . " %");
        $rand_oben += ($wordwrapper * 9);
    }
    ob_start();
    imagepng($diagramm);
    $imgData = ob_get_clean();
    imagedestroy($diagramm);

    $imgBase64 = base64_encode($imgData);
    apcu_store($keysHash, $imgBase64);

    return $imgBase64;
}


function getAlt($werte) {
    $alt = "";
    $anzahl = sizeof($werte);
    if ($anzahl == 0 || $anzahl > 34) return -1;

    foreach ($werte as $key => $value) {
        $key = wordwrap($key, 20, "\n");
        $key = translate($key, "de", $GLOBALS["lang"]);
        $wordwrapper += substr_count($key, "\n");
        $alt .= $key . ": ";
        $alt .= $value . "%; ";
    }
    return substr($alt, 0, -2);
}