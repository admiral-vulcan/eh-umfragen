<?php
namespace assets\php\classes;

use PDO;

class Collaborators extends DatabaseHandler
{

    public function getCreatorCollaborations($creator_id): false|array
    {
        $query = "SELECT survey_id FROM collaborators WHERE creator_id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$creator_id]);
        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}