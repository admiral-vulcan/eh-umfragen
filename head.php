<?php
if (!isset($title)) $title = "eh-umfragen.de - " . translate("Umfragen von und für Studierende der EH", "de", $GLOBALS["lang"]);
if (!isset($abstract)) $abstract = translate("Hier findet Ihr relevante Umfragen für Euch!", "de", $GLOBALS["lang"]);
if (!isset($description)) $description = translate("Hier findet Ihr Umfragen, die für Euch relevant sind! Ob sie Teil einer modultypischen Arbeit sind oder gerade nur ein Hirngespinst, hier werdet Ihr fündig!", "de", $GLOBALS["lang"]);
if (!isset($keywords)) $keywords = translate("Umfragen Evangelischen Hochschule Ludwigsburg Studierende", "de", $GLOBALS["lang"]);
if (!isset($this_uri)) $this_uri = "https://www.eh-umfragen.de";
if (!isset($GLOBALS["lang"])) $GLOBALS["lang"] = "de";
if (strtolower($GLOBALS["lang"]) == "en") $locale = "en_US";
else $locale = strtolower($GLOBALS["lang"]) . "_" . strtoupper($GLOBALS["lang"]);
?>
<!DOCTYPE HTML>
<html lang="<?php echo $GLOBALS["lang"] ?>" xml:lang="<?php echo $GLOBALS["lang"] ?>" prefix="og: https://ogp.me/ns#">
<head>
    <title><?php echo $title ?></title>
    <meta property="og:title" content="<?php echo $title ?>" />
    <meta property="og:url" content="<?php echo $this_uri ?>" />
    <meta property="og:type" content="Website" />
    <meta property="og:image" content="https://www.eh-umfragen.de/images/logo.png" />
    <meta property="og:description" content="<?php echo $description ?>" />
    <meta property="og:locale" content="<?php echo $locale ?>" />
    <meta property="og:site_name" content="eh-umfragen.de" />
    <meta name="twitter:card" content="<?php echo $abstract ?>" />
    <meta name="twitter:site" content="<?php echo $this_uri ?>" />
    <meta name="twitter:title" content="eh-umfragen.de" />
    <meta name="twitter:description" content="<?php echo $description ?>" />
    <meta name="twitter:image" content="https://www.eh-umfragen.de/images/logo.png" />
    <meta name="description" content="<?php echo $description ?>">
    <meta name="abstract" content="<?php echo $abstract ?>">
    <meta name="keywords" content="<?php echo $keywords ?>">
    <meta name ="rating" content="safe for kids">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes" />
    <meta name="google-site-verification" content="U7PXkW4rjTeYgOfn1WvG0aMP5EX2uYnEbnEPHsDUKkw" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="utf-8" />
    <meta http-equiv="content-language" content="<?php echo $GLOBALS["lang"] ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index,follow">
    <meta name="author" content="Felix Rau">
    <link rel="canonical" href="<?php echo $this_uri ?>"/>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#eed4c8">
    <meta name="theme-color" content="#eed4c8">
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/<?php echo $GLOBALS["color_scheme"]; ?>.css" id="theme-css" />

</head>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ResearchProject",
        "name": "eh-umfragen.de",
        "url": "https://www.eh-umfragen.de/",
        "logo": "https://www.eh-umfragen.de/images/logo.png"
    }
</script>