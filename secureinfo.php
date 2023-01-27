<section id="intro">
    <header>
<?php echo translate('
        <h1>Infos zur Übertragung personenbezogener Daten</h1>
        
        <h2>Überschrift 2</h2>
        <p>Hier steht bald etwas zu dem Thema.</p>
        
        <h2>Weiterführende Infos</h2>
        <p><a href="?content=passwordinfo">Abgleich und (Un-)Sichtbarkeit von Passwörtern</a></p>
        <p><a href="?content=mailinfo">Mailnutzung bei Umfragen</a></p>
        
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