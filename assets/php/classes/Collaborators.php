<?php

namespace assets\php\classes;

class Collaborators extends DatabaseHandler
{
    // Add a collaborator
    public function addCollaborator($survey_id, $creator_id)
    {
        $stmt = $this->connection->prepare("INSERT INTO collaborators (survey_id, creator_id) VALUES (:survey_id, :creator_id)");
        return $stmt->execute([
            ':survey_id' => $survey_id,
            ':creator_id' => $creator_id
        ]);
    }

    // Get collaborations by creator_id
    public function getCollaborations($creator_id): false|array
    {
        $stmt = $this->connection->prepare("SELECT survey_id FROM collaborators WHERE creator_id = :creator_id");
        $stmt->execute([':creator_id' => $creator_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    // Get collaborators by survey_id
    public function getCollaborators($survey_id): false|array
    {
        $stmt = $this->connection->prepare("SELECT creator_id FROM collaborators WHERE survey_id = :survey_id");
        $stmt->execute([':survey_id' => $survey_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    // Remove a collaborator
    public function removeCollaborator($survey_id, $creator_id)
    {
        $stmt = $this->connection->prepare("DELETE FROM collaborators WHERE survey_id = :survey_id AND creator_id = :creator_id");
        return $stmt->execute([
            ':survey_id' => $survey_id,
            ':creator_id' => $creator_id
        ]);
    }
}
