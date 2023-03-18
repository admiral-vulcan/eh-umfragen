<h2><?php echo translate("Mein Profil", "de", $GLOBALS["lang"]); ?></h2>
<p><?php echo translate("Hier kannst Du Deine Profildaten und -einstellungen einsehen und ändern.", "de", $GLOBALS["lang"]); ?></p>
<p><?php echo translate("Dieser Bereich ist im Entstehen und funktioniert noch nicht (richtig).", "de", $GLOBALS["lang"]); ?></p>
<p>
Profil TODO:
    <br>    Zu meinen Umfragen - Link
    <br>    Meine Daten (anzeigen)
    <br>    Meine Umfragen (Übersicht anzeigen)
    <br>    Bild hochladen/ändern/löschen
    <br>    Mit Google verknüpfen/Verknüpfung löschen
    <br>    Passwort ändern/setzen
    <br>    Konto löschen - DSGVO-konform 🤷‍
    <br>
    <br>    Wir haben folgende Infos über Dich in unserer Datenbank:
</p>
<?php
echo "<table>";
echo "<tr>";
echo "<th>" . translate("Schlüssel", "de", $GLOBALS["lang"]) . "</th>";
echo "<th>" . translate("Wert", "de", $GLOBALS["lang"]) . "</th>";
echo "</tr>";

foreach ($_SESSION as $key => $value) {
    echo "<tr>";
    echo "<td>" . $key . "</td>";
    echo "<td>" . $value . "</td>";
    echo "</tr>";
}
echo "</table>";
?>