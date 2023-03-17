<?php

if (isset($_GET["code"])) {
    $err_code = $_GET["code"];
}
else $err_code = "¯\_(ツ)_/¯";

switch ($err_code) {
    case 301:
        $err_head = translate('Moved Permanently', 'en', $GLOBALS['lang']);
        $err_text = translate('The requested resource has been moved permanently to a new URL.', 'en', $GLOBALS['lang']);
        break;
    case 400:
        $err_head = translate('Bad Request', 'en', $GLOBALS['lang']);
        $err_text = translate('The server cannot or will not process the request due to an apparent client error.', 'en', $GLOBALS['lang']);
        break;
    case 401:
        $err_head = translate('Unauthorized', 'en', $GLOBALS['lang']);
        $err_text = translate('The requested resource requires authentication.', 'en', $GLOBALS['lang']);
        break;
    case 402:
        $err_head = translate('Payment Required', 'en', $GLOBALS['lang']);
        $err_text = translate('The requested resource requires payment.', 'en', $GLOBALS['lang']);
        break;
    case 403:
        $err_head = translate('Forbidden', 'en', $GLOBALS['lang']);
        $err_text = translate('The server understood the request, but is refusing to fulfill it.', 'en', $GLOBALS['lang']);
        break;
    case 404:
        $err_head = translate('Page Not Found', 'en', $GLOBALS['lang']);
        $err_text = translate('The requested resource could not be found on this server.', 'en', $GLOBALS['lang']);
        break;
    case 405:
        $err_head = translate('Method Not Allowed', 'en', $GLOBALS['lang']);
        $err_text = translate('The requested resource does not support the specified HTTP method.', 'en', $GLOBALS['lang']);
        break;
    case 406:
        $err_head = translate('Not Acceptable', 'en', $GLOBALS['lang']);
        $err_text = translate('The server cannot produce a response matching the list of acceptable values defined in the request headers.', 'en', $GLOBALS['lang']);
        break;
    case 408:
        $err_head = translate('Request Timeout', 'en', $GLOBALS['lang']);
        $err_text = translate('The server timed out waiting for the request.', 'en', $GLOBALS['lang']);
        break;
    case 410:
        $err_head = translate('Gone', 'en', $GLOBALS['lang']);
        $err_text = translate('The requested resource is no longer available and has been permanently removed from the server.', 'en', $GLOBALS['lang']);
        break;
    case 413:
        $err_head = translate('Payload Too Large', 'en', $GLOBALS['lang']);
        $err_text = translate('The server cannot process the request because the request payload is too large.', 'en', $GLOBALS['lang']);
        break;
    case 414:
        $err_head = translate('URI Too Long', 'en', $GLOBALS['lang']);
        $err_text = translate('The server cannot process the request because the requested URI is too long.', 'en', $GLOBALS['lang']);
        break;
    case 415:
        $err_head = translate('Unsupported Media Type', 'en', $GLOBALS['lang']);
        $err_text = translate('The server cannot process the request because the media type of the requested data is not supported.', 'en', $GLOBALS['lang']);
        break;
    case 418:
        $err_head = translate('I\'m a teapot', 'en', $GLOBALS['lang']);
        $err_text = translate('You\'re trying to make tea with a coffee machine!', 'en', $GLOBALS['lang']);
        break;
    case 429:
        $err_head = translate('Too Many Requests', 'en', $GLOBALS['lang']);
        $err_text = translate('The user has sent too many requests in a given amount of time.', 'en', $GLOBALS['lang']);
        break;
    case 500:
        $err_head = translate('Internal Server Error', 'en', $GLOBALS['lang']);
        $err_text = translate('The server encountered an unexpected condition that prevented it from fulfilling the request.', 'en', $GLOBALS['lang']);
        break;
    case 501:
        $err_head = translate('Not Implemented', 'en', $GLOBALS['lang']);
        $err_text = translate('The server does not support the functionality required to fulfill the request.', 'en', $GLOBALS['lang']);
        break;
    case 502:
        $err_head = translate('Bad Gateway', 'en', $GLOBALS['lang']);
        $err_text = translate('The server received an invalid response from an upstream server while attempting to fulfill the request.', 'en', $GLOBALS['lang']);
        break;
    case 503:
        $err_head = translate('Service Unavailable', 'en', $GLOBALS['lang']);
        $err_text = translate('The server is currently unable to handle the request due to a temporary overload or maintenance of the server.', 'en', $GLOBALS['lang']);
        break;
    case 504:
        $err_head = translate('Gateway Timeout', 'en', $GLOBALS['lang']);
        $err_text = translate('The server received an invalid response from an upstream server while attempting to fulfill the request.', 'en', $GLOBALS['lang']);
        break;
    default:
        $err_head = translate('Unknown Error', 'en', $GLOBALS['lang']);
        $err_text = translate('We do not know exactly what happened.', 'en', $GLOBALS['lang']);
}
$err_text .= " " . translate('Please go back a step or to our home page and contact us if the error persists.', 'en', $GLOBALS['lang']);;

?>
<section id="intro">
    <header>
        <h1><?php echo $err_code; ?> - eh-umfragen.de</h1>
        <h2><?php echo $err_head; ?></h2>
        <p><?php echo $err_text; ?></p>
    </header>
</section>
<canvas id="logo_err_ani"></canvas>
<br><br><br>
<section>
    <div  style="width: 100%; position: fixed; left: 0; bottom: 0; background: var(--generic-body-back); opacity: .8;">
        <ul class="actions stacked">
            <li><a href="JavaScript:history.back()" class="button large fit"><?php echo translate('Zurück', 'de', $GLOBALS['lang']); ?></a></li>
        </ul></div>
</section>
<script type="application/javascript" src="/images/logo_err_ani.js"></script>