<section id="intro">
    <?php
    require_once "gitignore/gcred.php";

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


    $sent = "";
    if (isset($_POST['gid']) && $_POST['gid'] != "") {
        //login google
        $cid = get_cid($_POST['gid'], "gid");
        $mail = get_creator_mail($cid);
        if (creator_is_validated($_POST['gid'], "gid")){

            //login!
            my_session_start($cid);
            echo '<meta http-equiv="refresh" content="0; URL=/?creator=creator">';
        }
        else {
            $sent = sendCreatorConfirmation($cid, $mail);
            alert("Freischaltung ausstehend", "Um Deine Registrierung abzuschließen, klicke bitte auf den Link, die wir Dir an Deine studentische E-Mail-Adresse geschickt haben.");
        }
    }

    elseif (isset($_POST['email']) && $_POST['email'] != "") {
        //login mail & pwd
        if (creator_has_entry($_POST['email'])) {
            $cid = get_cid($_POST['email'], "email");
            if (creator_is_validated($_POST['email'], "email")){
                if (verifyPassword($cid, $_POST['password'])) {

                    //login!
                    get_creator_data($cid);
                    my_session_start($cid);
                    echo '<meta http-equiv="refresh" content="0; URL=/?creator=creator">';
                }
                else alert("Falsches Passwort", "Das Passwort ist falsch. Bitte versuche es noch einmal");
            }
            else {
                $sent = sendCreatorConfirmation($cid, $_POST['email']);
                alert("Freischaltung ausstehend", "Um Deine Registrierung abzuschließen, klicke bitte auf den Link, die wir Dir an Deine studentische E-Mail-Adresse geschickt haben.");
            }
        }
        else alert("Noch nicht registriert", "Du bist noch nicht registriert.");
    }
    elseif (isset($_POST['emailreg'])) {
        if (!isset($_POST['gidreg'])) $_POST['gidreg'] = "";
        if (!isset($_POST['gmailreg'])) $_POST['gmailreg'] = "";
        $createMsg = create_creator($_POST['gidreg'], $_POST['emailreg'], $_POST['gmailreg'], $_POST['firstnamereg'], $_POST['familynamereg'], $_POST['password1reg'], $_POST['password2reg'], postCheckifnN('AGB'), postCheckifnN('gPicAgree'), postValifnN('gPicreg'));
        if ($createMsg === "mail sent: OK") { //everything worked
            alert("Vielen Dank!", "Um Deine Registrierung abzuschließen, klicke bitte auf den Link, die wir Dir an Deine studentische E-Mail-Adresse geschickt haben.");
        }
        elseif (str_contains("mail sent: ")) {
/**
 * TODO do something with mail errormessage
 * it starts with "mail sent: "
 * followed by
 * "" no mail has been tried to sent
 * "OK" mail has been sent (already checked)
 * "ERROR" mail could not be sent (serverside error like certificate bs)
 * php exception message
 *
 */
        }
        else alert("Passwort nicht akzeptiert.", $createMsg, "warning");
    }
    function postValifnN($str) {
        return $_POST[$str] ?? "";
    }
    function postCheckifnN($str) {
        if (isset($_POST[$str]) && $_POST[$str] == "on") return "checked";
        else return "";
    }

    if (isset($_POST['sendSetPwdMail']) && $_POST['sendSetPwdMail'] == 'true') {
        sendCreatorNewPassword(postValifnN('emailSetPwd'));
        //alert($_POST['sendSetPwdMail'], $_POST['email']);
    }

    ?>
    <br>
    <h2><?php echo translate("Login", "en", $GLOBALS["lang"]); ?></h2>
    <form method="POST" id="anmelden" style="max-width: 20em">
        <label for="email"></label><input type="email" id="email" name="email" placeholder="<?php echo translate("Hochschul-E-Mail-Adresse", "de", $GLOBALS["lang"]); ?>" autocomplete="email" oninput="checkEmail()" pattern=".+@studnet\.eh-ludwigsburg\.de$" value="<?php echo postValifnN('email'); ?>" required>
        <input type="hidden" id="firstname" name="firstname" value="<?php echo postValifnN('firstname'); ?>">
        <input type="hidden" id="familyname" name="familyname" value="<?php echo postValifnN('familyname'); ?>">
        <input type="hidden" id="gmail" name="gmail" value="<?php echo postValifnN('gmail'); ?>">
        <input type="hidden" id="gPic" name="gPic" value="<?php echo postValifnN('gPic'); ?>">
        <label for="password"></label><input type="password" id="password" name="password" placeholder="<?php echo translate("Passwort", "de", $GLOBALS["lang"]); ?>" autocomplete="password" value="<?php echo postValifnN('password'); ?>" required>
        <span id="passwordEye" class="fa fa-fw fa-eye field-icon toggle-password"></span>
        <input type="hidden" id="gid" name="gid" value="<?php echo postValifnN('gid'); ?>">
        <input type="submit" value="<?php echo translate("Anmelden", "de", $GLOBALS["lang"]); ?>"><div class="button" onclick="setPwdInfo();" ><?php echo translate("Passwort zurücksetzen", "de", $GLOBALS["lang"]); ?></div>
    </form>


    <!-- p>Anmeldung via Apple zulassen. | ON | OFF </p> -->
    <p><?php echo translate("Registrierung und Anmeldung via Google zulassen?", "de", $GLOBALS["lang"]); ?><br> <a style="cursor: pointer;" onclick='loadGoogleWarningbyUser();'>(<?php echo translate("Warnung anzeigen und Cookies löschen", "de", $GLOBALS["lang"]); ?>)</a></p>
    <br>
    <div id="slider">
        <div aria-label="Google-verwenden-Button" style="" id="slider-thumb" class="not-selectable" tabindex="0"></div>
        <div class="not-selectable slider-text-container" id="slider-text">
            <p class="slider-text-no-yes">
                <span class="slider-text-no"><?php echo translate("Nein", "de", $GLOBALS["lang"]); ?></span>
                <span class="slider-text-yes"><?php echo translate("Ja", "de", $GLOBALS["lang"]); ?></span>
            </p>
        </div>
    </div>
    <br>
    <div id="g_id_onload"
         data-client_id="<?php echo $GLOBALS["gcred"]; ?>"
         data-callback="handleCredentialResponse">
    </div>
    <div style="position: absolute"><div id="google_btn"></div></div><br><br><br>
    <!-- <p>Anmeldung via Microsoft zulassen. | ON | OFF </p> -->

    <h2><?php echo translate("Registrierung", "de", $GLOBALS["lang"]); ?></h2>
    <p id="gRegInfo" style="display: none"><?php echo translate("Um die Registrierung mit Google abzuschließen, brauchen wir noch Deine Hochschul-E-Mail-Adresse und Deinen Namen.", "de", $GLOBALS["lang"]); ?></p>
    <form method="POST" id="registrieren" style="max-width: 20em">
        <label for="firstnamereg"></label><input type="text" id="firstnamereg" name="firstnamereg" placeholder="<?php echo translate("Vorname", "de", $GLOBALS["lang"]); ?>" autocomplete="given-name" oninput="checkFirstNameReg(); checkFamilyNameReg(); checkEmailReg()" pattern="[A-Z][A-Za-z]{2,}" minlength="3" value="<?php echo postValifnN('firstnamereg'); ?>" required>
        <label for="familynamereg"></label><input type="text" id="familynamereg" name="familynamereg" placeholder="<?php echo translate("Nachname", "de", $GLOBALS["lang"]); ?>" autocomplete="family-name" oninput="checkFirstNameReg(); checkFamilyNameReg(); checkEmailReg()" pattern="[A-Z][A-Za-z]{2,}" minlength="3" value="<?php echo postValifnN('familynamereg'); ?>" required>

        <label for="emailreg"></label><input type="email" id="emailreg" name="emailreg" placeholder="<?php echo translate("Hochschul-E-Mail-Adresse", "de", $GLOBALS["lang"]); ?>" autocomplete="email" oninput="checkFirstNameReg(); checkFamilyNameReg(); checkEmailReg()" pattern=".+@studnet\.eh-ludwigsburg\.de$" value="<?php echo postValifnN('emailreg'); ?>" required>
        <label for="password1reg"></label><input type="password" id="password1reg" name="password1reg" placeholder="<?php echo translate("Passwort", "de", $GLOBALS["lang"]); ?>" autocomplete="new-password" pattern="(?=.{8,})((?=.*\d)(?=.*[a-z])(?=.*[A-Z])|(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_])|(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])).*" minlength="8" oninput="checkPasswordreg();" value="<?php echo postValifnN('password1reg'); ?>" required>

        <span id="password1regEye" class="fa fa-fw fa-eye field-icon toggle-password"></span>

        <label for="password2reg"></label><input type="password" id="password2reg" name="password2reg" placeholder="<?php echo translate("Passwort wiederholen", "de", $GLOBALS["lang"]); ?>" autocomplete="new-password" pattern="(?=.{8,})((?=.*\d)(?=.*[a-z])(?=.*[A-Z])|(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_])|(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])).*" minlength="8" oninput="checkPasswordreg();" value="<?php echo postValifnN('password2reg'); ?>" required>

        <span id="password2regEye" class="fa fa-fw fa-eye field-icon toggle-password"></span>

        <div class="list" style="text-align: left;">
            <input type="checkbox" class="checkbox" id="AGB" name="AGB" <?php echo postCheckifnN('AGB'); ?> required>
            <label for="AGB"><?php echo translate('Bitte stimme unseren <a href="?content=agb" target="_blank">AGB</a> zu.', "de", $GLOBALS["lang"]); ?></label>
            <div id="gPicAgreeDIV" style="display: none">
                <input type="checkbox" class="checkbox" id="gPicAgree" name="gPicAgree" <?php echo postCheckifnN('gPicAgree'); ?>>
                <label for="gPicAgree"><?php echo translate("Möchtest Du Dein Google-Profilbild als Profilbild für hier verwenden?", "de", $GLOBALS["lang"]); ?></label>
            </div>
        </div>
        <input type="hidden" id="gidreg" name="gidreg" value="<?php echo postValifnN('gidreg'); ?>">
        <input type="hidden" id="gmailreg" name="gmailreg" value="<?php echo postValifnN('gmailreg'); ?>">
        <input type="hidden" id="gPicreg" name="gPicreg" value="<?php echo postValifnN('gPicreg'); ?>">
        <input type="submit" value="<?php echo translate("Registrieren", "de", $GLOBALS["lang"]); ?>">
    </form>
        <p>
            <br><?php echo translate("Damit wir neue Mitglieder freischalten können, müssen wir wissen, wer es ist. Deshalb wird in diesen Fällen sowohl der Name als auch die E-Mail-Adresse gespeichert.", "de", $GLOBALS["lang"]); ?>
            <br><?php echo translate("Der Name wird dann so auch in neu erstellten Umfragen angezeigt.", "de", $GLOBALS["lang"]); ?>
            <br><?php echo translate("E-Mail-Adressen und Namen können beim Ausfüllen von Umfragen dennoch nicht den Antworten zugeordnet werden.", "de", $GLOBALS["lang"]); ?>


            <br><?php echo translate("Gib bitte zweimal ein neues Passwort ein. Das Passwort muss mindestens 8 Zeichen lang sein und eine Kombination aus Groß- und Kleinbuchstaben, Ziffern und Sonderzeichen enthalten - mindestens drei der vier genannten Zeichenarten. Es wird auch überprüft, ob das Passwort ein leicht zu erratenes Muster enthält oder auf bekannten Leak-Seiten auftaucht. So stellen wir sicher, dass Dein Passwort sicher ist.", "de", $GLOBALS["lang"]); ?>

            <br><a href="/?content=secureinfo" target="_blank"><?php echo translate("Wie werden meine privaten Daten gesendet? Ist das sicher?", "de", $GLOBALS["lang"]); ?></a>
            <br><a href="/?content=passwordinfo" target="_blank"><?php echo translate("Wie wird mein Passwort gespeichert? Wer kann es sehen?", "de", $GLOBALS["lang"]); ?></a>
        </p>

    <!-- Google Warning -->
    <?php
    $googleWarning = alert("Google Anmeldung", "
    Beim Gebrauch des Anmeldeverfahrens von Google werden manche Deiner Daten, z.B. Deine IP-Adresse,
                        <br>ohne unseren Einfluss an Google gesendet und Google speichert und verwertet evtl. Cookies zu wirtschaftlichen Zwecken.
                        <br>Welche Daten Google speichert und wie Google sie verwendet, liegt außerhalb unseres Einflusses.
                        <br><a href='/?content=googleinfo' target='_blank'>Erfahre >>hier<< mehr darüber.</a><br>
                        <br>Wenn Du Ja wählst, speichern wir Deine Auswahl in einem Cookie.
                        <br>Wenn Du Nein wählst, werden Google-bezogene Cookies dieser Seite (innerhalb unseres Einflussbereichs) gelöscht.<br>
                        <br>Möchtest Du dennoch fortfahren?
    ", "warning", false, "setUserAgreed();", "setUserDisagreed();");
    ?>

    <!-- no Mail To Set Pwd Warning -->
    <?php
    $noMailToSetPwdWarning = alert("Passwort zurücksetzen", "Bitte gib zuerst Deine studentische E-Mail-Adresse ein, um Dein Passwort zurückzusetzen.", "warning", false);
    ?>

    <!-- set pwd info -->
    <?php
    $setPwdInfo = alert("Passwort zurücksetzen", "Wir senden Dir eine E-Mail mit einem Link, der 24 Stunden lang gültig ist. Klicke auf den Link, um ein neues Passwort zu setzen. <br>Möchtest Du fortfahren?", "warning", false, "sendSetPwdMail();", "close");
    ?>


    <!-- Register Error -->
    <?php
    if (isset($createMsg) && $createMsg != "OK") $regError = alert("Registrierungsfehler", $createMsg, "error", true);
    ?>
    <!-- TODO create new error message
    <div  class="error" id="registerError" style="display: none">
        <ul class="actions stacked">
            <li>
                <h3>Fehler</h3>
                <p>
                <b>
                    <?php //echo $createMsg; ?>
                </b>
                </p>
                <a class='button large fit' onclick='hideRegisterError();'>OK</a>
            </li>
        </ul>
    </div>
    -->
    <script type="application/javascript">
        const email = document.getElementById("email");
        const firstname = document.getElementById("firstname");
        const familyname = document.getElementById("familyname");
        const password = document.getElementById("password");
        const passwordEye = document.getElementById("passwordEye");
        const anmelden = document.getElementById("anmelden");
        const registrieren = document.getElementById("registrieren");
        const emailreg = document.getElementById("emailreg");
        const gRegInfo = document.getElementById("gRegInfo");
        const gPicAgreeDIV = document.getElementById("gPicAgreeDIV");
        const firstnamereg = document.getElementById("firstnamereg");
        const familynamereg = document.getElementById("familynamereg");
        const password1reg = document.getElementById("password1reg");
        const password1regEye = document.getElementById("password1regEye");
        const password2reg = document.getElementById("password2reg");
        const password2regEye = document.getElementById("password2regEye");
        const gmailreg = document.getElementById("gmailreg");
        const gmail = document.getElementById("gmail");
        const gid = document.getElementById("gid");
        const gidreg = document.getElementById("gidreg");
        const gPic = document.getElementById("gPic");
        const gPicreg = document.getElementById("gPicreg");

        //console.log(getComputedStyle(gPicAgreeDIV).display);

        const slider = document.getElementById('slider');
        const thumb = document.getElementById('slider-thumb');
        //const googleWarning = document.getElementById("GoogleWarning");
        // const registerError = document.getElementById("registerError");
        const google_btn = document.getElementById('google_btn');
        const googleStyle = getComputedStyle(google_btn);
        var isOn = slider.classList.contains('on');
        let currentSliderState = slider.classList.contains('on');
        let isDragging = false;
        let sliderPercentage = 0;
        var userAgreedToGoogle = userAgreedToGoogle || false;
        var userUsesGoogle = userUsesGoogle || false;
        var offPos = '3px';
        var onPos = `${slider.offsetWidth - thumb.offsetWidth-5}px`;

        const createMsg = '<?php echo $createMsg ?? ""; ?>';
        const fontColor = emailreg.style.color;

        checkGoogleRegReady();
        //showRegisterError();
        /*
                function hideRegisterError() {
                    registerError.style.display = "none";
                }
        *//*
        function showRegisterError() {
            if (createMsg !== "") registerError.style.display = "inline-block";
        }*/

        function checkGoogleRegReady() {
            if (gid.value !== "") {
                emailreg.value = emailreg.value || email.value || "";
                firstnamereg.value = firstnamereg.value || firstname.value || "";
                familynamereg.value = familynamereg.value || familyname.value || "";
                gPicreg.value = gPicreg.value || gPic.value || "";
                gidreg.value = gidreg.value || gid.value || "";
                gmailreg.value =  gmail.value || "";
                checkFamilyNameReg();
                password1reg.removeAttribute("required");
                password1reg.style.display = "none";
                password2reg.removeAttribute("required");
                password2reg.style.display = "none";
                gRegInfo.style.display = "block";
                gPicAgreeDIV.style.display = "block";
            }
        }

        checkUserAgreedToGoogleCookie();
        checkUserUsesGoogleCookie();
        setSliderInitialState();

        passwordEye.addEventListener('mousedown', showPassword);
        passwordEye.addEventListener('mousemove', hidePassword);
        passwordEye.addEventListener('mouseup', hidePassword);
        passwordEye.addEventListener('touchstart', showPassword);
        passwordEye.addEventListener('touchmove', hidePassword);
        passwordEye.addEventListener('touchend', hidePassword);

        password1regEye.addEventListener('mousedown', showPassword1reg);
        password1regEye.addEventListener('mousemove', hidePassword1reg);
        password1regEye.addEventListener('mouseup', hidePassword1reg);
        password1regEye.addEventListener('touchstart', showPassword1reg);
        password1regEye.addEventListener('touchmove', hidePassword1reg);
        password1regEye.addEventListener('touchend', hidePassword1reg);

        password2regEye.addEventListener('mousedown', showPassword2reg);
        password2regEye.addEventListener('mousemove', hidePassword2reg);
        password2regEye.addEventListener('mouseup', hidePassword2reg);
        password2regEye.addEventListener('touchstart', showPassword2reg);
        password2regEye.addEventListener('touchmove', hidePassword2reg);
        password2regEye.addEventListener('touchend', hidePassword2reg);

        slider.addEventListener('click', toggleSliderEvent);
        thumb.addEventListener('mousedown', handleMouseDown);
        document.addEventListener('mouseup', handleMouseUp);
        document.addEventListener('mousemove', handleMouseMove);
        slider.addEventListener('touchstart', handleTouchStart);
        slider.addEventListener('touchmove', handleTouchMove);
        slider.addEventListener('touchend', handleTouchEnd);

        slider.addEventListener('click', () => {
            if (slider.classList.contains('on') !== currentSliderState) {
                currentSliderState = !currentSliderState;
            }
        });

        function setSliderInitialState() {
            if (userUsesGoogle && userAgreedToGoogle) {
                thumb.style.left = onPos;
                slider.classList.add('on');
                google_btn.style.display = "none";
                drawGoogle();
            }
            else {
                thumb.style.left = offPos;
                slider.classList.remove('on');
                removeGoogle();
            }
        }

        function loadGoogleWarningbyUser() {
            moveToOffPosition();
            setUserDoesntUseGoogle();
            setUserDisagreedToGoogleCookie();
            //googleWarning.style.display="inline-block";
            showAlert(<?php echo $googleWarning; ?>);
            userAgreedToGoogle = false;
            userUsesGoogle = false;
        }

        function setUserAgreed() {
            //googleWarning.style.display="none";
            hideAlert(<?php echo $googleWarning; ?>);
            setUserAgreedToGoogleCookie();
            userAgreedToGoogle = true;
            slider.classList.add('on');
            moveToOnPosition();
        }

        function setUserDisagreed() {
            //googleWarning.style.display="none";
            hideAlert(<?php echo $googleWarning; ?>);
            userAgreedToGoogle = false;
            slider.classList.remove('on');
            moveToOffPosition();
        }

        function handleTouchStart() {
            isDragging = true;
            thumb.classList.add('dragging');
        }

        function handleTouchMove(e) {
            if (!isDragging && !thumb.classList.contains('dragging')) return;
            const touch = e.touches[0];
            const x = touch.clientX - slider.offsetLeft;
            updateSlider(x);
        }

        function handleTouchEnd() {
            if (isDragging || thumb.classList.contains('dragging')) {
                isDragging = false;
                thumb.classList.remove('dragging');
                snapToBorder();
                toggleSlideThumb();
            }
        }

        function handleMouseDown() {
            isDragging = true;
            thumb.classList.add('dragging');
        }

        function handleMouseMove(e) {
            if (!isDragging && !thumb.classList.contains('dragging')) return;
            const x = e.clientX - slider.offsetLeft;
            updateSlider(x);
        }

        function handleMouseUp() {
            if (isDragging || thumb.classList.contains('dragging')) {
                isDragging = false;
                thumb.classList.remove('dragging');
                snapToBorder();
                toggleSlider();
            }
        }

        function updateSlider(x) {
            x = Math.max(20, Math.min(x, slider.offsetWidth - thumb.offsetWidth+18));
            thumb.style.left = `${x - thumb.offsetWidth / 2}` + 'px';
        }

        function toggleSliderEvent(e) {
            if (e.target === thumb) return;
            toggleHitSwitch();
        }

        function toggleSlideThumb() {
            if (thumb.style.left !== onPos) {
                if (userAgreedToGoogle) thumb.style.left = offPos;
                slider.classList.remove('on');
                removeGoogle();
            } else {
                thumb.style.left = onPos;
                slider.classList.add('on');
            }
            toggleSlider();
        }

        function toggleHitSwitch() {
            if (thumb.style.left === onPos) {
                if (userAgreedToGoogle) thumb.style.left = offPos;
                slider.classList.remove('on');
                removeGoogle();
            } else {
                thumb.style.left = onPos;
                slider.classList.add('on');
            }
            toggleSlider();
        }

        function toggleSlider() {
            if (!userAgreedToGoogle && thumb.style.left === onPos) {
                slider.classList.remove('on');
                setTimeout(async () => {
                    await moveToOffPosition();
                    //googleWarning.style.display="inline-block";
                    showAlert(<?php echo $googleWarning; ?>);
                }, 500);
            }
            drawGoogle();
        }

        function getSliderPositionPercentage(e) {
            const sliderRect = slider.getBoundingClientRect();
            const mouseX = e.clientX || e.touches[0].clientX;
            const sliderX = mouseX - sliderRect.left;
            const sliderWidth = sliderRect.width;
            sliderPercentage = (sliderX / sliderWidth) * 100;
        }


        slider.addEventListener('mousemove', (e) => {
            getSliderPositionPercentage(e);
        });

        slider.addEventListener('touchmove', (e) => {
            getSliderPositionPercentage(e);
        });

        function moveToOnPosition() {
            thumb.style.left = onPos;
            if (userAgreedToGoogle) slider.classList.add('on');
            drawGoogle();
        }

        function moveToOffPosition() {
            thumb.style.left = offPos;
            slider.classList.remove('on');
            removeGoogle();
        }

        function snapToBorder() {
            if (sliderPercentage > 50) moveToOnPosition();
            else moveToOffPosition();
        }
        // Function to get the value of a cookie
        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) === 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        // Function to check if the "userAgreedToGoogle" cookie exists and set the userAgreed variable accordingly
        function checkUserAgreedToGoogleCookie() {
            var cookieValue = getCookie("userAgreedToGoogle");
            if (cookieValue !== "") {
                // If the cookie exists, set the userAgreed variable to the value of the cookie
                userAgreedToGoogle = (cookieValue === "true");
            } else {
                // If the cookie does not exist, set the userAgreed variable to false
                userAgreedToGoogle = false;
            }
        }

        // Function to check if the "userAgreedToGoogle" cookie exists and set the userAgreed variable accordingly
        function checkUserUsesGoogleCookie() {
            var cookieValue = getCookie("userUsesGoogle");
            if (cookieValue !== "") {
                // If the cookie exists, set the userAgreed variable to the value of the cookie
                userUsesGoogle = (cookieValue === "true");
            } else {
                // If the cookie does not exist, set the userAgreed variable to false
                userUsesGoogle = false;
            }
        }


        // Function to set the "userAgreedToGoogle" cookie to true
        function setUserAgreedToGoogleCookie() {
            document.cookie = "userAgreedToGoogle=true;max-age=3600";
            userAgreedToGoogle = true;
        }

        // Function to set the "userAgreedToGoogle" cookie to false
        function setUserDisagreedToGoogleCookie() {
            document.cookie = "userAgreedToGoogle=false;max-age=3600";
            userAgreedToGoogle = false;
            removeAllCookies("Google");
            removeAllCookies("g_");
        }

        // Function to set the "userUsesGoogle" cookie to true
        function setUserUsesGoogle() {
            document.cookie = "userUsesGoogle=true;max-age=3600";
            userUsesGoogle = true;
        }

        // Function to set the "userUsesGoogle" cookie to false
        function setUserDoesntUseGoogle() {
            document.cookie = "userUsesGoogle=false;max-age=3600";
            userUsesGoogle = false;
        }

        function removeAllCookies(str) {

        }

        thumb.addEventListener('keydown', (event) => {
            if (event.keyCode === 13 || event.keyCode === 32) {
                event.preventDefault();
                toggleHitSwitch();
            }
        });

        function drawGoogle() {
            if (slider.classList.contains('on') && thumb.style.left === onPos) {
                if (googleStyle.display === 'none') {
                    var script = document.createElement('script');
                    script.onload = function () {
                        google.accounts.id.initialize({
                            client_id: '<?php echo $GLOBALS["gcred"]; ?>',
                            callback: handleCredentialResponse
                        });
                        google_btn.style.display = "inline-block";
                        google.accounts.id.renderButton(google_btn, {theme: "filled_black"}); //https://developers.google.com/identity/gsi/web/reference/html-reference
                        //google.accounts.id.prompt();
                    };
                    script.src = "assets/js/src/gapi.js";
                    document.head.appendChild(script);
                    setUserUsesGoogle();

                    password1reg.removeAttribute("required");
                    password1reg.style.display = "none";
                    password2reg.removeAttribute("required");
                    password2reg.style.display = "none";
                    gRegInfo.style.display = "block";
                    gPicAgreeDIV.style.display = "block";
                }
            }
            else {
                removeGoogle();
            }
        }

        function removeGoogle() {
            if (userUsesGoogle) setUserDoesntUseGoogle();
            google_btn.style.display = "none";
            if (google_btn) {
                const clickEvent = new MouseEvent('click', {
                    view: window,
                    bubbles: true,
                    cancelable: true,
                });
                google_btn.dispatchEvent(clickEvent);
            }
            password1reg.setAttribute("required", "");
            password1reg.style.display = "block";
            password2reg.setAttribute("required", "");
            password2reg.style.display = "block";
            gRegInfo.style.display = "none";
            gPicAgreeDIV.style.display = "none";
            gid.value = "";
        }

        function decodeJwtResponse(jwt) {
            // Split the JWT into three parts: the header, the payload, and the signature
            //const [header, payload, signature] = jwt.split(".");
            const [, payload] = jwt.split(".");

            // Decode the payload from base64 to a JSON object
            const payloadJSON = atob(payload);
            return JSON.parse(payloadJSON);
        }

        function handleCredentialResponse(response) {
            // decodeJwtResponse() is a custom function defined by you
            // to decode the credential response.
            const responsePayload = decodeJwtResponse(response.credential);

            /*
            console.log("ID: " + responsePayload.sub);
            console.log('Full Name: ' + responsePayload.name);
            console.log('Given Name: ' + responsePayload.given_name);
            console.log('Family Name: ' + responsePayload.family_name);
            console.log("Image URL: " + responsePayload.picture);
            console.log("Email: " + responsePayload.email);
            */

            email.value = email.value || emailreg.value || "";
            firstnamereg.value = responsePayload.given_name;
            familynamereg.value = responsePayload.family_name;
            firstname.value = responsePayload.given_name;
            familyname.value = responsePayload.family_name;
            gmailreg.value = responsePayload.email;
            gmail.value = responsePayload.email;
            gid.value = responsePayload.sub;
            gidreg.value = responsePayload.sub;
            gPic.value = responsePayload.picture;
            gPicreg.value = responsePayload.picture;
            checkFamilyNameReg();
            password1reg.removeAttribute("required");
            password1reg.style.display = "none";
            password2reg.removeAttribute("required");
            password2reg.style.display = "none";
            gRegInfo.style.display = "block";
            gPicAgreeDIV.style.display = "block";
            anmelden.submit();

        }

        function checkPasswordreg() {
            if (password2reg.value !== password1reg.value) password2reg.style.color = "red";
            else password2reg.style.color = fontColor;
        }

        function checkEmail() {
            emailreg.value = email.value;
        }

        function checkEmailReg() {
            email.value = emailreg.value;
            if (
                checkMailAfter(emailreg.value) || (
                    emailreg.value.includes(".com") ||
                    (
                        emailreg.value.includes("de") &&
                        emailreg.value.includes(".") &&
                        emailreg.value.includes("@")
                    )) &&
                !emailreg.value.includes("@studnet.eh-ludwigsburg.de"))
                emailreg.style.color = "red";
            else emailreg.style.color = fontColor;
            checkFamilyNameReg();
        }


        function checkMailAfter(somemail) {
            let substring = "@studnet.eh-ludwigsburg.de";
            let index = somemail.indexOf(substring);
            return (index !== -1 && index + substring.length < somemail.length);
        }

        function checkFirstNameReg() {
            firstname.value = firstnamereg.value;
        }
        function checkFamilyNameReg() {
            familyname.value = familynamereg.value;
            if (!convertGermanChars(familynamereg.value.toLowerCase()).includes(getNameSnippet(convertGermanChars(emailreg.value)))) {
                familynamereg.style.color = 'red';
                return false;
            }
            else {
                familynamereg.style.color = fontColor;
                return true;
            }
        }

        function getNameSnippet(someemail) {
            // Find the position of the @ sign
            const atSignIndex = someemail.indexOf('@');
            // Extract the substring before the @ sign
            let nameSnippet = someemail.substring(0, atSignIndex);
            // Remove any numbers from the username
            nameSnippet = nameSnippet.replace(/\d/g, '');
            // Convert the username to lowercase
            nameSnippet = nameSnippet.toLowerCase();
            return nameSnippet;
        }

        function convertGermanChars(str) {
            return str.replace(/[äÄ]/g, 'ae')
                .replace(/[öÖ]/g, 'oe')
                .replace(/[üÜ]/g, 'ue')
                .replace(/[ß]/g, 'ss');
        }

        function showPassword() {
            password.setAttribute('type', 'text');
        }

        function hidePassword() {
            password.setAttribute('type', 'password');
        }

        function showPassword1reg() {
            password1reg.setAttribute('type', 'text');
        }

        function hidePassword1reg() {
            password1reg.setAttribute('type', 'password');
        }

        function showPassword2reg() {
            password2reg.setAttribute('type', 'text');
        }

        function hidePassword2reg() {
            password2reg.setAttribute('type', 'password');
        }

        function setPwdInfo() {
            if (email.value !== "") {
                showAlert(<?php echo $setPwdInfo; ?>);
            }
            else {
                showAlert(<?php echo $noMailToSetPwdWarning; ?>);
            }
        }
        function sendSetPwdMail() {
            var email = document.getElementById("email").value;

            var myForm = document.createElement("FORM");
            myForm.setAttribute("id","setPwdForm");
            document.body.appendChild(myForm);

            var emailInput = document.createElement("INPUT");
            emailInput.setAttribute("id","emailInput");
            emailInput.setAttribute("type","hidden");
            emailInput.setAttribute("name","emailSetPwd");
            emailInput.setAttribute("value", email);
            document.getElementById("setPwdForm").appendChild(emailInput);

            var sendSetPwdMailInput = document.createElement("INPUT");
            sendSetPwdMailInput.setAttribute("id","sendSetPwdMailInput");
            sendSetPwdMailInput.setAttribute("type","hidden");
            sendSetPwdMailInput.setAttribute("name","sendSetPwdMail");
            sendSetPwdMailInput.setAttribute("value", "true");
            document.getElementById("setPwdForm").appendChild(sendSetPwdMailInput);

            myForm.method = "POST";
            myForm.action = window.location.href;
            myForm.submit();
        }
    </script>

    <!--
    <script src="assets/js/src/gapi.js" async defer></script>
    -->