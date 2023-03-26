<?php

function getWeatherIdByName($name) {
    // Use standardized form chars.
    $name = replaceGermanSpecialChars($name);
    $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    // Open the file for reading
    $file = fopen(__DIR__ . '/../../assets/txt/wetterstationen.txt', 'r');
    // Read each line of the file
    while (($line = fgets($file)) !== false) {
        // Check if the name appears in the line (case-insensitive)
        if (stripos($line, $name) !== false) {
            // Check if the name is preceded by a space character
            if (substr($line, stripos($line, $name) - 1, 1) == " ") {
                // Get the substring starting 11 characters before the name and 5 characters long
                $id = substr($line, stripos($line, $name) - 11, 5);
                // Check if the ID is alphanumeric and 4 or 5 characters long
                if (ctype_alnum($id) && (strlen($id) == 4 || strlen($id) == 5)) {
                    // If the ID is 4 characters long, strip the ending space character
                    if (strlen($id) == 4) {
                        $id = rtrim($id);
                    }
                    // Return the ID
                    return $id;
                }
            }
        }
    }
    // If the name was not found or the ID is not in the correct format, return null
    return null;
}



//echo "<br>" . getWeatherIdByName("ludwigsburg") . "<br>"; Lubu is K2714


function replaceGermanSpecialChars($string) {
    $string = str_replace('ö', 'oe', $string);
    $string = str_replace('Ö', 'Oe', $string);
    $string = str_replace('ä', 'ae', $string);
    $string = str_replace('Ä', 'Ae', $string);
    $string = str_replace('ü', 'ue', $string);
    $string = str_replace('Ü', 'Ue', $string);
    $string = str_replace('ß', 'ss', $string);
    return $string;
}

function getWeatherData($stationId) {
    // Set the path to the weather data file
    $filePath = __DIR__ . '/../../weatherData/' . $stationId . '.json';
    // Set the URL for the weather data API
    $apiUrl = 'https://dwd.api.proxy.bund.dev/v30/stationOverviewExtended?stationIds=' . $stationId;

    // Check if the weather data file exists and is less than an hour old
    if (file_exists($filePath) && (time() - filemtime($filePath) < 3600)) {
        // If the file exists and is less than an hour old, read the data from the file
        $data = json_decode(file_get_contents($filePath));
    } else {
        // If the file does not exist or is older than an hour, download the data from the API
        $data = json_decode(file_get_contents($apiUrl));
        // Save the data to the file
        file_put_contents($filePath, json_encode($data));
    }
    // Return the weather data
    return $data;
}

$stationName = "Ludwigsburg";
$stationId = getWeatherIdByName($stationName);
$data = getWeatherData($stationId);

//temperature:
for ($i = 0; $i < sizeof($data->{$stationId}->forecast1->temperature); $i++) {
    if ($data->{$stationId}->forecast1->temperature[$i] < 32767) {
        $tempRAW = $data->{$stationId}->forecast1->temperature[$i];
        break;
    }
}
if (!isset($tempRAW)) $tempRAW = $data->{$stationId}->days[0]->temperatureMax - $data->{$stationId}->days[0]->temperatureMin;
$GLOBALS['temperature'] = number_format($tempRAW/10, 1, ',', '.')."°C";
//echo $GLOBALS['temperature'];


//weather:
for ($i = 0; $i < sizeof($data->{$stationId}->forecast1->icon); $i++) {
    if ($data->{$stationId}->forecast1->icon[$i] < 32767) {
        $iconRAW = $data->{$stationId}->forecast1->icon[$i];
        break;
    }
}
if (!isset($iconRAW)) $iconRAW = $data->{$stationId}->days[0]->icon;
$GLOBALS['weathericon'] = $iconRAW;
$GLOBALS['weathertext'] = iconToText($GLOBALS['weathericon']);
//echo $GLOBALS['weathericon'];

?>

    <form>
        <input type="hidden" id="temperature" value="<?php echo $GLOBALS['temperature']; ?>">
        <input type="hidden" id="weathericon" value="<?php echo $GLOBALS['weathericon']; ?>">
    </form>
