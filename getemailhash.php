<?php
require_once ("code.php");
if ($_GET["email"]) echo md5(String2Hex($_GET["email"]));
else echo "null";

?>