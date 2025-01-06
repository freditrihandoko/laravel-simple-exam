<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Answer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    protected $topicId;

    public function __construct($topicId)
    {
        $this->topicId = $topicId;
    }

    public function model(array $row)
    {
        $question = new Question();
        $question->topic_id = $this->topicId;
        $question->question_text = $row['question_text'];
        $question->question_type = $row['question_type'];
        $question->save();

        // if ($row['question_type'] == 'multiple_choice') {
        //     for ($i = 1; $i <= 4; $i++) {
        //         if (!empty($row["answer_$i"])) {
        //             $answer = new Answer();
        //             $answer->question_id = $question->id;
        //             $answer->answer_text = $row["answer_$i"];
        //             $answer->is_correct = $row["is_correct_$i"] ?? 0;
        //             $answer->save();
        //         }
        //     }
        // }
        if ($row['question_type'] == 'multiple_choice') {
            $answerCount = 0;
            foreach ($row as $key => $value) {
                if (strpos($key, 'answer_') === 0) {
                    $answerCount++;
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->answer_text = $value;
                    $answer->is_correct = isset($row["is_correct_$answerCount"]) ? $row["is_correct_$answerCount"] : 0;
                    $answer->save();
                }
            }
        }

        return $question;
    }
}
