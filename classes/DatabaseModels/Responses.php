<?php
/**
 * Class Responses
 *
 * Provides functionality for handling response-related database operations.
 */

namespace EHUmfragen\DatabaseModels;

use EHUmfragen\DatabaseHandler;

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
        $stmt = $this->connection->prepare("SELECT * FROM responses WHERE $type = ? ORDER BY id ASC");
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

    /**
     * Count the number of unique users for a given survey_id.
     *
     * @param int $survey_id
     * @return int
     */
    public function countUniqueUsersBySurveyId(int $survey_id): int
    {
        $stmt = $this->connection->prepare("SELECT COUNT(DISTINCT user_id) FROM responses WHERE survey_id = :survey_id");
        $stmt->execute([':survey_id' => $survey_id]);
        return (int)$stmt->fetchColumn();
    }

    public function countResponsesByChoiceId(int $choice_id): int
    {
        $sql = "SELECT COUNT(*) FROM responses WHERE choice_id = :choice_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':choice_id', $choice_id, \PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    public function countUniqueUsersByChoice(int $question_id, int $question_choice): int
    {
        $sql = "SELECT COUNT(DISTINCT user_id) FROM responses WHERE question_id = :question_id AND choice_id = :question_choice";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':question_id', $question_id, \PDO::PARAM_INT);
        $stmt->bindParam(':question_choice', $question_choice, \PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    public function countResponsesByChoiceIdAndReferenceChoice(int $choice_id, int $ref_question_id, int $ref_question_choice): int
    {
        $sql = "SELECT COUNT(*) FROM responses AS r1 JOIN responses AS r2 ON r1.user_id = r2.user_id WHERE r1.choice_id = :choice_id AND r2.question_id = :ref_question_id AND r2.choice_id = :ref_question_choice";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':choice_id', $choice_id, \PDO::PARAM_INT);
        $stmt->bindParam(':ref_question_id', $ref_question_id, \PDO::PARAM_INT);
        $stmt->bindParam(':ref_question_choice', $ref_question_choice, \PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    public function getResponsesByQuestionIdAndReferenceChoice(int $question_id, int $ref_question_id, int $ref_question_choice): array
    {
        $sql = "SELECT r1.* FROM responses AS r1 JOIN responses AS r2 ON r1.user_id = r2.user_id WHERE r1.question_id = :question_id AND r2.question_id = :ref_question_id AND r2.choice_id = :ref_question_choice";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':question_id', $question_id, \PDO::PARAM_INT);
        $stmt->bindParam(':ref_question_id', $ref_question_id, \PDO::PARAM_INT);
        $stmt->bindParam(':ref_question_choice', $ref_question_choice, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}