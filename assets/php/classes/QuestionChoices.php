<?php
/**
 * Class QuestionChoices
 *
 * Provides functionality for handling question choice-related database operations.
 */

namespace assets\php\classes;

use PDO;

class QuestionChoices extends DatabaseHandler
{
    /**
     * Add a question choice.
     *
     * @param int $survey_id
     * @param int $question_id
     * @param string $choice_text
     * @return int
     */
    public function addQuestionChoice(int $survey_id, int $question_id, string $choice_text): int
    {
        $stmt = $this->connection->prepare("INSERT INTO question_choices (survey_id, question_id, choice_text) VALUES (:survey_id, :question_id, :choice_text)");
        $stmt->execute([
            ':survey_id' => $survey_id,
            ':question_id' => $question_id,
            ':choice_text' => $choice_text,
        ]);

        return $this->connection->lastInsertId();
    }

    /**
     * Get a question choice by ID.
     *
     * @param int $id
     * @return false|array
     */
    public function getQuestionChoiceById(int $id): false|array
    {
        $stmt = $this->connection->prepare("SELECT * FROM question_choices WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get question choices by question ID.
     *
     * @param int $question_id
     * @return false|array
     */
    public function getQuestionChoicesByQuestionId(int $question_id): false|array
    {
        $stmt = $this->connection->prepare("SELECT * FROM question_choices WHERE question_id = :question_id");
        $stmt->execute([':question_id' => $question_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a question choice.
     *
     * @param int $id
     * @param string $choice_text
     * @return bool
     */
    public function updateQuestionChoice(int $id, string $choice_text): bool
    {
        $stmt = $this->connection->prepare("UPDATE question_choices SET choice_text = :choice_text, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':choice_text' => $choice_text,
        ]);
    }

    /**
     * Delete a question choice by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteQuestionChoiceById(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM question_choices WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get question choices by survey ID.
     *
     * @param int $survey_id
     * @return false|array
     */
    public function getQuestionChoicesBySurveyId(int $survey_id): false|array
    {
        $stmt = $this->connection->prepare("SELECT * FROM question_choices WHERE survey_id = :survey_id");
        $stmt->execute([':survey_id' => $survey_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
