<section id="intro">
    <header>
        <h2>EH-Umfragen</h2>
        <?php

        use assets\php\classes\Users;
        $users = new Users();

        require_once("head.php");
        require_once("gitignore/code.php");
        require_once ("database_com.php");
        include ("greeting.php");

        $thisGets = "";
        foreach ($_GET as $key => $value) {
            $thisGets .= "$key=" . strval($value) . ", ";
        }
        $thisGets = rtrim($thisGets, ", ");

        if ($_GET["uid"]) {
            $uid = decodeString($_GET["uid"]);
            if ($users->getUserValidation($uid)) {
                alert("Hallo nochmal!", "Deine Mail war bereits validiert.<br><br>Deine Stimmen zählen.", "info");

            }
            else {
                $users->validateUser($uid);
                if ($users->getUserValidation($uid)) {
                    alert("Vielen Dank!", "Deine E-Mail-Adresse wurde erfolgreich validiert.", "info");
                }
                else { //Validate Error 2
                    alert("Datenbank-Fehler", "Deine E-Mail-Adresse konnte nicht validiert werden.", "error", true, "validate error 2 - uid not validated.", $thisGets);
                }
            }
        }
        elseif ($_GET["creator_id"]) {
            $creator_id = decodeString($_GET["creator_id"]);
            if (creator_is_validated($creator_id)) {
                alert("Hallo nochmal!", "Dein Creator-Konto war bereits freigeschaltet.", "info");

            }
            else {
                set_creatorValidated($creator_id);
                if (creator_is_validated($creator_id)) {
                    alert("Vielen Dank!", "Dein Creator-Konto wurde erfolgreich freigeschaltet.", "info");
                }
                else { //Validate Error 3
                    alert("Datenbank-Fehler", "Dein Creator-Konto konnte nicht freigeschaltet werden.", "error", true, "validate error 3 - creator_id not validated.", $thisGets);
                }
            }

        }
        else { //Validate Error 1
            alert("Validierungslink-Fehler", "Der benutzte Link ist fehlerhaft.", "error", true, "validate error 1 - id not set.", $thisGets);
        }
        ?>
    </header>
</section>
