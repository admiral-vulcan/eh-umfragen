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
<div class="" id="preset-buttons"></div>
<div class="creator-buttons" id="creator-buttons">
    <button id="file-menu-toggle" onclick="toggleFileMenu();"><?php echo translate("Datei", "de", $GLOBALS["lang"]); ?></button>
    <button id="edit-menu-toggle" onclick="toggleEditMenu();"><?php echo translate("Bearbeiten", "de", $GLOBALS["lang"]); ?></button>
    <button id="survey-menu-toggle" onclick="toggleSurveyMenu();"><?php echo translate("Umfrage", "de", $GLOBALS["lang"]); ?></button>
    <div class="file-menu-items" id="file-menu-items">
        <button id="button_new" onclick="button_new();"><?php echo translate("Neu", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_open" onclick="button_open();"><?php echo translate("Öffnen", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_save" onclick="button_save();" disabled><?php echo translate("Speichern", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_close" onclick="button_close();" disabled><?php echo translate("Schließen", "de", $GLOBALS["lang"]); ?></button>
    </div>
    <div class="edit-menu-items" id="edit-menu-items">
        <button id="button_undo" disabled><?php echo translate("Rückgängig", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_redo" disabled><?php if ($GLOBALS["lang"]==="en")echo "Redo"; else echo translate("Wiederherstellen", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_delete" onclick="button_delete();" disabled><?php echo translate("Löschen", "de", $GLOBALS["lang"]); ?></button>
    </div>

    <div class="survey-menu-items" id="survey-menu-items">
        <button id="button_draft" onclick="button_draft();" disabled><?php echo translate("Vorschau", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_final" onclick="button_final();" disabled><?php echo translate("Final", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_evaluate" onclick="button_evaluate();" disabled><?php echo translate("Auswerten", "de", $GLOBALS["lang"]); ?></button>
        <button id="button_presets" onclick="togglePresetsButtons();" disabled><?php echo translate("Presets", "de", $GLOBALS["lang"]); ?></button>
    </div>
</div>
<br>

<?php

if (isset($_GET["open"]) && $_GET["open"] !== "") {
    $openGet = decodeString($_GET["open"]);
    $openFile = explode("/", $openGet)[1];
    $openCid = explode("-", $openFile)[4];
    /** TODO check for contributors!!! */
    if (str_contains($openCid, $GLOBALS["cid"])) {
        $openPath = $openGet . ".csv";
        $openFile = explode("/", $openPath)[1];
        $openFinal = explode("/", $openPath)[0] === "surveys";
        $openCSV = readCSVFile($openFinal, $openFile);
        $openDeconstructData = deconstructData($openCSV, $openFinal, $openCid, $openFile, $openCSV[0][0]);
        echo "<script type='application/javascript'> const openDeconstructJson = '" . json_encode($openDeconstructData, true) . "';</script>";
    }
}

include ("survey-builder.php");
$warnUserNew = alert("Datenverlust", "Bist Du sicher, dass Du die Sprache jetzt wechseln willst? Alle nicht gespeicherten Änderungen gehen dabei verloren.", "warning", false, "userLeaves();", "userStays();");

//open, close, auswerten, löschen

?>

<div class="my-surveys" id="my-surveys">
    <p><a href="" onclick="window.location.reload(true);"><?php echo translate('Neu laden, um neue Umfragen anzuzeigen.', 'de', $GLOBALS['lang']); ?></a></p>
    <form>
        <?php
        echo "<h3>" . translate("Meine Entwürfe", "de", $GLOBALS["lang"]) . "</h3>";
        echo "<table class='creator'>";
        echo "<tr>";
        echo "<th>#</th>";
        echo "<th></th>";
        echo "<th>" . translate("Umfragetitel", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Creator", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Beteiligte", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th class='hide-on-mobile'>" . translate("Aktiv", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th class='hide-on-mobile'>" . translate("Ausgewertet", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th class='hide-on-mobile hide-on-tablet'>" . translate("Aktiv seit", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th class='hide-on-mobile hide-on-tablet'>" . translate("Inaktiv seit", "de", $GLOBALS["lang"]) . "</th>";
        echo "</tr>";

        foreach (getDraftsNames($_SESSION['cid']) as $key => $value) {
            $survey_name = $value['draftsname'];
            $survey_creator = $_SESSION['fullname'];
            $survey_contributors = [];
            $survey_filename = encodeString('survey-drafts/' . str_replace('.csv', '', $value['filename']));
            echo "<tr>";
            /* #        */ echo "<td>" . " " . "</td>";
            /* checkbox */ echo "<td><input type='radio' name='Umfrage' id='" . $survey_filename . "'><label class='my-surveys' for='" . $survey_filename . "'></label></td>";
            /* title    */ echo "<td>" . translate($survey_name, "de", $GLOBALS["lang"]) . "</td>";
            /* creator  */ echo "<td>" . $survey_creator . "</td>";


            /* contrib  */ echo "<td class='hide-on-mobile'>";
            foreach ($survey_contributors as $conkey => $contributor) {
                if (isset($contributor['first']) && $contributor['first'] !== "") {
                    echo $contributor['first'] . " " . $contributor['family'];
                    if (isset($survey_contributors[$conkey+1]['first']) && $survey_contributors[$conkey+1]['first'] !== "") {
                        echo "; ";
                    }
                }
            }
            echo "</td><td class='hide-on-mobile'></td><td class='hide-on-mobile'></td><td class='hide-on-mobile hide-on-tablet'></td><td class='hide-on-mobile hide-on-tablet'></td></tr>";

        }
        echo "</table>";

        echo "<h3>" . translate("Meine Umfragen", "de", $GLOBALS["lang"]) . "</h3>";
        echo "<table class='creator'>";
        echo "<tr>";
        echo "<th>#</th>";
        echo "<th></th>";
        echo "<th>" . translate("Umfragetitel", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Creator", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th>" . translate("Beteiligte", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th class='hide-on-mobile'>" . translate("Aktiv", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th class='hide-on-mobile'>" . translate("Ausgewertet", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th class='hide-on-mobile hide-on-tablet'>" . translate("Aktiv seit", "de", $GLOBALS["lang"]) . "</th>";
        echo "<th class='hide-on-mobile hide-on-tablet'>" . translate("Inaktiv seit", "de", $GLOBALS["lang"]) . "</th>";
        echo "</tr>";

        $mySurveyList = [];
        if (is_array($_SESSION['my_creations']) && is_array($_SESSION['my_contributions'])) {
            $mySurveyList = array_merge($_SESSION['my_creations'], $_SESSION['my_contributions']);
        } elseif (is_array($_SESSION['my_creations'])) {
            $mySurveyList = $_SESSION['my_creations'];
        } elseif (is_array($_SESSION['my_contributions'])) {
            $mySurveyList = $_SESSION['my_contributions'];
        }
        if (!empty($mySurveyList)) {
            sort($mySurveyList);
        }

        foreach ($mySurveyList as $key => $value) {
            $survey = get_survey($value);
            $b = "";
            $Eb = "";
            if ($_SESSION['cid'] === $survey['creator']) {
                $b = "<b>";
                $Eb = "</b>";
            }
            $survey_name = $survey['name'];
            $survey_creator = get_creator_name($survey['creator']);
            $survey_contributors = get_survey_contributors_names($value);
            $survey_filename = encodeString('surveys/' . str_replace('.csv', '', get_survey_filename($value)));
            echo "<tr>";
            /* #        */ echo "<td>" . $b . $value . $Eb . "</td>";
            /* checkbox */ echo "<td><input type='radio' name='Umfrage' id='" . $survey_filename . "'" . ($b===""?"disabled":"") . "><label class='my-surveys' for='" . $survey_filename . "'></label></td>";
            /* title    */ echo "<td>" . $b . translate($survey_name, "de", $GLOBALS["lang"]) . $Eb . "</td>";
            /* creator  */ echo "<td>" . $survey_creator['first'] . " " . $survey_creator['family'] . "</td>";


            /* contrib  */ echo "<td>";
            foreach ($survey_contributors as $conkey => $contributor) {
                if (isset($contributor['first']) && $contributor['first'] !== "") {
                    echo $contributor['first'] . " " . $contributor['family'];
                    if (isset($survey_contributors[$conkey + 1]['first']) && $survey_contributors[$conkey + 1]['first'] !== "") {
                        echo "; ";
                    }
                }
            }
            echo "</td>";

            /* active   */ echo "<td class='hide-on-mobile'>";
            if ($survey['isactive'] === 1) echo translate("Ja", "de", $GLOBALS["lang"]);
            else  echo translate("Nein", "de", $GLOBALS["lang"]);
            echo "</td>";
            /* eval     */ echo "<td class='hide-on-mobile'>";
            if ($survey['hasresults'] === 1) echo translate("Ja", "de", $GLOBALS["lang"]);
            else  echo translate("Nein", "de", $GLOBALS["lang"]);
            echo "</td>";

            /* since    */ echo "<td class='hide-on-mobile hide-on-tablet'>";
            if (isset($survey['since']) && $survey['since'] !== "") {
                if ($GLOBALS["lang"] === "en") echo date("M/j/Y g:i A", $survey['since']);
                else echo translate(date("j.n.Y G:i", $survey['since']) . " Uhr", "de", $GLOBALS["lang"]);
            }
            echo "</td>";
            /* inasince */ echo "<td class='hide-on-mobile hide-on-tablet'>";
            if (isset($survey['inactivesince']) && $survey['inactivesince'] !== "") {
                if ($GLOBALS["lang"] === "en") echo date("M/j/Y g:i A", $survey['inactivesince']);
                else echo translate(date("j.n.Y G:i", $survey['inactivesince']) . " Uhr", "de", $GLOBALS["lang"]);
            }
            echo "</td>";

            echo "</tr>";
        }
        echo "</table>";
        ?>
    </form>
</div>
<script type="application/javascript">
    var mySurveys = document.getElementById("my-surveys");
    var builder = document.getElementById("builder");
    document.addEventListener('DOMContentLoaded', setButtonsPosition);
    window.addEventListener('resize', setButtonsPosition);
    document.addEventListener("DOMContentLoaded", setPositionPresetButtons);
    window.addEventListener("resize", setPositionPresetButtons);
    var originalFilename = 0;
    var originalSid = 0;

    function setButtonsPosition() {
        const creatorButtons = document.getElementById("creator-buttons");
        const topLinks = document.getElementsByClassName('links')[0];
        creatorButtons.style.left = topLinks.getBoundingClientRect().left + 4 + "px";

    }

    function setPositionPresetButtons() {
        var presets = document.getElementById("button_presets");
        var presetButtons = document.getElementById("preset-buttons");
        var rect = presets.getBoundingClientRect();

        presetButtons.style.left = rect.left + "px";
        presetButtons.style.top = rect.bottom + "px";
    }


    function setMenuItemsPosition() {
        const fileToggleElement = document.getElementById("file-menu-toggle");
        const editToggleElement = document.getElementById("edit-menu-toggle");
        const editMenuItemsElement = document.getElementById("edit-menu-items");
        const surveyToggleElement = document.getElementById("survey-menu-toggle"); // Added
        const surveyMenuItemsElement = document.getElementById("survey-menu-items"); // Added

        const positionOffsetRect = fileToggleElement.getBoundingClientRect();
        const positionOffsetXPosition = positionOffsetRect.left;
        const editToggleRect = editToggleElement.getBoundingClientRect();
        const editToggleXPosition = editToggleRect.left;
        const surveyToggleRect = surveyToggleElement.getBoundingClientRect(); // Added
        const surveyToggleXPosition = surveyToggleRect.left; // Added

        editMenuItemsElement.style.left = editToggleXPosition - positionOffsetXPosition + "px";
        surveyMenuItemsElement.style.left = surveyToggleXPosition - positionOffsetXPosition + "px"; // Added
        adjustButtonStyles();
    }

    document.addEventListener('click', closeMenusOnClick);
    let lastCallTime = 0;

    function closeMenusOnClick(event) {

        const currentTime = new Date().getTime();
        const timeSinceLastCall = currentTime - lastCallTime;

        if (timeSinceLastCall < 50) {
            lastCallTime = currentTime;
            return; // Ignore the click if the last call was within the last 50ms
        }

        lastCallTime = currentTime;
        const fileMenuToggleElement = document.getElementById("file-menu-toggle");
        const editMenuToggleElement = document.getElementById("edit-menu-toggle");
        const surveyMenuToggleElement = document.getElementById("survey-menu-toggle");

        const fileMenuItemsElement = document.getElementById("file-menu-items");
        const editMenuItemsElement = document.getElementById("edit-menu-items");
        const surveyMenuItemsElement = document.getElementById("survey-menu-items");

        const presetsMenuItemsElement = document.getElementById("preset-buttons");
        const addButton = document.getElementById("add-question");
        var fileDisplay = window.getComputedStyle(document.getElementById("file-menu-items")).getPropertyValue("display");
        var editDisplay = window.getComputedStyle(document.getElementById("edit-menu-items")).getPropertyValue("display");
        var surveyDisplay = window.getComputedStyle(document.getElementById("survey-menu-items")).getPropertyValue("display");
        var presetsOpacity = window.getComputedStyle(document.getElementById("preset-buttons")).getPropertyValue("opacity");

        if (presetsOpacity === "1" && !presetsMenuItemsElement.contains(event.target) && !addButton.contains(event.target)) {
            togglePresetsButtons();
        }

        if (window.innerWidth <= 1850) {
            if (fileDisplay !== "none" && !fileMenuToggleElement.contains(event.target) && !fileMenuItemsElement.contains(event.target)) {
                toggleFileMenu();
            }

            if (editDisplay !== "none" && !editMenuToggleElement.contains(event.target) && !editMenuItemsElement.contains(event.target)) {
                toggleEditMenu();
            }

            if (surveyDisplay !== "none" && !surveyMenuToggleElement.contains(event.target) && !surveyMenuItemsElement.contains(event.target) && !presetsMenuItemsElement.contains(event.target) && !addButton.contains(event.target)) {
                toggleSurveyMenu();
            }
        }
    }

    function toggleFileMenu() {
        if (getWidth() < 1850) {
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
        if (checkUndo()) document.getElementById("button_undo").removeAttribute("disabled");
        else document.getElementById("button_undo").setAttribute("disabled", true);
        if (checkRedo()) document.getElementById("button_redo").removeAttribute("disabled");
        else document.getElementById("button_redo").setAttribute("disabled", true);

        if (getWidth() < 1850) {
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

    function toggleSurveyMenu() {
        if (getWidth() < 1850) {
            const menuItems = document.getElementById('survey-menu-items');
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

    function togglePresetsButtons() {
            const menuItems = document.getElementById('preset-buttons');
            if (menuItems.style.display !== "inline-block" && menuItems.style.display !== "flex") {
                // show menu items with animation
                menuItems.style.display = "flex";
                setPositionPresetButtons();
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

    //document.addEventListener('click', closeMenusOnClick);
    window.addEventListener('resize', handleResize);

    function handleResize() {
        const fileMenuItems = document.getElementById('file-menu-items');
        const EditMenuItems = document.getElementById('edit-menu-items');
        const SurveyMenuItems = document.getElementById('survey-menu-items');
        const presetButtons = document.getElementById('preset-buttons');

        presetButtons.style.display = 'none';
        presetButtons.style.opacity = '0';

        if (window.innerWidth > 1850) {
            fileMenuItems.style.display = 'inline-block';
            EditMenuItems.style.display = 'inline-block';
            SurveyMenuItems.style.display = 'inline-block';
            fileMenuItems.style.opacity = '1';
            EditMenuItems.style.opacity = '1';
            SurveyMenuItems.style.opacity = '1';
        }
        else {
            fileMenuItems.style.display = 'none';
            EditMenuItems.style.display = 'none';
            SurveyMenuItems.style.display = 'none';
            fileMenuItems.style.opacity = '0';
            EditMenuItems.style.opacity = '0';
            SurveyMenuItems.style.opacity = '0';
        }
        adjustButtonStyles();
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
        //document.getElementById("button_save").setAttribute("disabled", true);
        document.getElementById("button_save").removeAttribute("disabled");
        document.getElementById("button_close").removeAttribute("disabled");
        document.getElementById("button_delete").removeAttribute("disabled");
        document.getElementById("button_draft").removeAttribute("disabled");
        document.getElementById("button_final").removeAttribute("disabled");
        document.getElementById("button_evaluate").removeAttribute("disabled");
        document.getElementById("button_presets").removeAttribute("disabled");
        toggleFileMenu();
        undoAll();
        clearTexts();
        resetSelects();
        mySurveys.style.display = "none";
        builder.style.display = "block";
    }

    function button_open() {
        toggleFileMenu();
        if (window.getComputedStyle(mySurveys).display === "none") {
            uncheckRadios();
            undoAll();
            clearTexts();
            resetSelects();
            mySurveys.style.display = "block";
            builder.style.display = "none";

        }
        else {
            const radioButtons = document.querySelectorAll('input[type="radio"][name="Umfrage"]');
            let checkedId;

            for (const radioButton of radioButtons) {
                if (radioButton.checked) {
                    checkedId = radioButton.id;
                    break;
                }
            }

            if (checkedId) {
                let url = new URL(window.location.href);

                // Remove the 'open' parameter if it exists
                url.searchParams.delete('open');

                // Reconstruct the URL without the 'open' parameter
                let openURL = url.pathname + url.search;
                openURL += "&open=" + checkedId;
                window.location.href = openURL;
            }
            else alert("No survey checked");
        }
    }

    async function button_save() {
        toggleFileMenu();
        await sendDataToServer(collectData(false)); //true if final, false if draft
    }

    function button_evaluate() {
        var selected = get_select_option();
        alert("Werte "+selected+" aus");
    }

    async function button_draft() {

        //way for ticked file:
        var selected = get_select_option();


        //way for currently opened file:
        var thisName = await sendDataToServer(collectData(false)); //true if final, false if draft
        const mylang = new URLSearchParams(window.location.search).get('lang');
        let url = `/?survey=${thisName}&draft=1&tabclose=1`;
        if (mylang) {
            url += `&lang=${mylang}`;
        }
        window.open(url, '_blank');
    }

    function button_final() {
        var selected = get_select_option();
        alert("Finalisieren von " +selected);
    }

    function button_delete() {
        var selected = get_select_option();
        alert("Lösche "+selected);
    }

    function button_close() {
        document.getElementById("button_save").setAttribute("disabled", true);
        document.getElementById("button_close").setAttribute("disabled", true);
        toggleFileMenu();
        uncheckRadios();
        undoAll();
        clearTexts();
        resetSelects();
        mySurveys.style.display = "block";
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

    async function undoAll() {
        while (undoStack.length > 0) { // loop until undo stack is empty
            const command = undoStack.pop(); // pop the last command
            command.unexecute(); // unexecute it
            redoStack.push(command); // push it to redo stack
        }
        emailDomainInput.style.display = "none";
        emailDomainLabel.style.display = "none";
        questionCount = 0;
        document.getElementById("button_undo").setAttribute("disabled", true);
        await clearRedoStack();
        await preventUserLeave();
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

    function adjustButtonStyles() {
        const buttons = document.querySelectorAll(".creator-buttons button");
        const presetButtons = document.querySelectorAll("#preset-buttons button");
        const paddingMin = 0.0;
        const paddingMax = 1.750;
        const letterSpacingMin = 0.0;
        const letterSpacingMax = 0.3;

        let maxCharCount = 0;

        buttons.forEach((button) => {
            const charCount = button.textContent.length;
            if (charCount > maxCharCount) {
                maxCharCount = charCount;
            }
        });

        const paddingScale = (maxCharCount - 10) / 20;
        const letterSpacingScale = (30 - maxCharCount) / 20;

        const paddingLeftRight = paddingMax - paddingScale * (paddingMax - paddingMin);
        const letterSpacing = letterSpacingMin + letterSpacingScale * (letterSpacingMax - letterSpacingMin);

        buttons.forEach((button) => {
            button.style.paddingLeft = paddingLeftRight + "em";
            button.style.paddingRight = paddingLeftRight + "em";
            button.style.letterSpacing = letterSpacing + "em";
        });

        presetButtons.forEach((button) => {
            button.style.paddingLeft = paddingLeftRight + "em";
            button.style.paddingRight = paddingLeftRight + "em";
            button.style.letterSpacing = letterSpacing + "em";
        });
    }
    adjustButtonStyles();
</script>

