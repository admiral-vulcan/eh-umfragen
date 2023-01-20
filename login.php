<section id="intro">
    <header>
        <h1>Creator-Bereich</h1>
        <p><i>Die Neugierde der Kinder ist der Wissensdurst nach Erkenntnis, darum sollte man diese in ihnen fördern und ermutigen.</i><br>John Locke
<?php
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