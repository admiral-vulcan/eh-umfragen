<h2><?php echo translate("Mein Creator", "de", $GLOBALS["lang"]); ?></h2>
<p><?php echo translate("Hier kannst Du Deine Umfragen erstellen, einsehen und auswerten. Beginne damit, oben (auf Mobilgeräten zuerst auf Datei) auf Neu zu klicken und schon kann's losgehen. Viel Spaß!", "de", $GLOBALS["lang"]); ?></p>

<?php
if ($GLOBALS['lang'] !== "de") {
    echo "<p>" . translate("Deine Sprache ist nicht auf Deutsch gestellt. Das heißt, dass alle Deine Inhalte aus dem " . getLanguage($GLOBALS["lang"]) . "en übersetzt werden, bevor sie in die Datenbank aufgenommen werden. Schreibe in " . getLanguage($GLOBALS["lang"]) . ", damit die automatische Übersetzung funktioniert. Die Übersetzung kann trotzdem ungenau sein, überlege auf Deutsch umzustellen.", "de", $GLOBALS["lang"]) . " ";
    if ($GLOBALS['lang'] !== "en") echo translate("Verwende alternativ die englische Version, da diese Übersetzung am Besten funktioniert.", "de", $GLOBALS["lang"]) . " ";
    echo translate("Wenn du das willst, stelle die Sprache im Menü um.", "de", $GLOBALS["lang"]) . "</p>";
}
?>

