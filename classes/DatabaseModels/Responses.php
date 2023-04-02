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
     * @param int $survey_id The ID of the survey the response is for
     * @param int $question_id The ID of the question the response is for
     * @param int $choice_id The ID of the choice the user selected (if applicable)
     * @param string $response_text The response text (if applicable)
     * @param int $user_id The ID of the user who submitted the response
     * @return bool
     */
    public function addResponse(int $survey_id, int $question_id, ?int $choice_id, string $response_text, int $user_id): bool
    {
        if ($choice_id === null || $choice_id === "" || $choice_id === 0) {
            $stmt = $this->connection->prepare("INSERT INTO responses (survey_id, question_id, response_text, user_id) VALUES (:survey_id, :question_id, :response_text, :user_id)");
            return $stmt->execute([
                ':survey_id' => $survey_id,
                ':question_id' => $question_id,
                ':response_text' => $response_text,
                ':user_id' => $user_id
            ]);
        } else {
            $stmt = $this->connection->prepare("INSERT INTO responses (survey_id, question_id, choice_id, user_id) VALUES (:survey_id, :question_id, :choice_id, :user_id)");
            return $stmt->execute([
                ':survey_id' => $survey_id,
                ':question_id' => $question_id,
                ':choice_id' => $choice_id,
                ':user_id' => $user_id
            ]);
        }
    }


    /**
     * Get all responses by user.
     *
     * @param string $string The user ID to retrieve responses for
     * @param string $type The type of ID to retrieve responses for (e.g. survey_id or question_id)
     * @return false|array Returns an array of response data, or false if the query failed
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
     * @param int $id The ID of the response to update
     * @param string $response_text The new response text
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
     * @param int $id The ID of the response to delete
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
     * @param int $survey_id The ID of the survey to count unique users for
     * @return int The number of unique users who responded to the survey
     */
    public function countUniqueUsersBySurveyId(int $survey_id): int
    {
        $stmt = $this->connection->prepare("SELECT COUNT(DISTINCT user_id) FROM responses WHERE survey_id = :survey_id");
        $stmt->execute([':survey_id' => $survey_id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count the number of responses for a given choice_id.
     *
     * @param int $choice_id The ID of the choice to count responses for
     * @return int The number of responses for the given choice
     */
    public function countResponsesByChoiceId(int $choice_id): int
    {
        $sql = "SELECT COUNT(*) FROM responses WHERE choice_id = :choice_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':choice_id', $choice_id, \PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * Count the number of unique users who responded to a given question and selected a given choice.
     *
     * @param int $question_id The ID of the question to count responses for
     * @param int $question_choice The ID of the choice to count responses for
     * @return int The number of unique users who responded to the question and selected the choice
     */
    public function countUniqueUsersByChoice(int $question_id, int $question_choice): int
    {
        $sql = "SELECT COUNT(DISTINCT user_id) FROM responses WHERE question_id = :question_id AND choice_id = :question_choice";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':question_id', $question_id, \PDO::PARAM_INT);
        $stmt->bindParam(':question_choice', $question_choice, \PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * Count the number of responses for a given choice_id and reference choice.
     *
     * @param int $choice_id The ID of the choice to count responses for
     * @param int $ref_question_id The ID of the question to use as a reference
     * @param int $ref_question_choice The ID of the reference choice to compare against
     * @return int The number of responses for the given choice and reference choice
     */
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

    /**
     * Get all responses for a given question_id where the user selected a specific choice for a different question.
     *
     * @param int $question_id The ID of the question to retrieve responses for
     * @param int $ref_question_id The ID of the reference question
     * @param int $ref_question_choice The ID of the reference question choice
     * @return array Returns an array of response data
     */
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

    /**
     * Check if a user has already submitted a response to a survey.
     *
     * @param int $survey_id The ID of the survey to check
     * @param int $user_id The ID of the user to check
     * @return bool Returns true if the user has already submitted a response, false otherwise
     */
    public function hasUserSubmittedResponse(int $survey_id, int $user_id): bool
    {
        $responses = $this->getResponsesBy($survey_id, "survey_id");
        foreach ($responses as $response) {
            if ($response['user_id'] == $user_id) {
                return true;
            }
        }
        return false;
    }
}