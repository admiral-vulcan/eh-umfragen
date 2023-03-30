<?php
/**
 * Class Questions
 *
 * Provides functionality for handling question-related database operations.
 */

namespace EHUmfragen\DatabaseModels;

use EHUmfragen\DatabaseHandler;
use PDO;

class Questions extends DatabaseHandler
{
    /**
     * Add a question.
     *
     * @param int $survey_id
     * @param string $question_text
     * @param string $question_type
     * @param int|null $follow_up_question_id
     * @param int|null $follow_up_choice_id
     * @return int
     */
    public function addQuestion(int $survey_id, string $question_text, string $question_type, ?int $follow_up_question_id = null, ?int $follow_up_choice_id = null): int
    {
        $stmt = $this->connection->prepare("INSERT INTO questions (survey_id, question_text, question_type, follow_up_question_id, follow_up_choice_id) VALUES (:survey_id, :question_text, :question_type, :follow_up_question_id, :follow_up_choice_id)");
        $stmt->execute([
            ':survey_id' => $survey_id,
            ':question_text' => $question_text,
            ':question_type' => $question_type,
            ':follow_up_question_id' => $follow_up_question_id,
            ':follow_up_choice_id' => $follow_up_choice_id,
        ]);

        return $this->connection->lastInsertId();
    }

    /**
     * Get a question by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getQuestionById(int $id): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM questions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result : null;
    }

    /**
     * Get all questions for a survey.
     *
     * @param int $survey_id
     * @return array
     */
    public function getQuestionsBySurveyId(int $survey_id): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM questions WHERE survey_id = :survey_id");
        $stmt->execute([':survey_id' => $survey_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a question.
     *
     * @param int $id
     * @param int $survey_id
     * @param string $question_text
     * @param string $question_type
     * @param int|null $follow_up_question_id
     * @param int|null $follow_up_choice_id
     */
    public function updateQuestion(int $id, int $survey_id, string $question_text, string $question_type, ?int $follow_up_question_id = null, ?int $follow_up_choice_id = null): void
    {
        $stmt = $this->connection->prepare("UPDATE questions SET survey_id = :survey_id, question_text = :question_text, question_type = :question_type, follow_up_question_id = :follow_up_question_id, follow_up_choice_id = :follow_up_choice_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute([
            ':id' => $id,
            ':survey_id' => $survey_id,
            ':question_text' => $question_text,
            ':question_type' => $question_type,
            ':follow_up_question_id' => $follow_up_question_id,
            ':follow_up_choice_id' => $follow_up_choice_id,
        ]);
    }

    /**
     * Delete a question by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteQuestionById(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM questions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}