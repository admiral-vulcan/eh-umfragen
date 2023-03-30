<?php
/**
 * Class Collaborators
 *
 * Provides functionality for handling collaborator-related database operations.
 */
namespace assets\php\classes;

use PDO;

class Collaborators extends DatabaseHandler
{
    /**
     * Add a collaborator.
     *
     * @param string $survey_id The survey ID.
     * @param string $creator_id The creator ID.
     * @return bool The result of the execute statement.
     */
    public function addCollaborator(string $survey_id, string $creator_id): bool
    {
        $stmt = $this->connection->prepare("INSERT INTO collaborators (survey_id, creator_id) VALUES (:survey_id, :creator_id)");
        return $stmt->execute([
            ':survey_id' => $survey_id,
            ':creator_id' => $creator_id
        ]);
    }

    /**
     * Get collaborations by creator_id.
     *
     * @param string $creator_id The creator ID.
     * @return false|array An array of survey IDs or false if none found.
     */
    public function getCollaborations(string $creator_id): false|array
    {
        $stmt = $this->connection->prepare("SELECT survey_id FROM collaborators WHERE creator_id = :creator_id");
        $stmt->execute([':creator_id' => $creator_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Get collaborators by survey_id.
     *
     * @param string $survey_id The survey ID.
     * @return false|array An array of creator IDs or false if none found.
     */
    public function getCollaborators(string $survey_id): false|array
    {
        $stmt = $this->connection->prepare("SELECT creator_id FROM collaborators WHERE survey_id = :survey_id");
        $stmt->execute([':survey_id' => $survey_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Remove a collaborator.
     *
     * @param string $survey_id The survey ID.
     * @param string $creator_id The creator ID.
     * @return bool The result of the execute statement.
     */
    public function removeCollaborator(string $survey_id, string $creator_id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM collaborators WHERE survey_id = :survey_id AND creator_id = :creator_id");
        return $stmt->execute([
            ':survey_id' => $survey_id,
            ':creator_id' => $creator_id
        ]);
    }
}
