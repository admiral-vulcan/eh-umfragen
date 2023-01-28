<?php
//require_once("head.php");
//require_once('gitignore/code.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



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




$z = "<p><i>Die Wissenschaft ist Teil der Lebenswirklichkeit; es ist das Was, das Wie und Warum von allem in unserer Erfahrung.</i><br>Rachel Carson</p>";
echo stripHtmlTags($z)["closingTags"];