<?php
    ?>
    <div id='skyandweather-container' class="printmenot">
        <div class="clouds" id="clouds">
            <?php
            if (
                $GLOBALS['weathericon'] ==  "2" || //Sonne, leicht bewölkt
                $GLOBALS['weathericon'] ==  "7" || //leichter Regen
                $GLOBALS['weathericon'] == "10" || //leichter Regen, rutschgefahr
                $GLOBALS['weathericon'] == "12" || //Regen, vereinzelt Schneefall
                $GLOBALS['weathericon'] == "14"    //leichter Schneefall
            ) {
                ?>
                <div class="clouds-1" id="clouds-1"></div>
                <div class="clouds-2" id="clouds-2"></div> <!--wenige Wolken (nur 3)-->
                <?php
            }
            elseif (
                $GLOBALS['weathericon'] ==  "3" || //Sonne, bewölkt
                $GLOBALS['weathericon'] ==  "4" || //Wolken
                $GLOBALS['weathericon'] ==  "8" || //Regen
                $GLOBALS['weathericon'] ==  "9" || //starker Regen
                $GLOBALS['weathericon'] == "11" || //starker Regen, rutschgefahr
                $GLOBALS['weathericon'] == "13" || //Regen, vermehrt Schneefall
                $GLOBALS['weathericon'] == "15" || //Schneefall
                $GLOBALS['weathericon'] == "16" || //starker Schneefall
                $GLOBALS['weathericon'] == "17" || //Wolken, (Hagel)
                $GLOBALS['weathericon'] == "26" || //Gewitter
                $GLOBALS['weathericon'] == "27" || //Gewitter, Regen
                $GLOBALS['weathericon'] == "28" || //Gewitter, starker Regen
                $GLOBALS['weathericon'] == "29" || //Gewitter, (Hagel)
                $GLOBALS['weathericon'] == "30"    //Gewitter, (starker Hagel)
            ) {
                ?>
                <div class="clouds-1" id="clouds-1"></div> <!--viele Wolken (1 und 3; 2 sind nur Schatten)-->
                <div class="clouds-2" id="clouds-2"></div>
                <div class="clouds-3" id="clouds-3"></div>
                <?php
            }
            ?>
        </div>
        <div id='sky'>
            <div id='weatherCutter'>
                <?php
                include ("assets/php/stars.php");
                if (
                    $GLOBALS['weathericon'] ==  "7" || //leichter Regen
                    $GLOBALS['weathericon'] == "10" || //leichter Regen, rutschgefahr
                    $GLOBALS['weathericon'] == "18"    //Sonne, leichter Regen
                ) include ("assets/php/weatherRainLight.php");
                elseif (
                    $GLOBALS['weathericon'] ==  "8" || //Regen
                    $GLOBALS['weathericon'] == "12" || //Regen, vereinzelt Schneefall
                    $GLOBALS['weathericon'] == "17" || //Wolken, (Hagel)
                    $GLOBALS['weathericon'] == "24" || //Sonne, (Hagel)
                    $GLOBALS['weathericon'] == "27" || //Gewitter, Regen
                    $GLOBALS['weathericon'] == "29"    //Gewitter, (Hagel)
                ) include ("assets/php/weatherRainMedium.php");
                elseif (
                    $GLOBALS['weathericon'] ==  "9" || //starker Regen
                    $GLOBALS['weathericon'] == "11" || //starker Regen, rutschgefahr
                    $GLOBALS['weathericon'] == "19" || //Sonne, starker Regen
                    $GLOBALS['weathericon'] == "25" || //Sonne, (staker Hagel)
                    $GLOBALS['weathericon'] == "28" || //Gewitter, starker Regen
                    $GLOBALS['weathericon'] == "30"    //Gewitter, (starker Hagel)
                ) include ("assets/php/weatherRainHeavy.php");
                elseif (
                    $GLOBALS['weathericon'] == "14" || //leichter Schneefall
                    $GLOBALS['weathericon'] == "15" || //Schneefall
                    $GLOBALS['weathericon'] == "16" || //starker Schneefall
                    $GLOBALS['weathericon'] == "22" || //Sonne, vereinzelter Schneefall
                    $GLOBALS['weathericon'] == "23"    //Sonne, vermehrter Schneefall
                ) include ("assets/php/weatherSnow.php");
                elseif (
                    $GLOBALS['weathericon'] == "12" || //Regen, vereinzelt Schneefall
                    $GLOBALS['weathericon'] == "13" || //Regen, vermehrt Schneefall
                    $GLOBALS['weathericon'] == "20" || //Sonne, Regen, vereinzelter Schneefall
                    $GLOBALS['weathericon'] == "21"    //Sonne, Regen, vermehrter Schneefall
                ) {
                    include ("assets/php/weatherRainLight.php");
                    include ("assets/php/weatherSnow.php");
                }
                ?>
            </div>
            <p class="weatherText"><?php /*echo . $GLOBALS['weathertext'] . " bei " . $GLOBALS['temperature']*/ //$stationName?></p>
            <div id='sun'>
            </div>
            <div id="moon">
                <div class="light hemisphere"></div>
                <div class="dark hemisphere"></div>
                <div class="divider"></div>
            </div>
        </div>
        <div class="circle"></div>
    </div>
    <?php
