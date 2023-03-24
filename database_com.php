<?php
require_once("gitignore/dbcred.php");
require_once ("passwordcheck.php");

function set_validated($uid) { //UPDATE users SET email = ? WHERE id = ?
    $validated = 0;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("UPDATE users SET isvalid = 1 WHERE uid = ?");
    try {
        $statement->execute(array($uid));
    }
    catch (Exception $e) {
        $e->getMessage();
    }
    $pdo = null;
}

function set_creatorValidated($cid) { //UPDATE users SET email = ? WHERE id = ?
    $validated = 0;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("UPDATE creators SET isvalid = 1 WHERE cid = ?");
    try {
        $statement->execute(array($cid));
    }
    catch (Exception $e) {
        $e->getMessage();
    }
    $pdo = null;
}

function mail_is_validated($uid) {
    $validated = 0;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT isvalid FROM users WHERE uid = ?");
    try {
        $statement->execute(array($uid));
        while($row = $statement->fetch()) {
            $valdata = $row['isvalid'];
            if ($valdata == 1) $validated = 1;
        }
    }
    catch (Exception $e) {
        $e->getMessage();
    }
    $pdo = null;
    return $validated;
}

function creator_is_validated($id, $type = 'cid') {
        $validated = 0;
    if ($type !== "cid" && $type !== "gid" && $type !== "email") return -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT isvalid FROM creators WHERE " . $type . " = ?");
    try {
        $statement->execute([$id]);
        while($row = $statement->fetch()) {
            $valdata = $row['isvalid'];
            if ($valdata == "1") $validated = 1;
        }
    }
    catch (Exception $e) {
        $e->getMessage();
    }
    $pdo = null;
    return $validated;
}

function get_cid($id, $type = "email") {
    if ($type !== "email" && $type !== "gid") return -1;
    $cid = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM creators WHERE " . $type . " = ?");
    $statement->execute([$id]);
    while($row = $statement->fetch()) {
        $cid = $row['cid'];
    }

    $pdo = null;
    return $cid;
}

function get_email_id($mailhash) {
    $uid = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM users WHERE mailhash = ?");
    $statement->execute(array($mailhash));
    while($row = $statement->fetch()) {
        $uid = $row['uid'];
    }

    $pdo = null;
    return $uid;
}

function get_survey_name($sid) {
    $name = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ? LIMIT 1");
    $statement->execute(array(sanitize($sid)));
    while($row = $statement->fetch()) {
        $name = $row['name'];
    }

    $pdo = null;
    return $name;
}
function get_creator_name($cid) {

    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM creators WHERE cid = ? LIMIT 1");
    $statement->execute(array($cid));
    while($row = $statement->fetch()) {
        $name['cid'] = $cid;
        $name['first'] = $row['firstname'];
        $name['family'] = $row['familyname'];
        break;
    }
    $pdo = null;
    return $name;
}
function get_survey($sid) {
    $survey = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ? LIMIT 1");
    $statement->execute(array(sanitize($sid)));
    while($row = $statement->fetch()) {
        $survey = $row;
        break;
    }

    $pdo = null;
    return $survey;
}
function get_survey_contributors_names($sid) {
    $name['cid'] = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ? LIMIT 1");
    $statement->execute(array(sanitize($sid)));
    while($row = $statement->fetch()) {
        $name['cids'] = $row['contributors'];
        break;
    }
    $substrings = explode(";", $name['cids']);
    foreach ($substrings as $key => $value) {
        $name[$key]['cid'] = $value;
        $var = get_creator_name($value);
        $name[$key]['first'] = $var['first'];
        $name[$key]['family'] = $var['family'];
    }

    $pdo = null;
    return $name;
}
function get_survey_filename($sid) {
    $filename = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ? LIMIT 1");
    $statement->execute(array(sanitize($sid)));
    while($row = $statement->fetch()) {
        $filename = $row['filename'];
        break;
    }

    $pdo = null;
    return $filename;
}

function get_survey_id($name) {
    $id = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE name = ? LIMIT 1");
    $statement->execute(array(sanitize($name)));
    while($row = $statement->fetch()) {
        $id = $row['id'];
    }
    $pdo = null;
    return intval($id);
}

