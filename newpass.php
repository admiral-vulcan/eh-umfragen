<section id="intro">
    <header>
        <h1>Passwort zurücksetzen</h1>
        <?php
        require_once("head.php");
        require_once("gitignore/code.php");
        require_once ("database_com.php");
        require_once ("pwdChgSet.php");
        //include ("greeting.php");




        if (isset($_GET["psetstr"])) {
            $email = valPwdMailKey($_GET["psetstr"]);

            if ($email !== -1)  {

                if (isset($_POST["email"])) {
                    if ($_POST["email"] == $email) {
                        $creator_id = get_creator_id($email);
                        if ($creator_id != -1) {
                            $pwdChangeAnswer = setPassword($creator_id, $_POST["password1set"]);
                            if ($pwdChangeAnswer == "OK") alert("Passwort gesetzt", "Dein neues Passwort wurde gesetzt und funktioniert ab sofort. Probiere es gleich aus.", "info", true, "/?creator=challenge");
                            else alert("Passwort nicht akzeptiert.", $pwdChangeAnswer, "warning");
                        }
                    }
                    else alert("Falsche E-Mail-Adresse", "Die angegebene E-Mail-Adresse stimmt nicht mit derjenigen überein, an die wir die Mail zum Zurücksetzen geschickt haben.", "warning");
                }
                ?>
                <p>Bitte gib zur Sicherheit nochmals Deine studentische E-Mail-Adresse ein. Diese gleichen wir mit derjenigen ab, an die wir die Mail zum Zurücksetzen gesendet haben.

                    <br>Gib bitte zweimal ein neues Passwort ein. Das Passwort muss mindestens 8 Zeichen lang sein und eine
                    Kombination aus Groß- und Kleinbuchstaben, Ziffern und Sonderzeichen enthalten - mindestens drei der vier
                    genannten Zeichenarten. Es wird auch überprüft, ob das Passwort ein leicht zu erratenes Muster enthält oder
                    auf bekannten Leak-Seiten auftaucht. So stellen wir sicher, dass Dein Passwort sicher ist.

                </p>
                <form method="POST" id="setPwd" style="max-width: 20em">
                    <input type="email" id="email" name="email" placeholder="Hochschul-E-Mail-Adresse" autocomplete="email" pattern=".+@studnet\.eh-ludwigsburg\.de$" required>
                    <input type="password" id="password1set" name="password1set" placeholder="Passwort" autocomplete="new-password" oninput="checkPasswordreg();" pattern="(?=.{8,})((?=.*\d)(?=.*[a-z])(?=.*[A-Z])|(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_])|(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])).*" minlength="8" required>

                    <span id="password1setEye" class="fa fa-fw fa-eye field-icon toggle-password"></span>

                    <input type="password" id="password2set" name="password2set" placeholder="Passwort wiederholen" autocomplete="new-password" oninput="checkPasswordreg();" pattern="(?=.{8,})((?=.*\d)(?=.*[a-z])(?=.*[A-Z])|(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_])|(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])).*" minlength="8" required>

                    <span id="password2setEye" class="fa fa-fw fa-eye field-icon toggle-password"></span>

                    <input type="submit" value="Passwort setzen">
                </form>

                <?php
            }
            else {
                echo '<meta http-equiv="refresh" content="0; url=/" />';
            }
        }
        else {
            echo '<meta http-equiv="refresh" content="0; url=/" />';
        }
        ?>
    </header>
</section>

<script type="application/javascript">
    const email = document.getElementById("email");
    const password1set = document.getElementById("password1set");
    const password1setEye = document.getElementById("password1setEye");
    const password2set = document.getElementById("password2set");
    const password2setEye = document.getElementById("password2setEye");
    const fontColor = email.style.color;

    password1setEye.addEventListener('mousedown', showPassword1set);
    password1setEye.addEventListener('mousemove', hidePassword1set);
    password1setEye.addEventListener('mouseup', hidePassword1set);
    password1setEye.addEventListener('touchstart', showPassword1set);
    password1setEye.addEventListener('touchmove', hidePassword1set);
    password1setEye.addEventListener('touchend', hidePassword1set);

    password2setEye.addEventListener('mousedown', showPassword2set);
    password2setEye.addEventListener('mousemove', hidePassword2set);
    password2setEye.addEventListener('mouseup', hidePassword2set);
    password2setEye.addEventListener('touchstart', showPassword2set);
    password2setEye.addEventListener('touchmove', hidePassword2set);
    password2setEye.addEventListener('touchend', hidePassword2set);

    function showPassword1set() {
        password1set.setAttribute('type', 'text');
    }

    function hidePassword1set() {
        password1set.setAttribute('type', 'password');
    }

    function showPassword2set() {
        password2set.setAttribute('type', 'text');
    }

    function hidePassword2set() {
        password2set.setAttribute('type', 'password');
    }

    function checkPasswordreg() {
        if (password2set.value !== password1set.value) password2set.style.color = "red";
        else password2set.style.color = fontColor;
    }
</script>
