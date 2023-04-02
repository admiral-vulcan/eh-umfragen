<?php
/**
 * Class Results
 *
 * Provides functionality for handling result-related database operations.
 */
namespace EHUmfragen\DatabaseModels;

use EHUmfragen\DatabaseHandler;

class Results extends DatabaseHandler
{
    private Surveys $surveys;
    private Responses $responses;
    private Creators $creators;
    private Collaborators $collaborators;

    /**
     * Results constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->surveys = new Surveys($this->connection);
        $this->responses = new Responses($this->connection);
        $this->creators = new Creators($this->connection);
        $this->collaborators = new Collaborators($this->connection);
    }

    /**
     * Get results data by survey ID.
     *
     * @param int $survey_id The ID of the survey to get results for
     * @return array Returns an array of result data
     */
    public function getResultsBySurveyId(int $survey_id): array
    {
        $result = [];
        $totalRespondents = $this->responses->countUniqueUsersBySurveyId($survey_id);

        $questions = $this->getQuestionsBySurveyId($survey_id);

        foreach ($questions as $question) {
            $question_id = $question['id'];
            $question_type = $question['question_type'];

            if (in_array($question_type, ['single_choice', 'multiple_choice', 'dropdown'])) {
                $choices = $this->getChoicesByQuestionId($question_id);
                $answeredCount = 0;

                foreach ($choices as $choice) {
                    $choice_id = $choice['id'];
                    $count = $this->responses->countResponsesByChoiceId($choice_id);
                    $answeredCount += $count;

                    $result[$question_id]['choices'][$choice_id] = [
                        'text' => $choice['choice_text'],
                        'count' => $count,
                    ];
                }

                $result[$question_id]['question'] = [
                    'text' => $question['question_text'],
                    'question_type' => $question_type,
                ];
                $result[$question_id]['total'] = $answeredCount;
                $result[$question_id]['not_answered'] = $totalRespondents - $answeredCount;
            } elseif ($question_type === 'free_text') {
                $responses = $this->responses->getResponsesBy($question_id, "question_id");
                $answeredCount = count($responses);

                $result[$question_id]['question'] = [
                    'text' => $question['question_text'],
                    'question_type' => $question_type,
                ];
                $result[$question_id]['responses'] = $responses;
                $result[$question_id]['total'] = $answeredCount;
                $result[$question_id]['not_answered'] = $totalRespondents - $answeredCount;
            }
        }

        return $result;
    }

    /**
     * Get relative results by choice.
     *
     * @param int $survey_id The ID of the survey to get results for
     * @param int $question_id The ID of the question to get results for
     * @param int $question_choice The ID of the choice to get relative results for
     * @return array
     */
    public function getRelativeResultsByChoice(int $survey_id, int $question_id, int $question_choice): array
    {
        $result = [];
        $totalRespondents = $this->responses->countUniqueUsersByChoice($question_id, $question_choice);

        $questions = $this->getQuestionsBySurveyId($survey_id);

        foreach ($questions as $question) {
            $current_question_id = $question['id'];
            $question_type = $question['question_type'];

            if (in_array($question_type, ['single_choice', 'multiple_choice', 'dropdown'])) {
                $choices = $this->getChoicesByQuestionId($current_question_id);
                $answeredCount = 0;

                foreach ($choices as $choice) {
                    $choice_id = $choice['id'];
                    $count = $this->responses->countResponsesByChoiceIdAndReferenceChoice($choice_id, $question_id, $question_choice);
                    $answeredCount += $count;

                    $result[$current_question_id]['choices'][$choice_id] = [
                        'text' => $choice['choice_text'],
                        'count' => $count,
                    ];
                }

                $result[$current_question_id]['question'] = [
                    'text' => $question['question_text'],
                    'question_type' => $question_type,
                ];
                $result[$current_question_id]['total'] = $answeredCount;
                $result[$current_question_id]['not_answered'] = $totalRespondents - $answeredCount;
            } elseif ($question_type === 'free_text') {
                $responses = $this->responses->getResponsesByQuestionIdAndReferenceChoice($current_question_id, $question_id, $question_choice);
                $answeredCount = count($responses);

                $result[$current_question_id]['question'] = [
                    'text' => $question['question_text'],
                    'question_type' => $question_type,
                ];
                $result[$current_question_id]['responses'] = $responses;
                $result[$current_question_id]['total'] = $answeredCount;
                $result[$current_question_id]['not_answered'] = $totalRespondents - $answeredCount;
            }
        }

        return $result;
    }

