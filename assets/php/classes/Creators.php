<?php
/**
 * Class Creators
 *
 * Provides functionality for handling creator-related database operations.
 */
namespace assets\php\classes;

use PDO;

class Creators extends DatabaseHandler {

    /**
     * Get creator by creator_id, google_id, or email.
     *
     * @param string $value The value to search for.
     * @param string $type The type of value to search for, either 'creator_id', 'google_id', or 'email'.
     * @return array|null The creator's data as an associative array, or null if not found.
     */
    private function getCreatorBy(string $value, string $type = "creator_id"): ?array {
        $stmt = $this->connection->prepare("SELECT * FROM creators WHERE $type = :value");
        $stmt->execute([':value' => $value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fill the session with creator data.
     *
     * @param string $creator_id The creator ID.
     */
    public function fillSession(string $creator_id): void {
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

        $_SESSION['creatorSurveys'] = $surveys->getCreations($creator_id);
        $_SESSION['creatorCollaborations'] = $collaborators->getCollaborations($creator_id);
    }

    /**
     * Add a new creator to the database.
     *
     * @param string $google_id The creator's Google ID.
     * @param string $email The creator's email address.
     * @param string $gmail The creator's Gmail address.
     * @param string $firstname The creator's first name.
     * @param string $familyname The creator's family name.
     * @param string $password1 The creator's password.
     * @param string $password2 The creator's password confirmation.
     * @param string $agb The creator's agreement to the terms and conditions.
     * @param string $gPicAgree The creator's agreement to use their Google picture.
     * @param string $gPic The creator's Google picture URL.
     * @return bool|int|string Returns true if successful, or an error message.
     */
    public function addCreator(string $google_id, string $email, string $gmail, string $firstname, string $familyname, string $password1, string $password2, string $agb, string $gPicAgree, string $gPic): bool|int|string
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

    /**
     * Get creator validation status.
     *
     * @param string $string The value to search for.
     * @param string $type The type of value to search for, either 'creator_id' or 'email'.
     * @return bool True if the creator is validated, false otherwise.
     */
    public function getCreatorValidation(string $string, string $type = 'creator_id'): bool
    {
        $creator = $this->getCreatorBy($string, $type);
        return $creator && $creator['valid'] == 1;
    }

    /**
     * Validate a creator by setting the valid flag to 1.
     *
     * @param string $creator_id The creator ID.
     */
    public function validateCreator(string $creator_id): void
    {
        $stmt = $this->connection->prepare("UPDATE creators SET valid = 1 WHERE creator_id = :creator_id");
        $stmt->execute([':creator_id' => $creator_id]);
    }

    /**
     * Get the creator_id by email or google_id.
     *
     * @param string $string The value to search for, either email or google_id.
     * @param string $type The type of value to search for, either 'email' or 'google_id'.
     * @return string|bool The creator_id if found, false otherwise.
     */
    public function getCreatorId(string $string, string $type = 'email'): string|false
    {
        if ($type !== "email" && $type !== "google_id") return false;
        $creator = $this->getCreatorBy($string, $type);
        return $creator ? $creator['creator_id'] : false;
    }
}
