<section id="intro">
    <header>
        <h2>EH-Umfragen</h2>
        <?php

        use EHUmfragen\DatabaseModels\Users;
        use EHUmfragen\DatabaseModels\Responses;

        $users = new Users();

        require_once ("gitignore/code.php");
        require_once ("translate.php");

        if (!isset($ver_float)) $ver_float = 0;
        if (!isset($this_uri)) $this_uri = "";
        $tmpkey = 0;
        $countkey = 0;
        $answers[][] = 0;
        $sent = "";


        function fill_survey($user_id) {
            $responses = new Responses();
            $survey_id = $_POST["survey_id"];
            if ($user_id > 2 && $responses->hasUserSubmittedResponse($survey_id, $user_id)) return -1;
            else {
                foreach ($_POST as $key => $value) {
                    if (intval($key) > 0) { //POST contains other data, only question_id are int
                        if (intval($value) > 0) //non-free_text (int) vs free_text (string)
                            $responses->addResponse(intval($survey_id), intval($key), intval($value), "", $user_id);
                        else
                            $responses->addResponse(intval($survey_id), intval($key), 0, translate($value, $GLOBALS["lang"], 'de'), $user_id);
                    }
                }
                return 0;
            }
        }
        //check target:
        $target = $_POST["target"];
        $survey_id = $_POST["survey_id"];
        $mailneedle = ["@", "@"];
        if ($target == "ehlb_students") $mailneedle = ["@studnet.eh-ludwigsburg.de", "@studnet.eh-ludwigsburg.de"];
        elseif ($target == "ehlb_lecturers") $mailneedle = ["@lehrbeauftragte.eh-ludwigsburg.de", "@eh-ludwigsburg.de"];
        elseif ($target == "ehlb_all") $mailneedle = ["eh-ludwigsburg.de", "eh-ludwigsburg.de"];
        elseif (str_contains($target, "@")) $mailneedle = [$target, $target];

        if ($target == "ehlb_students") {
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
        if ($target == "ehlb_students") {
            if (str_contains(strtolower($_POST["email"]), $mailneedle[0]) or str_contains(strtolower($_POST["email"]), $mailneedle[1])) {
                //echo "Mail address seems valid.<br>";
                $email = strtolower($_POST["email"]);
                $mailhash = md5(String2Hex($email));
                $uid = $users->getUserIdByMailHash($mailhash);
                if ($uid < 1) $uid = $users->addUser($target, $mailhash); //TODO check if already answered
                echo $uid;
                if (fill_survey($uid) === 0) {

                    if (!$users->getUserValidation($uid)) {
                        $sent = sendconfirmation($uid, $email, $target);
                        echo "<h3>Vielen Dank!</h3>";
                        echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :) <br>Deine E-Mail-Adresse ist noch nicht validiert. Ein Link dazu wird Dir zugesandt. Erfahre <a href='https://www.eh-umfragen.de?content=mailinfo' target='_blank'>hier</a>, warum wir das brauchen und wie das funktioniert.</p>";
                        echo "<p>".$share."</p>";
                        if ($ver_float > 0.9999999) echo "<p>".$beCreator."</p>";
                        echo "<p>".$contactus."</p>";
                        echo "<p>".$othersurveys."</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    } else {
                        echo "<h3>Vielen Dank!</h3>";
                        echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :)</p>";
                        echo "<p>".$share."</p>";
                        if ($ver_float > 0.9999999) echo "<p>".$beCreator."</p>";
                        echo "<p>".$contactus."</p>";
                        echo "<p>".$othersurveys."</p>";
                        echo "<a href='/' class='button large fit'>Startseite</a>";
                    }
                } else { //user has already submitted
                    if (!$users->getUserValidation($uid)) {
                        $sent = sendconfirmation($uid, $email, $target);
                        echo "<h3>Du hast bereits teilgenommen.</h3>";
                        echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Deine ursprüngliche Abgabe wird allerdings erst gewertet, wenn Du Deine E-Mail-Adresse validierst. Ein Link dazu wird Dir zugesandt. Erfahre <a href='https://www.eh-umfragen.de/mailinfo' target='_blank'>hier</a>, warum wir das brauchen und wie das funktioniert.</p>";
                        echo "<p>".$share."</p>";
                        if ($ver_float > 0.9999999) echo "<p>".$beCreator."</p>";
                        echo "<p>".$contactus."</p>";
                        echo "<p>".$othersurveys."</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    } else {
                        echo "<h3>Du hast bereits teilgenommen.</h3>";
                        echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Deine ursprüngliche Abgabe wird allerdings gewertet. Danke dafür!</p>";
                        echo "<p>".$share."</p>";
                        if ($ver_float > 0.9999999) echo "<p>".$beCreator."</p>";
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
                $uid = $users->getUserIdByMailHash($mailhash);
                if ($uid < 1) $uid = $users->addUser($target, $mailhash); //TODO check if already answered
                if (fill_survey($uid) === 0) { //answers is two-dim-array [answer-nums][answers-per-num]

                    if (!$users->getUserValidation($uid)) {
                        $sent = sendconfirmation($uid, $email, $target);
                        echo "<h3>Vielen Dank!</h3>";
                        echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :) <br>Ihre E-Mail-Adresse ist noch nicht validiert. Ein Link dazu wird Ihnen zugesandt. Erfahren Sie <a href='https://www.eh-umfragen.de/mailinfo' target='_blank'>hier</a>, warum wir das brauchen und wie das funktioniert.<br>Schauen Sie bitte auch in Ihrem Spam-Ordner nach, falls Sie unsere Mail nicht gleich finden.</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    } else {
                        echo "<h3>Vielen Dank!</h3>";
                        echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :)</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    }
                } else { //user has already submitted
                    if (!$users->getUserValidation($uid)) {
                        $sent = sendconfirmation($uid, $email, $target);
                        echo "<h3>Sie haben bereits teilgenommen.</h3>";
                        echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Ihre ursprüngliche Abgabe wird allerdings erst gewertet, wenn Sie Ihre E-Mail-Adresse validiert haben. Ein Link dazu wird Ihnen zugesandt. Erfahren Sie <a href='https://www.eh-umfragen.de/mailinfo' target='_blank'>hier</a>, warum wir das brauchen und wie das funktioniert.</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                    } else {
                        echo "<h3>Sie haben bereits teilgenommen.</h3>";
                        echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Ihre ursprüngliche Abgabe wird allerdings gewertet. Danke dafür!</p>";
                        echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";

                    }
                }
            }
            elseif ($target === "no_restriction") {
                if (has_submitted($survey_id)) {
                    echo "<h3>Sie haben bereits teilgenommen.</h3>";
                    echo "<p>Eine zweite Abgabe kann leider nicht gewertet werden. Ihre ursprüngliche Abgabe wird allerdings gewertet. Danke dafür!</p>";
                    echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                } else {
                    // Erzeugt einen neuen Benutzer mit einem Dummy-Mailhash und einer Dummy-Benutzergruppe
                    $users = new Users();
                    $dummy_mailhash = md5(uniqid(rand(), true));
                    $user_id = $users->addUser($target, $dummy_mailhash);

                    fill_survey($user_id);
                    store_submission($survey_id);
                    echo "<h3>Vielen Dank!</h3>";
                    echo "<p>Danke für die Abgabe, " . $nice[rand(0, 3)] . " :)</p>";
                    echo "<a href='/' class='button large fit'>Zurück zur Startseite</a>";
                }
            }

            else {
                //hier nur Mitarbeitende
                if ($target == "ehlb_lecturers") {
                    echo "<h3>Die E-Mail-Adresse scheint keine Adresse der EH zu sein.</h3>";
                    echo "<p>Nur Mitarbeitende der EH gehören zur Zielgruppe dieser Umfrage. Deshalb überprüfen wir den Aufbau der E-Mail-Adresse und versenden eine kleine Validierungsmail. Keine Sorge, Sie können noch zurück und die E-Mail-Adresse ändern.</p>";
                    echo "<a href='javascript:history.back()' class='button large fit'>Zurück zur Umfrage</a>";
                }
                //hier Studierende und Mitarbeitende
                elseif ($target == "ehlb_all") {
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
            if ($target !== "no_restriction") echo "Schauen Sie bald wieder vorbei, wenn wir nicht nur neue Fragen, sondern auch Auswertungen Ihrer aktuellen Antworten haben!</p>";
        }



/**
 * TODO
 * do something with $sent
 * it can be
 * "" no mail has been tried to sent
 * "OK" mail has been sent
 * "ERROR" mail could not be sent (serverside error like certificate bs)
 * php exception message
 *
 */



        /*
        echo password_hash($_POST["email"], PASSWORD_ARGON2ID) . "<br>";
        echo $_POST["sid"] . "<br>";
        echo time();
*/
        function has_submitted($survey_id) {
            if (isset($_COOKIE["submitted_surveys"])) {
                $submitted_surveys = json_decode($_COOKIE["submitted_surveys"], true);
                return in_array($survey_id, $submitted_surveys);
            }
            return false;
        }

        function store_submission($survey_id) {
            $submitted_surveys = [];
            if (isset($_COOKIE["submitted_surveys"])) {
                $submitted_surveys = json_decode($_COOKIE["submitted_surveys"], true);
            }

            $submitted_surveys[] = $survey_id;
            $cookie_value = json_encode($submitted_surveys);
            $cookie_expiration = time() + 60 * 60 * 24 * 365;

            //somehow php setcookie doesnt work TODO fix this
            echo "<script>
        document.cookie = 'submitted_surveys=" . $cookie_value . "; expires=" . date('D, d M Y H:i:s T', $cookie_expiration) . "; path=/';
    </script>";
        }
        ?>

    </header>
</section>
