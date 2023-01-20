<?php

$url =  "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

$this_uri = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
//echo $this_uri;
?>