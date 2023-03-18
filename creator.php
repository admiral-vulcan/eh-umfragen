<h2><?php echo translate("Mein Creator", "de", $GLOBALS["lang"]); ?></h2>
<p><?php echo translate("Hier kannst Du Deine Umfragen erstellen, einsehen und auswerten.", "de", $GLOBALS["lang"]); ?></p>
<p><?php echo translate("Dieser Bereich ist im Entstehen und funktioniert noch nicht (richtig).", "de", $GLOBALS["lang"]); ?></p>

<div>
    <button onclick="button_new();"><?php echo translate("Neu", "de", $GLOBALS["lang"]); ?></button>
    <button onclick="button_open();"><?php echo translate("Öffnen", "de", $GLOBALS["lang"]); ?></button>
    <button onclick="button_save();"><?php echo translate("Speichern", "de", $GLOBALS["lang"]); ?></button>
    <button onclick="button_evaluate();"><?php echo translate("Auswerten", "de", $GLOBALS["lang"]); ?></button>
    <button onclick="button_delete();"><?php echo translate("Löschen", "de", $GLOBALS["lang"]); ?></button>
    <button onclick="button_close();"><?php echo translate("Schließen", "de", $GLOBALS["lang"]); ?></button>
</div>

<br>

<div>
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
    echo "<td><input type='radio' name='Umfrage' id='" . $value . "'><label style='top: -3px;' for='" . $value . "'></label></td>";
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
    echo "<td><input type='radio' name='Umfrage' id='" . $value . "'><label style='top: -3px;' for='" . $value . "'></label></td>";
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
</form>
</div>

<p>
    Creator TODO:
    <br>    Zu meinem Profil - Link
    <br>    Meine Umfragen (anzeigen)
    <br>    Umfragen erstellen
    <br>    Umfragen auswerten
    <br>    Umfragen löschen (wäre ja schade) 🤷
</p>
<script type="application/javascript">
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
        alert("Neue Umfrage");
    }

    function button_open() {
        var selected = get_select_option();
        alert("Öffne "+selected);
    }

    function button_save() {
        alert("Speichere");
    }

    function button_evaluate() {
        var selected = get_select_option();
        alert("Werte "+selected+" aus");
    }

    function button_delete() {
        var selected = get_select_option();
        alert("Lösche "+selected);
    }

    function button_close() {
        alert("Schließe");
    }
</script>