function set_survey_id($name, $filename = "YYYY-MM-DD-title-cid.csv") {
    $creator = str_replace('.csv', '', explode("-", $filename)[4]);
    $id = 1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT id FROM surveys ORDER BY id DESC LIMIT 1");
    $statement->execute();
    while($row = $statement->fetch()) {
        $id = $row['id'] + 1;
    }
    try {
        $statement = $pdo->prepare("INSERT INTO surveys (id, creator, isactive, hasresults, name, since, filename) VALUES (?,?,?,?,?,?,?)");
        $statement->execute([$id, $creator, 1, 0, sanitize($name), time(), $filename]);
    }

    catch (Exception $e) {
        $e->getMessage();
        echo $e;
    }

    $pdo = null;
    return $id;
}


function get_inactivesince($id) {
    $inactivesince = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ? LIMIT 1");
    try {
        $statement->execute([$id]);
        while ($row = $statement->fetch()) {
            $inactivesince = $row['inactivesince'];
        }
    }

    catch (Exception $e) {
        $e->getMessage();
        //echo $e;
    }
    $pdo = null;
    return $inactivesince;
}

function get_since($id) {
    $since = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ? LIMIT 1");
    try {
        $statement->execute([$id]);
        while ($row = $statement->fetch()) {
            $since = $row['since'];
        }
    }

    catch (Exception $e) {
        $e->getMessage();
        //echo $e;
    }
    $pdo = null;
    return $since;
}

function get_active($id) {
    $isactive = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ? LIMIT 1");
    try {
        $statement->execute([$id]);
        while ($row = $statement->fetch()) {
            $isactive = $row['isactive'];
        }
    }

    catch (Exception $e) {
        $e->getMessage();
        //echo $e;
    }
    $pdo = null;
    return $isactive;
}

function get_hasresults($id) {
    $hasresults = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ? LIMIT 1");
    $statement->execute([$id]);
    while($row = $statement->fetch()) {
        $hasresults = $row['hasresults'];
    }

    $pdo = null;
    return ($hasresults == 1);
}

function get_type($sid, $Qnum) {
    $type = -1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SHOW FULL COLUMNS FROM `" . $sid . "`");
    $statement->execute();
    while($row = $statement->fetch()) {
        if ($row['Field'] == $Qnum) $type = $row['Comment'];
    }

    $pdo = null;
    $type = strstr($type, ":", true);
    if (!$type) $type = "unknown";
    return $type;
}

function set_active($id): void {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    try {
        $statement = $pdo->prepare("UPDATE surveys SET isactive=? WHERE id=?");
        $statement->execute([1, $id]);
    }

    catch (Exception $e) {
        $e->getMessage();
        //echo $e;
    }
    $pdo = null;
}

function set_inactive($id): void {
    if (get_active($id) == 1) {
        $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
        try {
            $statement = $pdo->prepare("UPDATE surveys SET isactive=?, inactivesince=? WHERE id=?");
            $statement->execute([0, time(), $id]);
        } catch (Exception $e) {
            $e->getMessage();
            //echo $e;
        }
        $pdo = null;
    }
}

function set_hasresults($id, $hasresults = 1): void {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    try {
        $statement = $pdo->prepare("UPDATE surveys SET hasresults=? WHERE id=?");
        $statement->execute([$hasresults, $id]);
    }

    catch (Exception $e) {
        $e->getMessage();
        //echo $e;
    }
    $pdo = null;
}

function set_hasnoresults($id): void {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    try {
        $statement = $pdo->prepare("UPDATE surveys SET hasresults=? WHERE id=?");
        $statement->execute([0, $id]);
    }

    catch (Exception $e) {
        $e->getMessage();
        //echo $e;
    }
    $pdo = null;
}



function create_survey($id, $qcount, $types, $questions, $options): void {
    $script = "";
    for ($i = 0; $i < $qcount; $i++) {
        if ($types[$i] === "textfeld") $script .= "`" . $i . "` VARCHAR( 250 ) NOT NULL COMMENT 'free:";
        elseif ($types[$i] === "und") $script .= "`" . $i . "` VARCHAR( 50 ) NOT NULL COMMENT 'multi:";
        else $script .= "`" . $i . "` INT( 11 ) NOT NULL COMMENT 'single:";
        $script .= sanitize($questions[$i]) . "; " . sanitize($options[$i]) . "', ";
    }
    $script .= "timestamp DOUBLE NOT NULL, ";
    //$script .= "ip VARCHAR( 50 ) NOT NULL, ";
    $script .= "uid INT( 11 )  NOT NULL";
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("CREATE TABLE IF NOT EXISTS `$id`(" . $script . ");") ;
    try {

        $statement->execute();

    }

    catch (Exception $e) {
        $e->getMessage();
        //echo $e;
    }
    $pdo = null;
}

