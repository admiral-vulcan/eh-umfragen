<?php
//require_once("head.php");
//require_once('code.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



if (isset($_GET["test"])) {
	$number = 0;
	$string = $_GET["test"];

	preg_match('/\d+$/', $string, $matches);
	if (isset($matches[0])) $number = intval($matches[0]);
	$string = preg_replace('/\d+$/', '', $string);
	
	echo "comb: \"" . $_GET["test"] . "\"<br>";
	echo "number: \"" . $number . "\"<br>";
	echo "string: \"" . $string . "\"<br>";
}
