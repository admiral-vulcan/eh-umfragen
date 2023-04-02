<section id="intro">
    <header>
        <h1>
            <?php use EHUmfragen\DatabaseModels\Creators;

            echo translate("Creator-Bereich", "de", $GLOBALS["lang"]);
            echo "</h1>";

            $creators = new Creators();
            require_once ("zitate.php");
            echo zitat("neugierde");
            my_session_start();
            if(isset($_SESSION['creator_id']) && $_SESSION['creator_id'] != "") $creators->fillSession($_SESSION['creator_id']);
            if (!isset($_SESSION['creator_id']) || $_SESSION['creator_id'] == "") include ("challenge.php");
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