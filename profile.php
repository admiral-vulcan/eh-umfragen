<h2>Mein Profil</h2>
<p>Du bist eingeloggt und im PROFIL-Bereich angekommen. Herzlichen Glückwunsch!</p>
<p>Dieser Bereich ist im Entstehen und funktioniert noch nicht (richtig).</p>
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
    <br>    Wir haben folgende Infos in der Datenbank:
</p>
<?php
echo "<table>";
echo "<tr>";
echo "<th>Key</th>";
echo "<th>Value</th>";
echo "</tr>";

foreach ($_SESSION as $key => $value) {
    echo "<tr>";
    echo "<td>" . $key . "</td>";
    echo "<td>" . $value . "</td>";
    echo "</tr>";
}
echo "</table>";
?>