function get_creator_data($cid) {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM creators WHERE cid = ?");
    $statement->execute([$cid]);
    while($row = $statement->fetch()) {
        $_SESSION['cid'] = $row['cid'];
        $_SESSION['gid'] = $row['gid'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['gmail'] = $row['gmail'];
        $_SESSION['firstname'] = $row['firstname'];
        $_SESSION['familyname'] = $row['familyname'];
        $_SESSION['fullname'] = $row['firstname'] . " " . $row['familyname'];
        $_SESSION['owns'] = $row['owns'];
        $_SESSION['friends'] = $row['friends'];
        $_SESSION['du'] = $row['du'];
        $_SESSION['isadmin'] = $row['isadmin'];
        $_SESSION['since'] = $row['since'];
    }
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE creator = ?");
    $statement->execute([$cid]);
    $i=0;
    $_SESSION['my_creations'] = [];
    while($row = $statement->fetch()) {
        $_SESSION['my_creations'][$i] = $row['id'];
        $i++;
    }
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM surveys WHERE contributors LIKE ?");
    $statement->execute(["%$cid%"]);
    $i=0;
    $_SESSION['my_contributions'] = [];
    while($row = $statement->fetch()) {
        $_SESSION['my_contributions'][$i] = $row['id'];
        $i++;
    }
    $pdo = null;
}

function get_creator_cid($email) {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM creators WHERE email = ?");
    $statement->execute([$email]);
    while($row = $statement->fetch()) {
        return $row['cid'];
    }
    return -1;
}

function create_creator($gid, $email, $gmail, $firstname, $familyname, $password1, $password2, $agb, $gPicAgree, $gPic): bool|int|string
{

    $email = sanitize($email);
    $firstname = sanitize($firstname);
    $familyname = sanitize($familyname);
    $password1 = htmlspecialchars($password1);
    $password2 = htmlspecialchars($password2);
    $isvalid = 0;
    $isadmin = 0;
    $pwdhash = "";
    $since = time();
    $du = 0;
    $du = 1;
    $cid = checkCreatorUniqid(str_replace('.', '', uniqid('', true)));

    if ($agb !== "checked") return "Bitte stimme unseren AGB zu.";

    if ($password1 !== $password2) return "Die Passwörter stimmen nicht überein.";

    if ($gid != "" && checkCreatorGoogleid($gid)) return "Du bist bereits mit Deiner Google-ID bei uns registriert.";
    if (checkCreatorEmail($email)) return "Du bist bereits mit Deiner E-Mail-Adresse bei uns registriert.";

    if ($gid === "") {
        $pwdIsBad = check_password_is_bad($password1);
        if ($pwdIsBad != '') return $pwdIsBad;
        $pwdhash = hashPassword($password1);
    }

    if ($gPicAgree === "checked") saveCreatorPicture($cid, $gPic);

    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("INSERT INTO creators (cid, gid, email, gmail, firstname, familyname, pwdhash, du, isvalid, isadmin, since) VALUES (?,?,?,?, ?,?,?,?,?,?,?)");
    $statement->execute([$cid, $gid, $email, $gmail, $firstname, $familyname, $pwdhash, $du, $isvalid, $isadmin, $since]);

    $pdo = null;

    sendCreatorConfirmation($cid, $email);

    return "OK";
}

function saveCreatorPicture($cid, $picURL) {
    // Get the size of the file
    $headers = get_headers($picURL, 1);
    $size = isset($headers['Content-Length']) ? intval($headers['Content-Length']) : 0;
    // Get the file extension using exif_imagetype function
    $fileType = exif_imagetype($picURL);
    if ($fileType && $size <= 10485760) {
        // Map file type to extension
        $extension = image_type_to_extension($fileType);
        // Create new file name
        $newFileName = $cid . $extension;
        // Save image to /images/creatorPics folder
        file_put_contents('./images/creatorPics/' . $newFileName, file_get_contents($picURL));
    }
}

function hashPassword($pwd) {
    $options = [
        'memory_cost' => 1 << 17,
        'time_cost' => 9,
        'threads' => 2,
        'salt' => random_bytes(16)
    ];
    return password_hash($pwd, PASSWORD_ARGON2ID, $options);
}

function verifyPassword($cid, $pwd) {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT pwdhash FROM creators WHERE cid = ?");
    $statement->execute([$cid]);
    while($row = $statement->fetch()) {
        $hash = $row['pwdhash'];
    }
    alert("", $hash);
    if (isset($hash)) return password_verify($pwd, $hash);
    else return false;
}

function setPassword($cid, $pwd) {
    $pwdIsBad = check_password_is_bad($pwd);
    if ($pwdIsBad != '') return $pwdIsBad;
    $pwdhash = hashPassword($pwd);
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("UPDATE creators SET pwdhash = ? WHERE cid = ?");
    $statement->execute([$pwdhash, $cid]);
    return "OK";
}

function checkCreatorEmail($email) {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM creators");
    $statement->execute();
    $pdo = null;
    while($row = $statement->fetch()) {
        if ($email == $row['email']) return true;
    }
    return false;
}

function checkCreatorGoogleid($id) {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM creators");
    $statement->execute();
    $pdo = null;
    while($row = $statement->fetch()) {
        if ($id == $row['gid']) return true;
    }
    return false;
}

function checkCreatorUniqid($id) {
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM creators");
    $statement->execute();
    $pdo = null;
    while($row = $statement->fetch()) {
        if ($id == $row['cid']) return checkCreatorUniqid(str_replace('.', '', uniqid('', true)));
    }
    return $id;
}



function create_user($email, $mailhash, $target) {
    $email = sanitize($email);
    $databasemails="";
    $isvalid = 0;
    require_once("databasemails.php");
    if (str_contains($databasemails, $email)) $isvalid = 1;
    $du = 0;
    if ($target == "studs") $du = 1;
    $id = 1;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM users ORDER BY uid DESC LIMIT 1");
    $statement->execute();
    while($row = $statement->fetch()) {
        $id = $row['uid'] + 1;
    }
    $statement = $pdo->prepare("INSERT INTO users (uid, mailhash, du, isvalid, since) VALUES (?,?,?,?,?)");
    $statement->execute([$id, $mailhash, $du, $isvalid, time()]);

    $pdo = null;
    return $id;
}

function fill_survey($sid, $uid, $answers) {
    $already_submitted = 0;
    $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
    $statement = $pdo->prepare("SELECT * FROM `" . $sid . "` WHERE uid = ?");
    $statement->execute(array($uid));
    while($row = $statement->fetch()) {
        $already_submitted = 1;
    }
    if ($already_submitted === 0) {
        $count = sizeof($answers);
        $rownames = "";
        $valplaceholder = "?";
        $vals[0] = "";

        for ($i = 0; $i < sizeof($answers); $i++) {
            for ($j = 0; $j < sizeof($answers[$i]); $j++) {
                if ($j == 0) $vals[$i] = sanitize($answers[$i][$j]);
                else $vals[$i] .= "; " . sanitize($answers[$i][$j]);
            }
        }
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) $rownames .= "`" . $i . "`";
            else $rownames .= ", `" . $i . "`";
            $valplaceholder .= ",?";
        }



        $rownames .= ", timestamp, uid";
        $valplaceholder .= ",?";
        array_push($vals, time(), $uid);

        /*
                    for ($i = 0; $i < sizeof($vals); $i++) {
                        echo $vals[$i] . "<br>";
                    }
                    echo $rownames . "<br>";
                    echo $valplaceholder . "<br>";
        */


        try {
            $query = "INSERT INTO `" . $sid . "`(" . $rownames . ") VALUES (" . $valplaceholder . ")";
            //$pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
            $statement = $pdo->prepare($query);
            $statement->execute($vals);
            $pdo = null;
        }

        catch (MySQLDuplicateKeyException $e) {
            // duplicate entry exception
            $e->getMessage();
        }
        catch (MySQLException $e) {
            // other mysql exception (not duplicate key entry)
            $e->getMessage();
        }
        catch (Exception $e) {
            // not a MySQL exception
            $e->getMessage();
        }
        echo $e;
    }
    return $already_submitted;
}

