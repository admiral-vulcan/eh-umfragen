<?php
/**
 * Class Responses
 *
 * Provides functionality for handling response-related database operations.
 */
namespace assets\php\classes;

class Responses extends DatabaseHandler
{
    /**
     * Add a response.
     *
     * @param int $survey_id
     * @param int $question_id
     * @param int $choice_id
     * @param string $response_text
     * @param int $user_id
     * @return bool
     */
    public function addResponse(int $survey_id, int $question_id, int $choice_id, string $response_text, int $user_id): bool
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

    /**
     * Get all responses by user.
     *
     * @param string $string
     * @param string $type
     * @return false|array
     */
    public function getResponsesBy(string $string, string $type = "survey_id"): false|array
    {
        $stmt = $this->connection->prepare("SELECT * FROM responses WHERE $type = ?");
        $stmt->execute([$string]);
        return $stmt->fetchAll();
    }

    /**
     * Update response_text.
     *
     * @param int $id
     * @param string $response_text
     * @return bool
     */
    public function updateResponseText(int $id, string $response_text): bool
    {
        $stmt = $this->connection->prepare("UPDATE responses SET response_text = :response_text WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':response_text' => $response_text
        ]);
    }

    /**
     * Delete a response by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteResponseById(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM responses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}