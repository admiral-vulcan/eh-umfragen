<section id="intro">
    <header>
        <h1><?php echo translate("Creator-Bereich", "de", $GLOBALS["lang"]); ?></h1>
<?php
require_once ("zitate.php");
echo zitat("neugierde");
my_session_start();
get_creator_data($_SESSION['cid']);
if (!isset($_SESSION['cid']) || $_SESSION['cid'] == "") include ("challenge.php");
elseif ($_GET["creator"] == "challenge") include ("creator.php");
elseif ($_GET["creator"] == "creator") include ("creator.php");
elseif ($_GET["creator"] == "profile") include ("profile.php");
elseif ($_GET["creator"] == "logout") {
    logout();
    alert("Abmeldung", "Du wurdest abgemeldet.", "info", true, "/");
}
else include ($_GET["creator"] . ".php");
?>
<br>
    </header>
</section>