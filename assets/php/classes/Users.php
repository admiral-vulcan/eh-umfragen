<?php
/**
 * Class Users
 *
 * Provides functionality for handling user-related database operations.
 */
namespace assets\php\classes;

use PDO;

class Users extends DatabaseHandler {

    /**
     * Get user by ID.
     *
     * @param string $user_id The user ID.
     * @return array|false|null The user's data as an associative array, or false/null if not found.
     */
    public function getUserById(string $user_id): array|false|null
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch();
    }

    /**
     * Add a new user to the database.
     *
     * @param string $user_group The user group.
     * @param string $mailhash The mailhash.
     * @param int $formal The formal (optional, defaults to 0).
     * @param int $valid The valid (optional, defaults to 0).
     * @return false|string The last inserted ID or false on failure.
     */
    public function addUser(string $user_group, string $mailhash, int $formal = 0, int $valid = 0): false|string {
        $stmt = $this->connection->prepare("INSERT INTO users (user_group, mailhash, formal, valid) VALUES (:user_group, :mailhash, :formal, :valid)");
        $stmt->execute([':user_group' => $user_group, ':mailhash' => $mailhash, ':formal' => $formal, ':valid' => $valid]);
        return $this->connection->lastInsertId();
    }

    /**
     * Get user ID by mailhash.
     *
     * @param string $mailhash The mailhash.
     * @return string|null The user ID or null if not found.
     */
    public function getUserIdByMailHash(string $mailhash): ?string
    {
        $stmt = $this->connection->prepare("SELECT user_id FROM users WHERE mailhash = :mailhash");
        $stmt->execute([':mailhash' => $mailhash]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['user_id'];
        } else {
            return null;
        }
    }

    /**
     * Update user.
     *
     * @param string $user_id The user ID.
     * @param string $user_group The user group.
     * @param string $mailhash The mailhash.
     * @param int $formal The formal.
     * @param int $valid The valid.
     */
    public function updateUser(string $user_id, string $user_group, string $mailhash, int $formal, int $valid): void
    {
        $stmt = $this->connection->prepare("UPDATE users SET user_group = :user_group, mailhash = :mailhash, formal = :formal, valid = :valid, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id, ':user_group' => $user_group, ':mailhash' => $mailhash, ':formal' => $formal, ':valid' => $valid]);
    }

    /**
     * Validate user.
     *
     * @param string $user_id The user ID.
     */
    public function validateUser(string $user_id): void
    {
        $stmt = $this->connection->prepare("UPDATE users SET valid = 1, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    }

    /**
     * Get user validation status.
     *
     * @param string $user_id The user ID.
     * @return bool The user validation status (true if valid, false otherwise).
     */
    public function getUserValidation(string $user_id): bool
    {
        $stmt = $this->connection->prepare("SELECT valid FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return (int)$result['valid'] === 1;
        } else {
            return false;
        }
    }
}
