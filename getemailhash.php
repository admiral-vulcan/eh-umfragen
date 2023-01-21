<?php
require_once("gitignore/code.php");
if ($_GET["email"]) echo md5(String2Hex($_GET["email"]));
else echo "null";

?>