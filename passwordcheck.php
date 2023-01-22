<?PHP
require_once('translate.php');
//echo check_password_is_bad("aA123321Aa");

function check_password_is_bad($pw)
{
    $password_warning = 'Das Passwort muss mindestens acht Zeichen lang sein und mindestens drei der folgenden vier Regeln erfüllen: Mindestens ein Kleinbuchstabe, mindestens ein Großbuchstabe, mindestens eine Ziffer und mindestens ein Sonderzeichen.';
    $pwd_strengh = 0;

    if (preg_match("#[0-9]+#", $pw)) {
        $pwd_strengh++;
    }

    if (preg_match("#[a-z]+#", $pw)) {
        $pwd_strengh++;
    }

    if (preg_match("#[A-Z]+#", $pw)) {
        $pwd_strengh++;
    }

    if (preg_match("#\W+#", $pw)) {
        $pwd_strengh++;
    }

    if (strlen($pw) < 8) {
        $pwd_strengh--;
    }

    if ($pwd_strengh < 3) return $password_warning;
    else return password_is_vulnerable($pw);
}

function password_is_vulnerable($pw, $score = FALSE)
{
    $lang = "de";
    $password_warning_sec = 'Das Passwort erfüllt die Qualitätsrichtlinien nicht, ';
    $CRACKLIB = "/usr/sbin/cracklib-check";
    $PWSCORE = "/usr/bin/pwscore";

    // prevent UTF-8 characters being stripped by escapeshellarg

    $out = [];
    $ret = NULL;
    $command = "echo " . escapeshellarg($pw) . " | {$CRACKLIB}";
    exec($command, $out, $ret);
    if (($ret == 0) && preg_match("/: ([^:]+)$/", $out[0], $regs)) {
        list(, $msg) = $regs;
        switch ($msg) {
            case "OK":
                if ($score) {
                    $command = "echo " . escapeshellarg($pw) . " | {$PWSCORE}";
                    exec($command, $out, $ret);
                    if (($ret == 0) && is_numeric($out[1])) {
                        return (int)$out[1]; // return score
                    } else {
                        return FALSE; // probably OK, but may be too short, or a palindrome
                    }
                } else {
                    return FALSE; // OK
                }
                break;

            default:
                if (strcasecmp($lang, 'en') !== 0 || strcasecmp($lang, '') !== 0) $msg = str_replace("sie ist", "es ist", translate($msg, "en", $lang));
                if (substr($msg != ".", -1)) $msg = $msg . ".";
                return $password_warning_sec . $msg; // not OK - return cracklib message //deepL free: 500.000 chars per month free. CHECK!

        }
    }

    return FALSE; // possibly OK
}

if (isset($_GET["passToCheck"])) {
    $analysis = check_password_is_bad(htmlspecialchars($_GET["passToCheck"]));
    if ($analysis == '') echo "Glückwunsch, das Passwort erfüllt die Qualitätsrichtlinien.";
    else echo $analysis;
}

?>