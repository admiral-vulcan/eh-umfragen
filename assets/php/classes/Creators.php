<?php
namespace assets\php\classes;
use PDO;

class Creators extends DatabaseHandler {

    // Get creator by creator_id, google_id or email
    private function getCreatorBy($value, $type = "creator_id") {
        $stmt = $this->connection->prepare("SELECT * FROM creators WHERE $type = :value");
        $stmt->execute([':value' => $value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fillSession($creator_id) {
        $creatorData = $this->getCreatorBy($creator_id);
        $surveys = new Surveys();
        $collaborators = new Collaborators();

        $_SESSION['creator_id'] = $creatorData['creator_id'];
        $_SESSION['google_id'] = $creatorData['google_id'];
        $_SESSION['email'] = $creatorData['email'];
        $_SESSION['gmail'] = $creatorData['gmail'];
        $_SESSION['firstname'] = $creatorData['firstname'];
        $_SESSION['familyname'] = $creatorData['familyname'];
        $_SESSION['fullname'] = $creatorData['firstname'] . " " . $creatorData['familyname'];
        $_SESSION['owns'] = "";
        $_SESSION['friends'] = $creatorData['friends'];
        $_SESSION['du'] = !$creatorData['formal']; //deprecated; for backwards compatibility reasons
        $_SESSION['formal'] = $creatorData['formal'];
        $_SESSION['isadmin'] = $creatorData['is_admin']; //deprecated; for backwards compatibility reasons
        $_SESSION['is_admin'] = $creatorData['is_admin'];
        $_SESSION['since'] = $creatorData['created_at']; //deprecated; for backwards compatibility reasons
        $_SESSION['created_at'] = $creatorData['created_at'];

        $_SESSION['creatorSurveys'] = $surveys->getCreatorSurveys($creator_id);
        $_SESSION['creatorCollaborations'] = $collaborators->getCreatorCollaborations($creator_id);
    }

    public function addCreator($google_id, $email, $gmail, $firstname, $familyname, $password1, $password2, $agb, $gPicAgree, $gPic): bool|int|string {
        $email = sanitize($email);
        $firstname = sanitize($firstname);
        $familyname = sanitize($familyname);
        $password1 = htmlspecialchars($password1);
        $password2 = htmlspecialchars($password2);
        $isvalid = 0;
        $isadmin = 0;
        $pwdhash = "";
        $since = time();
        $du = 1;
        $creator_id = checkCreatorUniqid(str_replace('.', '', uniqid('', true)));

        if ($agb !== "checked") return "Bitte stimme unseren AGB zu.";

        if ($password1 !== $password2) return "Die Passwörter stimmen nicht überein.";

        if ($google_id != "" && checkCreatorGoogleid($google_id)) return "Du bist bereits mit Deiner Google-ID bei uns registriert.";
        if (checkCreatorEmail($email)) return "Du bist bereits mit Deiner E-Mail-Adresse bei uns registriert.";

        if ($google_id === "") {
            $pwdIsBad = check_password_is_bad($password1);
            if ($pwdIsBad != '') return $pwdIsBad;
            $pwdhash = hashPassword($password1);
        }

        if ($gPicAgree === "checked") saveCreatorPicture($creator_id, $gPic);

        $statement = $this->connection->prepare("INSERT INTO creators (creator_id, google_id, email, gmail, firstname, familyname, pwdhash, formal, valid, is_admin, created_at) VALUES (?,?,?,?, ?,?,?,?,?,?,?)");
        $statement->execute([$creator_id, $google_id, $email, $gmail, $firstname, $familyname, $pwdhash, $du, $isvalid, $isadmin, $since]);

        return "mail sent: ".sendCreatorConfirmation($creator_id, $email); //returns OK or ERROR or exception message
    }

    // Get creator validation
    public function getCreatorValidation($string, $type = 'creator_id'): bool
    {
        $creator = $this->getCreatorBy($string, $type);
        return $creator && $creator['valid'] == 1;
    }

    // Validate creator
    public function validateCreator($creator_id) {
        $stmt = $this->connection->prepare("UPDATE creators SET valid = 1 WHERE creator_id = :creator_id");
        $stmt->execute([':creator_id' => $creator_id]);
    }

    // Get creator_id by email or google_id
    public function getCreatorId($string, $type = 'email') {
        $creator = $this->getCreatorBy($string, $type);
        return $creator ? $creator['creator_id'] : false;
    }

    // ... other getters and setters ...
}
