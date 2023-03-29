<?php
namespace assets\php\classes;
use PDO;

class Surveys extends DatabaseHandler {

    public function addSurvey($creator_id, $title, $subtitle = "", $description = "", $subdescription = "", $target_group = ""): false|string
    {
        $query = "INSERT INTO surveys (creator_id, title, subtitle, description, subdescription, target_group) VALUES (?, ?, ?, ?, ?, ?)";
        $statement = $this->connection->prepare($query);
        $statement->execute([$creator_id, $title, $subtitle, $description, $subdescription, $target_group]);
        return $this->connection->lastInsertId();
    }

    public function getSurvey($id)
    {
        $query = "SELECT * FROM surveys WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getSurveysIdBy($string, $type = "creator_id"): false|array
    {
        $query = "SELECT id FROM surveys WHERE $type = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$string]);
        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function changeSurvey($id, $title, $subtitle, $description, $subdescription, $target_group): bool
    {
        $query = "UPDATE surveys SET title = ?, subtitle = ?, description = ?, subdescription = ?, target_group = ? WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$title, $subtitle, $description, $subdescription, $target_group, $id]);
        return $statement->rowCount() > 0;
    }

    public function getSetting($id, $setting)
    {
        $query = "SELECT $setting FROM surveys WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$id]);
        return $statement->fetchColumn();
    }

    public function setSetting($id, $setting, $value): bool
    {
        $query = "UPDATE surveys SET $setting = ? WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$value, $id]);
        return $statement->rowCount() > 0;
    }

    // Get owned surveys
    public function getCreatorSurveys($creator_id): string
    {
        $sql = "SELECT id FROM surveys WHERE creator_id = :creator_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':creator_id', $creator_id);
        $stmt->execute();
        $ownedSurveys = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return implode(';', $ownedSurveys);
    }
}