function iconToText($icon) {
    $icons = array(
        1 => 'klarer Himmel',
        2 => 'leicht bewölkt',
        3 => 'bewölkt',
        4 => 'stark bewölkt',
        5 => 'Nebel',
        6 => 'Nebel, rutschgefahr',
        7 => 'leichter Regen',
        8 => 'Regen',
        9 => 'starker Regen',
        10 => 'leichter Regen, rutschgefahr',
        11 => 'starker Regen, rutschgefahr',
        12 => 'Regen, vereinzelt Schneefall',
        13 => 'Regen, vermehrt Schneefall',
        14 => 'leichter Schneefall',
        15 => 'Schneefall',
        16 => 'starker Schneefall',
        17 => 'Wolken, Hagel',
        18 => 'leichter Regen',
        19 => 'starker Regen',
        20 => 'Regen, vereinzelter Schneefall',
        21 => 'Regen, vermehrter Schneefall',
        22 => 'vereinzelter Schneefall',
        23 => 'vermehrter Schneefall',
        24 => 'Hagel',
        25 => 'starker Hagel',
        26 => 'Gewitter',
        27 => 'Gewitter, Regen',
        28 => 'Gewitter, starker Regen',
        29 => 'Gewitter, Hagel',
        30 => 'Gewitter, starker Hagel',
        31 => 'starker Windgang',
    );
    return $icons[$icon];
}


/* some testing
$start = hrtime(true);

$stationId = getWeatherIdByName("ludwigsburg");
$data = getWeatherData($stationId);
echo "Temperatur: " . $data->{$stationId}->forecast1->temperature[0]/10;

$end = hrtime(true);
$elapsed = ($end - $start)/1000000;
echo "<br>Function took $elapsed ms to run.<br>";
*/

/**weather now:
echo $data->{$stationId}->forecast1->temperature[0]/10;
echo $data->{$stationId}->forecast1->icon[0];
echo $data->{$stationId}->forecast1->start;
 */

/** Icon data
 *
1.	Sonne
2.	Sonne, leicht bewölkt
3.	Sonne, bewölkt
4.	Wolken
5.	Nebel
6.	Nebel, rutschgefahr
7.	leichter Regen
8.	Regen
9.	starker Regen
10.	leichter Regen, rutschgefahr
11.	starker Regen, rutschgefahr
12.	Regen, vereinzelt Schneefall
13.	Regen, vermehrt Schneefall
14.	leichter Schneefall
15.	Schneefall
16.	starker Schneefall
17.	Wolken, (Hagel)
18.	Sonne, leichter Regen
19.	Sonne, starker Regen
20.	Sonne, Regen, vereinzelter Schneefall
21.	Sonne, Regen, vermehrter Schneefall
22.	Sonne, vereinzelter Schneefall
23.	Sonne, vermehrter Schneefall
24.	Sonne, (Hagel)
25.	Sonne, (staker Hagel)
26.	Gewitter
27.	Gewitter, Regen
28.	Gewitter, starker Regen
29.	Gewitter, (Hagel)
30.	Gewitter, (starker Hagel)
31.	(Wind)



 */

?>