function read_surveys($sid) {
    try {


        date_default_timezone_set("Europe/Berlin");

        $since = "";
        $name = "";
        $questions = [];
        $answers = [];
        $users = [];

        $pdo = new PDO('mysql:host=localhost;dbname=eh-umfragen', $GLOBALS["dbuser"], $GLOBALS["dbpwd"]);
        $statement = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
        $statement->execute(array($sid));
        while ($row = $statement->fetch()) {
            $since = $row['since'];
            $name = $row['name'];
        }
        //echo $name;
        //echo $since;
        $datum = date("d.m.Y", $since);
        $uhrzeit = date("H:i", $since) . " Uhr";
        //echo $datum," - ",$uhrzeit," Uhr";

        $statement = $pdo->prepare("SHOW FULL COLUMNS FROM `" . $sid . "`");
        $statement->execute();
        while ($row = $statement->fetch()) {
            if ($row['Comment'] != "") array_push($questions, iconv("UTF-8", "Windows-1252", $row['Comment']));
        }
        $questions[sizeof($questions)] = "Umfrage abgegeben";
        $questions[sizeof($questions)] = "E-Mail ist validiert";
        /*
            for ($i = 0; $i < sizeof($questions); $i++) {
                echo $i . " ";
                echo $questions[$i] . "<br>";
            }
        */
        $i = 0;
        $statement = $pdo->prepare("SELECT * FROM `" . $sid . "`");
        $statement->execute();
        while ($row = $statement->fetch()) {
            for ($j = 0; $j < sizeof($questions) - 1; $j++) {
                $answers[$i][$j] = iconv("UTF-8", "Windows-1252", $row[$j]);
                /*
                echo $i . " ";
                echo $j . " ";
                echo $row[$j] . "<br>";
                */
            }
            $answers[$i][sizeof($questions) - 2] = date("d.m.Y", $row["timestamp"]) . " " . date("H:i", $row["timestamp"]) . " Uhr";
            $answers[$i][sizeof($questions) - 1] = $row["uid"];
            $i++;
        }


        $statement = $pdo->prepare("SELECT * FROM users");
        $statement->execute();
        $i = 0;
        while ($row = $statement->fetch()) {
            $users[$i][0] = $row["uid"];
            $users[$i][1] = $row["isvalid"];
            $i++;
        }
        for ($i = 0; $i < sizeof($answers); $i++) {
            //compare two arrays- GOOGLE!!!
            for ($j = 0; $j < sizeof($users); $j++) {
                if ($users[$j][0] == $answers[$i][sizeof($answers[$i]) - 1]) {
                    $answers[$i][sizeof($answers[$i]) - 1] = $users[$j][1];
                    break;
                }
            }
        }


        //ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large

        //create a file
        $filename = "export_" . date("Y.m.d") . "_" . $name . ".csv";
        $csv_file = fopen('results/' . $filename, 'w');


        //iconv("UTF-8", "Windows-1252"

        // The column headings of your .csv file
        $title_row = array("Umfragetitel:", iconv("UTF-8", "Windows-1252", $name), "Online seit:", $datum, $uhrzeit);
        fputcsv($csv_file, $title_row, ';');
        $header_row = $questions;

        fputcsv($csv_file, $header_row, ';');

        for ($i = 0; $i < sizeof($answers); $i++) {
            $row = [];
            for ($j = 0; $j < sizeof($answers[$i]); $j++) {
                $row[$j] = $answers[$i][$j];
            }
            fputcsv($csv_file, $row, ';');
        }


        fclose($csv_file);
        chmod('results/' . $filename, 0775);
        $pdo = null;
    }
    catch (MySQLDuplicateKeyException $e) {
        // duplicate entry exception
        $e->getMessage();
        return $e;
    }
    catch (MySQLException $e) {
        // other mysql exception (not duplicate key entry)
        $e->getMessage();
        return $e;
    }
    catch (Exception $e) {
        // not a MySQL exception
        $e->getMessage();
        return $e;
    }
    return $filename . " stored";
}
if (isset($_GET["storeresults"]) && intval($_GET["storeresults"]) > 0) echo read_surveys(intval($_GET["storeresults"]));

//echo get_survey_id("genial");
//echo set_survey_id("genial");
//create_survey("485","3",array("hallöchen mein guter", "komische fragen hier!", "noch ne Frage"),array("und", "oder", "offen"), array("hier; dort", "ja; nein; vielleicht", "offen"));

//echo get_type(1, 2);

//echo checkCreatorUniqid(str_replace('.', '', uniqid('', true)));
?>