<?php
/**
 * Class Surveys
 *
 * Provides functionality for handling survey-related database operations.
 */

namespace EHUmfragen\DatabaseModels;

use EHUmfragen\DatabaseHandler;
use PDO;

class Surveys extends DatabaseHandler {

    /**
     * Add a new survey
     *
     * @param string $creator_id The creator ID
     * @param string $title The survey title
     * @param string $subtitle The survey subtitle
     * @param string $description The survey description
     * @param string $subdescription The survey subdescription (optional)
     * @param string $target_group The survey target group (optional)
     * @return false|string The inserted survey ID, or false on failure
     */
    public function addSurvey(string $creator_id, string $title, string $subtitle, string $description, string $subdescription = "", string $target_group = "no_restriction"): false|string
    {
        $query = "INSERT INTO surveys (creator_id, title, subtitle, description, subdescription, target_group) VALUES (?, ?, ?, ?, ?, ?)";
        $statement = $this->connection->prepare($query);
        $statement->execute([$creator_id, $title, $subtitle, $description, $subdescription, $target_group]);
        return $this->connection->lastInsertId();
    }

    /**
     * Get a survey by ID
     *
     * @param int $id The survey ID
     * @return array|null The survey data as an associative array, or null if not found
     */
    public function getSurvey(int $id): array|null
    {
        $query = "SELECT * FROM surveys WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get survey IDs by a specified value and type
     *
     * @param string $string The value to match
     * @param string $type The column to match against. Default is 'creator_id'
     * @return false|array An array of matching survey IDs, or false on failure
     */
    public function getSurveysIdsBy(string $string, string $type = "creator_id"): false|array
    {
        $query = "SELECT id FROM surveys WHERE $type = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$string]);
        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Update survey details (title, subtitle, description, subdescription, and target group) for a specified survey ID
     *
     * @param int $id The survey ID
     * @param string $title The new title for the survey
     * @param string $subtitle The new subtitle for the survey
     * @param string $description The new description for the survey
     * @param string $subdescription The new subdescription for the survey
     * @param string $target_group The new target group for the survey
     * @return bool True if the survey details were updated successfully, False otherwise
     */
    public function changeSurvey(int $id, string $title, string $subtitle, string $description, string $subdescription, string $target_group): bool
    {
        $query = "UPDATE surveys SET title = ?, subtitle = ?, description = ?, subdescription = ?, target_group = ? WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$title, $subtitle, $description, $subdescription, $target_group, $id]);
        return $statement->rowCount() > 0;
    }

    /**
     * Get a specific key value for a survey
     *
     * @param int $id The survey ID
     * @param string $key The setting name. Accepted values: 'title', 'subtitle', 'description', 'subdescription', 'target_group', 'is_active', 'is_draft', 'has_results', 'activated_at', 'inactivated_at', 'results_received_at'
     * @return mixed The setting value
     */
    public function getSetting(int $id, string $key): mixed
    {
        $query = "SELECT $key FROM surveys WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$id]);
        return $statement->fetchColumn();
    }

    /**
     * Set a specific key value for a survey
     *
     * @param int $id The survey ID
     * @param string $key The setting name. Accepted values: 'title', 'subtitle', 'description', 'subdescription', 'target_group', 'is_active', 'is_draft', 'has_results', 'activated_at', 'inactivated_at', 'results_received_at'
     * @param mixed $value The new setting value
     * @return bool True if the setting was updated, false otherwise
     */
    public function setSurveyKey(int $id, string $key, mixed $value): bool
    {
        $query = "UPDATE surveys SET $key = ? WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$value, $id]);
        return $statement->rowCount() > 0;
    }

    /**
     * Get a list of survey IDs created by a specified creator
     *
     * @param string $creator_id The creator ID
     * @return string A semicolon-separated list of survey IDs created by the creator
     */
    public function getCreations(string $creator_id): string
    {
        $sql = "SELECT id FROM surveys WHERE creator_id = :creator_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':creator_id', $creator_id);
        $stmt->execute();
        $ownedSurveys = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return implode(';', $ownedSurveys);
    }

    /**
     * Get all survey IDs.
     *
     * @return int[]
     */
    public function getAllSurveyIds(): array
    {
        $stmt = $this->connection->prepare("SELECT id FROM surveys ORDER BY id ASC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Get the creator_id of a survey.
     *
     * @param string $survey_id The survey ID.
     * @return string|null The creator_id if found, null otherwise.
     */
    public function getCreatorId(string $survey_id): ?string
    {
        $query = "SELECT creator_id FROM surveys WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$survey_id]);
        return $statement->fetchColumn();
    }
}