    /**
     * Get all questions for a survey.
     *
     * @param int $survey_id The ID of the survey to get questions for
     * @return array
     */
    private function getQuestionsBySurveyId(int $survey_id): array
    {
        $sql = "SELECT * FROM questions WHERE survey_id = :survey_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':survey_id', $survey_id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all choices for a question.
     *
     * @param int $question_id The ID of the question
     * @return array Returns an array of choices
     */
    private function getChoicesByQuestionId(int $question_id): array
    {
        $sql = "SELECT * FROM question_choices WHERE question_id = :question_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':question_id', $question_id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Download results for a survey in CSV format.
     *
     * @param int $survey_id The ID of the survey to download results for
     * @return array Returns an array with 'title' and 'csv' keys
     */
    public function downloadResultsbySurveyId(int $survey_id)
    {
        $survey = $this->surveys->getSurvey($survey_id);
        $title = $survey['title'];
        $results = $this->getResultsBySurveyId($survey_id);
        $csv = "\xEF\xBB\xBF"; // write BOM (Byte Order Mark) for UTF-8 encoding
        $csv .= "Question ID;Question Text;Question Type;Choice ID;Choice Text;Answer Count;Not Answered Count\r\n";

        foreach ($results as $question_id => $result) {
            $question_text = $result['question']['text'];
            $question_type = $result['question']['question_type'];

            if (isset($result['choices'])) {
                foreach ($result['choices'] as $choice_id => $choice) {
                    $choice_text = $choice['text'];
                    $answer_count = $choice['count'];
                    $not_answered_count = $result['not_answered'];

                    $csv .= "$question_id;$question_text;$question_type;$choice_id;$choice_text;$answer_count;$not_answered_count\r\n";
                }
            } elseif (isset($result['responses'])) {
                foreach ($result['responses'] as $response) {
                    $response_text = $response['response_text'];
                    if ($response_text !== '') {
                        $answer_count = 1;
                        $not_answered_count = $result['not_answered'];
                        $csv .= "$question_id;$question_text;$question_type;;$response_text;$answer_count;$not_answered_count\r\n";
                    }
                }
            }
        }
        return array('title' => $title, 'csv' => $csv);
    }

    /**
     * Download survey metadata in CSV format.
     *
     * @param int $survey_id The ID of the survey to download metadata for
     * @return array Returns an array with 'title' and 'csv' keys
     */
    public function downloadMetasbySurveyId(int $survey_id): array
    {
        $survey = $this->surveys->getSurvey($survey_id);
        $responses = $this->responses->countUniqueUsersBySurveyId($survey_id);
        $csv = "\xEF\xBB\xBF"; // write BOM (Byte Order Mark) for UTF-8 encoding
        $csv .= "Titel;Untertitel;Beschreibung 1;Beschreibung 2;Creator;Collaborators;Zielgruppe;Anzahl (n);Aktiviert am;Deaktiviert am\r\n";

        $title = $survey['title'];
        $subtitle = $survey['subtitle'];
        $description = $survey['description'];
        $subdescription = $survey['subdescription'];
        $creator_id = $survey['creator_id'];
        $creator = $this->creators->getCreatorBy($creator_id);
        $creator_name = $creator['firstname'] . " " . $creator['familyname'];
        $collaborators = $this->collaborators->getCollaborators($survey_id);
        foreach ($collaborators as $thisCollaborator) {
            $collaborators_names .= $this->creators->getCreatorBy($thisCollaborator)['firstname'] . " " . $this->creators->getCreatorBy($thisCollaborator)['familyname'] . ", ";
        }
        if (isset($collaborators_names)) {
            $collaborators_names = substr($collaborators_names, 0, -2);
        } else {
            $collaborators_names = "";
        }
        $target_group = $survey['target_group'];
        $activated_at = $survey['activated_at'];
        $inactivated_at = $survey['inactivated_at'];

        $csv .= "$title;$subtitle;$description;$subdescription;$creator_name;$collaborators_names;$target_group;$responses;$activated_at;$inactivated_at\r\n";
        return array('title' => $title, 'csv' => $csv);
    }
}