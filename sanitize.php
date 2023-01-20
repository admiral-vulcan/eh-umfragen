<?php
function sanitize($string) {
	return
        str_ireplace(
        "<", "(",
        str_ireplace(
        ">", ")",
        str_ireplace(
        "{", "(",
        str_ireplace(
        "}", ")",
        str_ireplace(
        "\"", "”",
        str_ireplace(
        "'", "’",
        str_ireplace(
        "`", "’",
		str_ireplace(
		" DELETE ", "",
		str_ireplace(
		" DROP ", "",
		str_ireplace(
		" INSERT ", "",
		str_ireplace(
		" REFERENCES ", "",
		str_ireplace(
		" SELECT ", "",
		str_ireplace(
		" SET ", "",
		str_ireplace(
		" UPDATE ", "", htmlspecialchars($string)

	))))))))))))));
}

/*
$teststring="string without sql commands DrOp delete INSERT REfERENCES SELEcT UPdATE or html tags <a href=\"https://www.donotgohere.com\"> to prevent crazy stuff from happening</a> but normal brackets are ok: () [] also: 1234567890äöüÄÖÜß";
echo "teststring: ".$teststring;
echo "<br><br>sanitized: ".sanitize($teststring);
*/

?>