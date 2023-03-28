<?php

namespace assets\php\classes;

use PDO;

class Users extends DatabaseHandler {

    // Get user by ID
    public function getUserById($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch();
    }

    // Add user
    public function addUser($user_group, $mailhash, $formal = 0, $valid = 0): false|string
    {
        $stmt = $this->connection->prepare("INSERT INTO users (user_group, mailhash, formal, valid) VALUES (:user_group, :mailhash, :formal, :valid)");
        $stmt->execute([':user_group' => $user_group, ':mailhash' => $mailhash, ':formal' => $formal, ':valid' => $valid]);
        return $this->connection->lastInsertId();
    }

    // Get user ID by mailhash
    public function getUserIdByMailHash($mailhash) {
        $stmt = $this->connection->prepare("SELECT user_id FROM users WHERE mailhash = :mailhash");
        $stmt->execute([':mailhash' => $mailhash]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['user_id'];
        } else {
            return null;
        }
    }

    // Update user
    public function updateUser($user_id, $user_group, $mailhash, $formal, $valid) {
        $stmt = $this->connection->prepare("UPDATE users SET user_group = :user_group, mailhash = :mailhash, formal = :formal, valid = :valid, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id, ':user_group' => $user_group, ':mailhash' => $mailhash, ':formal' => $formal, ':valid' => $valid]);
    }

    // Validate user
    public function validateUser($user_id) {
        $stmt = $this->connection->prepare("UPDATE users SET valid = 1, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    }

    // Get user validation status
    public function getUserValidation($user_id): bool
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

    // Get all submitted surveys by user
    public function getSurveysByUser($user_id): false|array
    {
        $stmt = $this->connection->prepare("SELECT s.* FROM surveys s JOIN responses r ON s.id = r.survey_id WHERE r.user_id = :user_id GROUP BY s.id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    // Get all responses by user
    public function getResponsesByUser($user_id): false|array
    {
        $stmt = $this->connection->prepare("SELECT r.* FROM responses r WHERE r.user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }
}
