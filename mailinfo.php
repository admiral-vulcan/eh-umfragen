<section id="intro">
    <header>
<?php echo translate('
        <h1>Infos zur Angabe der E-Mail-Adresse bei Umfragen</h1>

        <h2>Warum ist das notwendig?</h2>
        <p>Die jeweiligen Umfragen legen u.a. ihre Zielgruppe fest. Je nachdem, ob sie nur von Studierenden, (auch/nur) von Lehrbeauftragten und Mitarbeitenden der EH oder von allen Menschen ausgefüllt werden dürfen, wird über die E-Mail-Adresse die Einhaltung der Zielgruppe gesichert. Damit können wir Manipulationen der Statistik von außerhalb der Zielgruppe ausschließen.
            <br>Außerdem können wir darüber feststellen, ob jemand bereits an einer Umfrage teilgenommen hat und damit auch Manipulationen von innen ausschließen.</p>

        <h2>Die Anonymität bleibt gewahrt</h2>
        <p>Wir speichern keine E-Mail-Adressen und generell keine personenbezogenen Daten auf unserem Server. Zum Abgleich speichern wir einen sog. Hash-Wert der E-Mail-Adresse.</p>
        
        <h2>Funktionsweise</h2>
        <p>Zunächst überprüft unser Server, ob die E-Mail-Adresse gültig ist. <br>Anschließend wird ein Hash errechnet, mit dem künftige Abgabeversuche abgeglichen werden können. Da Quersummen ähnlich funktionieren, lässt es sich damit leichter erklären:
            <br>Sagen wir, die E-Mail-Adresse lautet: 123456789. Die Quersumme aus 123456789 ist 45. Wir speichern nur die 45. Aus der 45 lässt sich 123456789 nicht wieder zurückrechnen, aber wenn (versehentlich) versucht wird, ein zweites Mal an derselben Umfrage teilzunehmen, wird die Quersumme wieder 45 sein und die Abgabe wird nicht funktionieren.<br>Echte Hash-Werte müssen natürlich länger und sicherer sein, auch damit nicht zwei E-Mail-Adressen den gleichen Wert haben. So sieht z.B. ein echter Hash-Wert aus: 41d772015c6b319162fb50e3e918a686
            <br>Das Verfahren, das wir nutzen, nennt sich MD5. Früher wurde dieser auch zur Passwortspeicherung genutzt. Ein Hauptkritikpunkt war, dass man bei MD5 rückwärtssuchen kann. Man muss also nur viele E-Mail-Adressen haben, um, im obigen Beispiel, irgendwann die 45 zu errechnen, um die Adresse zuordnen zu können. Deshalb verschlüsseln wir die Adresse zuerst, bevor wir MD5 anwenden. <br>Keine Sorge, für den <a href="?content=passwordinfo">Passwortableich</a> im Creator-Bereich verwenden wir natürlich ein anderes Verfahren.</p>

        <h2>Weiterführende Infos</h2>
        <p><a href="?content=secureinfo">Übertragung personenbezogener Daten</a></p>
        <p><a href="?content=passwordinfo">Ableich und (Un-)Sichtbarkeit von Passwörtern</a></p>
        
        <h2>Andere Info-Themen</h2>
        <p><a href="?content=googleinfo">Nutzung von „Über Google anmelden“</a></p>
        ', 'de', $GLOBALS['lang']); ?>
    </header>
</section>
<section>
    <div  style="width: 100%; position: fixed; left: 0; bottom: 0; background: var(--generic-body-back); opacity: .8;">
        <ul class="actions stacked">
            <li><a href="JavaScript:window.close()" class="button large fit"><?php echo translate('Tab schließen', 'de', $GLOBALS['lang']); ?></a></li>
        </ul></div>
</section>