<p><?php echo translate("Dieser Bereich ist im Entstehen und funktioniert noch nicht (richtig).", "de", $GLOBALS["lang"]); ?></p>
<!-- <div style="position: fixed; top: 4em;"> -->
<div class="creator-buttons" id="creator-buttons">
    <button id="file-menu-toggle" onclick="toggleFileMenu();"><?php echo translate("Datei", "de", $GLOBALS["lang"]); ?></button>
    <button id="edit-menu-toggle" onclick="toggleEditMenu();"><?php echo translate("Bearbeiten", "de", $GLOBALS["lang"]); ?></button>
    <div class="file-menu-items" id="file-menu-items">
        <button onclick="button_new();"><?php echo translate("Neu", "de", $GLOBALS["lang"]); ?></button>
        <button onclick="button_open();"><?php echo translate("Öffnen", "de", $GLOBALS["lang"]); ?></button>
        <button onclick="button_save();"><?php echo translate("Speichern", "de", $GLOBALS["lang"]); ?></button>
        <button onclick="button_close();"><?php echo translate("Schließen", "de", $GLOBALS["lang"]); ?></button>
    </div>
    <div class="edit-menu-items" id="edit-menu-items">
        <button id="button_undo"><?php echo translate("Rückgängig", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_redo"><?php if ($GLOBALS["lang"]==="en")echo "Redo"; else echo translate("Wiederherstellen", "de", $GLOBALS["lang"]); ?></button>
        <button onclick="button_preview();"><?php echo translate("Vorschau", "de", $GLOBALS["lang"]); ?></button>
        <button onclick="button_evaluate();"><?php echo translate("Auswerten", "de", $GLOBALS["lang"]); ?></button>
        <button onclick="button_delete();"><?php echo translate("Löschen", "de", $GLOBALS["lang"]); ?></button>
    </div>
</div>
<br>

<?php
include ("survey-builder.php");
?>

<div class="my-surveys" id="my-surveys">
    <form>
        <?php
        echo "<h3>" . translate("Meine Umfragen", "de", $GLOBALS["lang"]) . "</h3>";
        echo "<table class='creator'>";
        echo "<tr>";
        echo "<th>#</th>";
        echo "<th></th>";
        echo "<th>" . translate("Umfragetitel", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Creator", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Beteiligte", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Aktiv", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Ausgewertet", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Aktiv seit", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Inaktiv seit", "de", $GLOBALS["lang"]) . "</th>";
        echo "</tr>";

        foreach ($_SESSION['my_creations'] as $key => $value) {
            $survey = get_survey($value);
            $survey_name = $survey['name'];
            $survey_creator = get_creator_name($survey['creator']);
            $survey_contributors = get_survey_contributors_names($value);
            echo "<tr>";
            echo "<td>" . $value . "</td>";
            echo "<td><input type='radio' name='Umfrage' id='" . $value . "'><label class='my-surveys' for='" . $value . "'></label></td>";
            echo "<td>" . translate($survey_name, "de", $GLOBALS["lang"]) . "</td>";
            echo "<td>" . $survey_creator['first'] . " " . $survey_creator['family'] . "</td>";


            echo "<td>";
            foreach ($survey_contributors as $conkey => $contributor) {
                if (isset($contributor['first']) && $contributor['first'] !== "") {
                    echo $contributor['first'] . " " . $contributor['family'];
                    if (isset($survey_contributors[$conkey+1]['first']) && $survey_contributors[$conkey+1]['first'] !== "") {
                        echo "; ";
                    }
                }
            }
            echo "</td>";



            echo "<td>";
            if ($survey['isactive'] === 1) echo translate("Ja", "de", $GLOBALS["lang"]);
            else  echo translate("Nein", "de", $GLOBALS["lang"]);
            echo "</td>";
            echo "<td>";
            if ($survey['hasresults'] === 1) echo translate("Ja", "de", $GLOBALS["lang"]);
            else  echo translate("Nein", "de", $GLOBALS["lang"]);
            echo "</td>";

            echo "<td>";
            if (isset($survey['since']) && $survey['since'] !== "") {
                if ($GLOBALS["lang"] === "en") echo date("M/j/Y g:i A", $survey['since']);
                else echo translate(date("j.n.Y G:i", $survey['since']) . " Uhr", "de", $GLOBALS["lang"]);
            }
            echo "</td>";
            echo "<td>";
            if (isset($survey['inactivesince']) && $survey['inactivesince'] !== "") {
                if ($GLOBALS["lang"] === "en") echo date("M/j/Y g:i A", $survey['inactivesince']);
                else echo translate(date("j.n.Y G:i", $survey['inactivesince']) . " Uhr", "de", $GLOBALS["lang"]);
            }
            echo "</td>";

            echo "</tr>";
        }
        echo "</table>";


        echo "<h3>" . translate("Andere Umfragen", "de", $GLOBALS["lang"]) . "</h3>";
        echo "<table class='creator'>";
        echo "<tr>";

        echo "<th>#</th>";
        echo "<th></th>";
        echo "<th>" . translate("Umfragetitel", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Creator", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Beteiligte", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Aktiv", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Ausgewertet", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Aktiv seit", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Inaktiv seit", "de", $GLOBALS["lang"]) . "</th>";

        echo "</tr>";

        foreach ($_SESSION['my_contributions'] as $key => $value) {
            $survey = get_survey($value);
            $survey_name = $survey['name'];
            $survey_creator = get_creator_name($survey['creator']);
            $survey_contributors = get_survey_contributors_names($value);
            echo "<tr>";
            echo "<td>" . $value . "</td>";
            echo "<td><input type='radio' name='Umfrage' id='" . $value . "'><label class='my-surveys' for='" . $value . "'></label></td>";
            echo "<td>" . translate($survey_name, "de", $GLOBALS["lang"]) . "</td>";
            echo "<td>" . $survey_creator['first'] . " " . $survey_creator['family'] . "</td>";


            echo "<td>";
            foreach ($survey_contributors as $conkey => $contributor) {
                if (isset($contributor['first']) && $contributor['first'] !== "") {
                    echo $contributor['first'] . " " . $contributor['family'];
                    if (isset($survey_contributors[$conkey+1]['first']) && $survey_contributors[$conkey+1]['first'] !== "") {
                        echo "; ";
                    }
                }
            }
            echo "</td>";



            echo "<td>";
            if ($survey['isactive'] === 1) echo translate("Ja", "de", $GLOBALS["lang"]);
            else  echo translate("Nein", "de", $GLOBALS["lang"]);
            echo "</td>";
            echo "<td>";
            if ($survey['hasresults'] === 1) echo translate("Ja", "de", $GLOBALS["lang"]);
            else  echo translate("Nein", "de", $GLOBALS["lang"]);
            echo "</td>";

            echo "<td>";
            if (isset($survey['since']) && $survey['since'] !== "") {
                if ($GLOBALS["lang"] === "en") echo date("M/j/Y g:i A", $survey['since']);
                else echo translate(date("j.n.Y G:i", $survey['since']) . " Uhr", "de", $GLOBALS["lang"]);
            }
            echo "</td>";
            echo "<td>";
            if (isset($survey['inactivesince']) && $survey['inactivesince'] !== "") {
                if ($GLOBALS["lang"] === "en") echo date("M/j/Y g:i A", $survey['inactivesince']);
                else echo translate(date("j.n.Y G:i", $survey['inactivesince']) . " Uhr", "de", $GLOBALS["lang"]);
            }
            echo "</td>";

            echo "</tr>";
        }
        echo "</table>";
        ?>
        <button type="button" id="open_selection"><?php echo translate('Öffnen', 'de', $GLOBALS['lang']); ?></button>
    </form>
</div>
<script type="application/javascript">
    var mySurveys = document.getElementById("my-surveys");
    var builder = document.getElementById("builder");
    //var sizehelper = document.getElementById("sizehelper");
    //var sizehelperOpacity = window.getComputedStyle(mySurveys).getPropertyValue("opacity");
    document.addEventListener('DOMContentLoaded', setButtonsPosition);
    window.addEventListener('resize', setButtonsPosition);

    function setButtonsPosition() {
        const creatorButtons = document.getElementById("creator-buttons");
        const topLinks = document.getElementsByClassName('links')[0];
        creatorButtons.style.left = topLinks.getBoundingClientRect().left + 4 + "px";

    }
    function setMenuItemsPosition() {
        const fileToggleElement = document.getElementById("file-menu-toggle");
        const editToggleElement = document.getElementById("edit-menu-toggle");
        const editMenuItemsElement = document.getElementById("edit-menu-items");

        const positionOffsetRect = fileToggleElement.getBoundingClientRect();
        const positionOffsetXPosition = positionOffsetRect.left;
        const editToggleRect = editToggleElement.getBoundingClientRect();
        const editToggleXPosition = editToggleRect.left;

        editMenuItemsElement.style.left = editToggleXPosition - positionOffsetXPosition + "px";
    }

    function closeMenusOnClick(event) {
        const fileMenuToggleElement = document.getElementById("file-menu-toggle");
        const fileMenuItemsElement = document.getElementById("file-menu-items");
        const editMenuToggleElement = document.getElementById("edit-menu-toggle");
        const editMenuItemsElement = document.getElementById("edit-menu-items");
        var fileDisplay = window.getComputedStyle(document.getElementById("file-menu-items")).getPropertyValue("display");
        var editDisplay = window.getComputedStyle(document.getElementById("edit-menu-items")).getPropertyValue("display");

        if (window.innerWidth <= 1430) {
            if (fileDisplay !== "none" && !fileMenuToggleElement.contains(event.target) && !fileMenuItemsElement.contains(event.target)) {
                toggleFileMenu();
            }

            if (editDisplay !== "none" && !editMenuToggleElement.contains(event.target) && !editMenuItemsElement.contains(event.target)) {
                toggleEditMenu();
            }
        }
    }

    function toggleFileMenu() {
        if (getWidth() < 1430) {
            const menuItems = document.getElementById("file-menu-items");
            if (menuItems.style.display !== "inline-block" && menuItems.style.display !== "flex") {
                // show menu items with animation
                menuItems.style.display = "flex";
                setMenuItemsPosition();
                var opacity = 0; // initial opacity
                var id = setInterval(frame, 15); // interval function
                function frame() {
                    if (opacity >= 1) {
                        // stop animation when opacity reaches 1
                        clearInterval(id);
                    } else {
                        opacity += 0.1; // increase opacity by 0.01 each time
                        menuItems.style.opacity = opacity; // set opacity style
                    }
                }
            } else {
                // hide menu items with animation
                var opacity = 1; // initial opacity
                var id = setInterval(frame, 15); // interval function
                function frame() {
                    if (opacity <= 0) {
                        // stop animation when opacity reaches 0
                        clearInterval(id);
                        menuItems.style.display = "none";
                    } else {
                        opacity -= 0.1; // decrease opacity by 0.01 each time
                        menuItems.style.opacity = opacity; // set opacity style
                    }
                }
            }
        }
    }

    function toggleEditMenu() {
        if (getWidth() < 1430) {
            const menuItems = document.getElementById('edit-menu-items');
            if (menuItems.style.display !== "inline-block" && menuItems.style.display !== "flex") {
                // show menu items with animation
                menuItems.style.display = "flex";
                setMenuItemsPosition();
                var opacity = 0; // initial opacity
                var id = setInterval(frame, 15); // interval function
                function frame() {
                    if (opacity >= 1) {
                        // stop animation when opacity reaches 1
                        clearInterval(id);
                    } else {
                        opacity += 0.1; // increase opacity by 0.01 each time
                        menuItems.style.opacity = opacity; // set opacity style
                    }
                }
            } else {
                // hide menu items with animation
                var opacity = 1; // initial opacity
                var id = setInterval(frame, 15); // interval function
                function frame() {
                    if (opacity <= 0) {
                        // stop animation when opacity reaches 0
                        clearInterval(id);
                        menuItems.style.display = "none";
                    } else {
                        opacity -= 0.1; // decrease opacity by 0.01 each time
                        menuItems.style.opacity = opacity; // set opacity style
                    }
                }
            }
        }
    }

    document.addEventListener('click', closeMenusOnClick);

    window.addEventListener("resize", setMenuItemsPosition);
    window.addEventListener('resize', handleResize);

    function handleResize() {
        const fileMenuItems = document.getElementById('file-menu-items');
        const EditMenuItems = document.getElementById('edit-menu-items');

        if (window.innerWidth > 1430) {
            fileMenuItems.style.display = 'inline-block';
            EditMenuItems.style.display = 'inline-block';
            fileMenuItems.style.opacity = '1';
            EditMenuItems.style.opacity = '1';
        }
        else {
            fileMenuItems.style.display = 'none';
            EditMenuItems.style.display = 'none';
            fileMenuItems.style.opacity = '0';
            EditMenuItems.style.opacity = '0';
        }
    }

    function get_select_option() {
        var radios = document.getElementsByName('Umfrage');

        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                return radios[i].id;
            }
        }

        return null;
    }

    function button_new() {
        toggleFileMenu();
        undoAll();
        clearTexts();
        resetSelects();
        mySurveys.style.display = "none";
        builder.style.display = "block";
    }

    function button_open() {
        toggleFileMenu();
        uncheckRadios();
        undoAll();
        clearTexts();
        resetSelects();
        mySurveys.style.display = "block";
        builder.style.display = "none";
    }

    function button_save() {
        toggleFileMenu();
        sendDataToServer(collectData(false)); //true if final, false if draft
    }

    function button_evaluate() {
        var selected = get_select_option();
        alert("Werte "+selected+" aus");
    }

    function button_preview() {
        var selected = get_select_option();
        alert("Vorschau von " +selected);
    }

    function button_delete() {
        var selected = get_select_option();
        alert("Lösche "+selected);
    }

    function button_close() {
        toggleFileMenu();
        uncheckRadios();
        undoAll();
        clearTexts();
        resetSelects();
        mySurveys.style.display = "none";
        builder.style.display = "none";
    }

    function uncheckRadios() {
        var radios = document.getElementsByName("Umfrage"); // select all radio buttons with name="Umfrage"
        for (var i = 0; i < radios.length; i++) { // loop through them
            radios[i].checked = false; // uncheck each one
        }
    }

    function clearTexts() {
        var inputs = document.querySelectorAll("input[type=text]"); // get all input elements with type="text"
        for (var i = 0; i < inputs.length; i++) {
            // loop through them
            inputs[i].value = ""; // set their value to an empty string
        }
    }

    function resetSelects() {
        var selects = document.getElementsByTagName("select"); // get all select elements
        for (var i = 0; i < selects.length; i++) {
            // loop through them
            selects[i].selectedIndex = 0; // set their selectedIndex to 0
        }
    }

    function undoAll() {
        while (undoStack.length > 0) { // loop until undo stack is empty
            const command = undoStack.pop(); // pop the last command
            command.unexecute(); // unexecute it
            redoStack.push(command); // push it to redo stack
        }
        emailDomainInput.style.display = "none";
        emailDomainLabel.style.display = "none";
        questionCount = 0;
    }

    function getWidth() {
        return Math.max(
            document.body.scrollWidth,
            document.documentElement.scrollWidth,
            document.body.offsetWidth,
            document.documentElement.offsetWidth,
            document.documentElement.clientWidth
        );
    }
</script>

