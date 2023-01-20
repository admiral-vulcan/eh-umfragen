<?php

if (isset($_GET["evaluate"]) && $_GET["evaluate"] != "") {

	$thisID = $_GET["evaluate"];

	set_inactive($thisID);

	read_surveys($thisID);

	set_hasresults($thisID, 1);

	//loadResults($thisID);

    alert("Evaluated", "#".$_GET["evaluate"]." has been evaluated.");


}


?>