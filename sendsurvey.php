<section id="intro">
    <header>
        <h2>EH-Umfragen</h2>

        <?php
        if (!isset($ver_float)) $ver_float = 0;
        if (!isset($this_uri)) $this_uri = "";
        $tmpkey = 0;
        $countkey = 0;
        $answers[][] = 0;
        //print_r($_POST);

        foreach ($_POST as $key => $value) {
            //echo $value . "<br>";
            if (intval(substr($key, 0, 1)) > 0) {
                if (str_contains($key, "x")) {
                    if ($tmpkey == substr($key, 0, strpos($key, "x"))) $countkey++;
                    else {
                        $countkey = 0;
                        $tmpkey = substr($key, 0, strpos($key, "x"));
                    }
                    $answers[substr($key, 0, strpos($key, "x")) - 2][$countkey] = $value;
                }
                else {
                    $answers[$key - 2][0] = $value;
                }
            }
        }

        /*
        echo "<br>";
        for ($i = 0; $i < sizeof($answers); $i++) {
            for ($j = 0; $j < sizeof($answers[$i]); $j++) {
                echo $i . ":" . $j . ":" . $answers[$i][$j] . "<br>";
            }
        }
        */

        //check target:
        $target = $_POST["target"];
        $mailneedle = ["@", "@"];
        if ($target == "studs") $mailneedle = ["@studnet.eh-ludwigsburg.de", "@studnet.eh-ludwigsburg.de"];
        elseif ($target == "empl") $mailneedle = ["@lehrbeauftragte.eh-ludwigsburg.de", "@eh-ludwigsburg.de"];
        elseif ($target == "both") $mailneedle = ["eh-ludwigsburg.de", "eh-ludwigsburg.de"];
        //elseif ($target == "all") $mailneedle = ["@", "@"];

        if ($target == "studs") {
            $nice = ["Du bist toll!", "Du bist großartig!", "Du hast uns eine große Freude gemacht!", "Du bist ein wunderbarer Mensch!"];
            $share = "Wir freuen uns auch sehr, wenn Du unsere Umfrage teilst. Hier ist der passende Link:<br><center><a href='"."$this_uri"."'>"."$this_uri"."</center><br></a>";
            $contactus = "Hast Du ein Anliegen oder eine Idee für eine Umfrage? Dann schreib uns:<br><center><a href='mailto:kontakt@eh-umfrage.de'>kontakt@eh-umfrage.de</center><br></a>";
            $othersurveys = "Schau Dir auch gerne unsere anderen Umfragen und schon fertigen Auswertungen an. <br>Diese findest Du auf der";
            $beCreator = "<br><center><b>Werde Creator und schließe Dich uns an!<br><br>In unserem einzigartigen Baukastensystem kannst Du spielend leicht selbst Umfragen erstellen und hinterher automatisiert auswerten lassen.<br><br><a href='/?creator=challenge'>Schau gleich mal im Creator-Bereich vorbei.</a></center></b><br>";
        }
        else {
            $nice = ["Sie sind toll!", "Sie sind großartig!", "Sie haben uns eine große Freude gemacht!", "Sie sind ein wunderbarer Mensch!"];
            $share = "Wir würden uns auch sehr freuen, wenn Sie unsere Umfrage teilen würden. Hier ist der passende Link:<br><center><a href='"."$this_uri"."'>"."$this_uri"."</center><br></a>";
            $contactus = "Haben Sie ein Anliegen oder wollen uns einer Studierendengruppe als Plattform empfehlen? Dann schreiben Sie uns:<br><center><a href='mailto:kontakt@eh-umfrage.de'>kontakt@eh-umfrage.de</center><br></a>";
            $othersurveys = "Schauen Sie sich auch gerne unsere anderen Umfragen und schon fertigen Auswertungen an. <br>Diese finden Sie auf der";
            $beCreator = "";
        }

        //Hier nur Studierende
        if ($target == "studs") {
            if (str_contains(strtolower($_POST["email"]), $mailneedle[0]) or str_contains(strtolower($_POST["email"]), $mailneedle[1])) {
                //echo "Mail address seems valid.<br>";
                $email = strtolower($_POST["email"]);
                $mailhash = md5(String2Hex($email));
                $uid = get_email_id($mailhash);
                if ($uid < 1) $uid = create_user($email, $mailhash, $target); //TODO check if already answered
                if (fill_survey($_POST["sid"], $uid, $answers) === 0) { //answers is two-dim-array [answer-nums][answers-per-num]

                    if (mail_is_validated($uid) === 0) {
                        sendconfirmation($uid, $email, $target);
                        echo "<h3>Vielen Dank!</h3>";
                        echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :) <br>Deine E-Mail-Adresse ist noch nicht validiert. Ein Link dazu wird Dir zugesandt. Erfahre <a href='https://www.eh-umfragen.de?content=mailinfo' target='_blank'>hier</a>, warum wir das brauchen und wie das funktioniert.</p>";
                        echo "<p>".$share."</p>";
                        if ($ver_float > 0.99) echo "<p>".$beCreator."</p>";
                        echo "<p>".$contactus."</p>";
                        echo "<p>".$othersurveys."</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    } else {
                        echo "<h3>Vielen Dank!</h3>";
                        echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :)</p>";
                        echo "<p>".$share."</p>";
                        if ($ver_float > 0.99) echo "<p>".$beCreator."</p>";
                        echo "<p>".$contactus."</p>";
                        echo "<p>".$othersurveys."</p>";
                        echo "<a href='/' class='button large fit'>Startseite</a>";
                    }
                } else { //user has already submitted
                    if (!mail_is_validated($uid)) {
                        sendconfirmation($uid, $email, $target);
                        echo "<h3>Du hast bereits teilgenommen.</h3>";
                        echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Deine ursprüngliche Abgabe wird allerdings erst gewertet, wenn Du Deine E-Mail-Adresse validierst. Ein Link dazu wird Dir zugesandt. Erfahre <a href='https://www.eh-umfragen.de/mailinfo' target='_blank'>hier</a>, warum wir das brauchen und wie das funktioniert.</p>";
                        echo "<p>".$share."</p>";
                        if ($ver_float > 0.99) echo "<p>".$beCreator."</p>";
                        echo "<p>".$contactus."</p>";
                        echo "<p>".$othersurveys."</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    } else {
                        echo "<h3>Du hast bereits teilgenommen.</h3>";
                        echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Deine ursprüngliche Abgabe wird allerdings gewertet. Danke dafür!</p>";
                        echo "<p>".$share."</p>";
                        if ($ver_float > 0.99) echo "<p>".$beCreator."</p>";
                        echo "<p>".$contactus."</p>";
                        echo "<p>".$othersurveys."</p>";
                        echo "<a href='/' class='button large fit'>Startseite</a>";

                    }
                }
            } else {
                echo "<h3>Die E-Mail-Adresse scheint keine Studnet-Adresse der EH zu sein.</h3>";
                echo "<p>Nur Studierende der EH gehören zur Zielgruppe dieser Umfrage. Deshalb überprüfen wir den Studierendenstatus über die Studnet-Adresse und eine kleine Validierungsmail. Keine Sorge, Du kannst noch zurück und die E-Mail-Adresse ändern.</p>";
                echo "<a href='javascript:history.back()' class='button large fit'>Zurück zur Umfrage</a>";
            }
        }

        //hier alle anderen
        else {
            if (str_contains(strtolower($_POST["email"]), $mailneedle[0]) or str_contains(strtolower($_POST["email"]), $mailneedle[1])) {
                //echo "Mail address seems valid.<br>";
                $email = strtolower($_POST["email"]);
                $mailhash = md5(String2Hex($email));
                $uid = get_email_id($mailhash);
                if ($uid < 1) $uid = create_user($mailhash, $target); //TODO check if already answered
                if (fill_survey($_POST["sid"], $uid, $answers) === 0) { //answers is two-dim-array [answer-nums][answers-per-num]

                    if (mail_is_validated($uid) === 0) {
                        sendconfirmation($uid, $email, $target);
                        echo "<h3>Vielen Dank!</h3>";
                        echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :) <br>Ihre E-Mail-Adresse ist noch nicht validiert. Ein Link dazu wird Ihnen zugesandt. Erfahren Sie <a href='https://www.eh-umfragen.de/mailinfo' target='_blank'>hier</a>, warum wir das brauchen und wie das funktioniert.<br>Schauen Sie bitte auch in Ihrem Spam-Ordner nach, falls Sie unsere Mail nicht gleich finden.</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    } else {
                        echo "<h3>Vielen Dank!</h3>";
                        echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :)</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    }
                } else { //user has already submitted
                    if (!mail_is_validated($uid)) {
                        sendconfirmation($uid, $email, $target);
                        echo "<h3>Sie haben bereits teilgenommen.</h3>";
                        echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Ihre ursprüngliche Abgabe wird allerdings erst gewertet, wenn Sie Ihre E-Mail-Adresse validiert haben. Ein Link dazu wird Ihnen zugesandt. Erfahren Sie <a href='https://www.eh-umfragen.de/mailinfo' target='_blank'>hier</a>, warum wir das brauchen und wie das funktioniert.</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    } else {
                        echo "<h3>Sie haben bereits teilgenommen.</h3>";
                        echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Ihre ursprüngliche Abgabe wird allerdings gewertet. Danke dafür!</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";

                    }
                }
            } else {
                //hier nur Mitarbeitende
                if ($target == "empl") {
                    echo "<h3>Die E-Mail-Adresse scheint keine Adresse der EH zu sein.</h3>";
                    echo "<p>Nur Mitarbeitende der EH gehören zur Zielgruppe dieser Umfrage. Deshalb überprüfen wir den Aufbau der E-Mail-Adresse und versenden eine kleine Validierungsmail. Keine Sorge, Sie können noch zurück und die E-Mail-Adresse ändern.</p>";
                    echo "<a href='javascript:history.back()' class='button large fit'>Zurück zur Umfrage</a>";
                }
                //hier Studierende und Mitarbeitende
                elseif ($target == "both") {
                    echo "<h3>Die E-Mail-Adresse scheint keine Adresse der EH zu sein.</h3>";
                    echo "<p>Nur Studierende und Mitarbeitende der EH gehören zur Zielgruppe dieser Umfrage. Deshalb überprüfen wir den Aufbau der E-Mail-Adresse und versenden eine kleine Validierungsmail. Keine Sorge, Sie können noch zurück und die E-Mail-Adresse ändern.</p>";
                    echo "<a href='javascript:history.back()' class='button large fit'>Zurück zur Umfrage</a>";
                }
                //hier alle
                else {
                    echo "<h3>Die E-Mail-Adresse scheint nicht valide zu sein.</h3>";
                    echo "<p>Bitte geben Sie eine korrekte E-Mail-Adresse ein. Keine Sorge, Sie können noch zurück und die E-Mail-Adresse ändern.</p>";
                    echo "<a href='javascript:history.back()' class='button large fit'>Zurück zur Umfrage</a>";
                }
            }
            echo "Schauen Sie bald wieder vorbei, wenn wir nicht nur neue Fragen, sondern auch Auswertungen Ihrer aktuellen Antworten haben!</p>";
        }







        /*
        echo password_hash($_POST["email"], PASSWORD_ARGON2ID) . "<br>";
        echo $_POST["sid"] . "<br>";
        echo time();
*/
        ?>

    </header>
</section>
