<?php

namespace assets\php\classes;

class Responses extends DatabaseHandler
{
    // Add a response
    public function addResponse($survey_id, $question_id, $choice_id, $response_text, $user_id)
    {
        $stmt = $this->connection->prepare("INSERT INTO responses (survey_id, question_id, choice_id, response_text, user_id) VALUES (:survey_id, :question_id, :choice_id, :response_text, :user_id)");
        return $stmt->execute([
            ':survey_id' => $survey_id,
            ':question_id' => $question_id,
            ':choice_id' => $choice_id,
            ':response_text' => $response_text,
            ':user_id' => $user_id
        ]);
    }

// Get all responses by user
    public function getResponsesBy($string, $type = "survey_id"): false|array
    {
        $stmt = $this->connection->prepare("SELECT * FROM responses WHERE $type = ?");
        $stmt->execute([$string]);
        return $stmt->fetchAll();
    }

    // Update response_text
    public function updateResponseText($id, $response_text)
    {
        $stmt = $this->connection->prepare("UPDATE responses SET response_text = :response_text WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':response_text' => $response_text
        ]);
    }

    // Delete a response by ID
    public function deleteResponseById($id)
    {
        $stmt = $this->connection->prepare("DELETE FROM responses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}