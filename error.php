<?php

if (isset($_GET["code"])) {
    $err_code = $_GET["code"];
}
else $err_code = "¯\_(ツ)_/¯";
switch ($err_code) {
    case 301:
        $err_head = translate('Permanent verschoben', 'de', $GLOBALS['lang']);
        $err_text = translate('Die angeforderte Ressource wurde dauerhaft zu einer neuen URL verschoben.', 'de', $GLOBALS['lang']);
        break;
    case 400:
        $err_head = translate('Fehlerhafte Anfrage', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server kann die Anfrage aufgrund eines offensichtlichen Client-Fehlers nicht oder nicht verarbeiten.', 'de', $GLOBALS['lang']);
        break;
    case 401:
        $err_head = translate('Unbefugter Zugriff', 'de', $GLOBALS['lang']);
        $err_text = translate('Die angeforderte Ressource erfordert eine Authentifizierung.', 'de', $GLOBALS['lang']);
        break;
    case 402:
        $err_head = translate('Zahlung erforderlich', 'de', $GLOBALS['lang']);
        $err_text = translate('Die angeforderte Ressource erfordert eine Zahlung.', 'de', $GLOBALS['lang']);
        break;
    case 403:
        $err_head = translate('Verboten', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server hat die Anfrage verstanden, aber es wird verweigert, sie zu erfüllen.', 'de', $GLOBALS['lang']);
        break;
    case 404:
        $err_head = translate('Seite nicht gefunden', 'de', $GLOBALS['lang']);
        $err_text = translate('Die angeforderte Ressource konnte auf diesem Server nicht gefunden werden.', 'de', $GLOBALS['lang']);
        break;
    case 405:
        $err_head = translate('Methode nicht erlaubt', 'de', $GLOBALS['lang']);
        $err_text = translate('Die angeforderte Ressource unterstützt die angegebene HTTP-Methode nicht.', 'de', $GLOBALS['lang']);
        break;
    case 406:
        $err_head = translate('Nicht akzeptabel', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server kann keine Antwort erstellen, die der Liste der im Anforderungsheader definierten akzeptablen Werte entspricht.', 'de', $GLOBALS['lang']);
        break;
    case 408:
        $err_head = translate('Zeitüberschreitung der Anfrage', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server hat die Anfrage abgebrochen, weil er zu lange auf eine Antwort gewartet hat.', 'de', $GLOBALS['lang']);
        break;
    case 410:
        $err_head = translate('Verschwunden', 'de', $GLOBALS['lang']);
        $err_text = translate('Die angeforderte Ressource ist nicht mehr verfügbar und wurde dauerhaft vom Server entfernt.', 'de', $GLOBALS['lang']);
        break;
    case 413:
        $err_head = translate('Payload zu groß', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server kann die Anfrage nicht verarbeiten, da der Anforderungspayload zu groß ist.', 'de', $GLOBALS['lang']);
        break;
    case 414:
        $err_head = translate('URI zu lang', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server kann die Anfrage nicht verarbeiten, da die URI (Uniform Resource Identifier) zu lang ist.', 'de', $GLOBALS['lang']);
        break;
    case 415:
        $err_head = translate('Nicht unterstützter Medientyp', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server kann die Anfrage nicht verarbeiten, da der angeforderte Medientyp nicht unterstützt wird.', 'de', $GLOBALS['lang']);
        break;
    case 418:
        $err_head = translate('Ich bin eine Teekanne', 'de', $GLOBALS['lang']);
        $err_text = translate('Du versuchst, Tee mit einer Kaffeemaschine zu kochen!', 'de', $GLOBALS['lang']);
        break;
    case 429:
        $err_head = translate('Zu viele Anfragen', 'de', $GLOBALS['lang']);
        $err_text = translate('Du hast innerhalb einer bestimmten Zeitspanne zu viele Anfragen gesendet.', 'de', $GLOBALS['lang']);
        break;
    case 500:
        $err_head = translate('Interner Serverfehler', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server hat einen unerwarteten Zustand festgestellt und kann die Anforderung nicht erfüllen.', 'de', $GLOBALS['lang']);
        break;
    case 501:
        $err_head = translate('Nicht implementiert', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server versteht die Anfrage nicht oder unterstützt die angeforderte Funktionalität nicht.', 'de', $GLOBALS['lang']);
        break;
    case 502:
        $err_head = translate('Fehlerhafte Gateway', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server fungiert als Gateway oder Proxy und hat von einem anderen Server eine ungültige Antwort erhalten.', 'de', $GLOBALS['lang']);
        break;
    case 503:
        $err_head = translate('Dienst nicht verfügbar', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server ist vorübergehend nicht verfügbar (z. B. aufgrund von Wartungsarbeiten oder Überlastung).', 'de', $GLOBALS['lang']);
        break;
    case 504:
        $err_head = translate('Zeitüberschreitung des Gateways', 'de', $GLOBALS['lang']);
        $err_text = translate('Der Server fungiert als Gateway oder Proxy und hat von einem anderen Server keine rechtzeitige Antwort erhalten.', 'de', $GLOBALS['lang']);
        break;
    default:
        $err_head = translate('Unbekannter Fehler', 'de', $GLOBALS['lang']);
        $err_text = translate('Es ist ein unbekannter Fehler aufgetreten.', 'de', $GLOBALS['lang']);
        break;
}
$err_text .= " " . translate('Bitte gehe einen Schritt zurück oder zu unserer Homepage und kontaktiere uns, wenn der Fehler weiterhin besteht.', 'de', $GLOBALS['lang']);;

?>
<section id="intro">
    <header>
        <h1><?php echo $err_code; ?> - eh-umfragen.de</h1>
        <h2><?php echo $err_head; ?></h2>
        <p><?php echo $err_text; ?></p>
    </header>
</section>
<canvas id="logo_err_ani" aria-label="<?php echo translate('Ein Klemmbrett als Logo mit drei drehenden Zahnrädern in der Mitte und dem Wort Fehler darüber', 'de', $GLOBALS['lang']); ?>"></canvas>
<br><br><br>
<section>
    <div  style="width: 100%; position: fixed; left: 0; bottom: 0; background: var(--generic-body-back); opacity: .8;">
        <ul class="actions stacked">
            <li><a href="JavaScript:history.back()" class="button large fit"><?php echo translate('Zurück', 'de', $GLOBALS['lang']); ?></a></li>
        </ul></div>
</section>
<script type="application/javascript" src="/images/logo_err_ani.js"></script>