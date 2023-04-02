<?php
require_once ("gitignore/code.php");
//require_once ("database_com.php");

$psetstr = "";
if (isset($_GET["psetstr"])) $psetstr = $_GET["psetstr"];



function genPwdMailKey($email) {
    return encodeString(time() . rand(100, 999) . $email);
}

function valPwdMailKey($key) {
    $key = decodeString($key);
    $genTime = substr($key, 0, 10);
    $id = substr($key, 13);
    $min_timestamp = 1672527600; //1st Jan 2023 GMT+1
    $max_timestamp = 4828201200; //1st Jan 2123 GMT+1

    if (is_numeric($genTime) && ($genTime >= $min_timestamp) && ($genTime <= $max_timestamp)) //if timestamp is valid (works for the next 100years)
        {
        if (time() - $genTime < 86400) return $id; //if timestamp is not older than 24h
            else { //timestamp to old
                alert("Passwortlink abgelaufen", "Der benutzte Link ist älter als 24 Stunden. Aus Sicherheitsgründen bitten wir Dich, das Passwort neu zurückzusetzen.", "warning", true);
                return -1;
            }
    } else { //not a valid Link
        alert("Passwortlink-Fehler", "Der benutzte Link ist fehlerhaft.", "error", true, "password link error 1 - could not interpret.", $key);
        return -1;
    }
}
/*
if ($psetstr != "") {
    $key = valPwdMailKey($psetstr);
    if ($key != -1 && $key != -2) {
        //print the password fields
        ?>

        <?php
    }
}

genPwdMailKey(1234);
echo "<br>";
*/
?>