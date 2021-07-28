<?php

declare(strict_types=1);

namespace App\Domain\AnswerQuestion;

use App\Models\Question as QuestionModel;

final class AnswerQuestionHandler
{
    public function handle(AnswerQuestion $command): AnswerStatus
    {
        $questionModel = QuestionModel::with('attempt')->findOrFail($command->questionId());

        $question = new Question(
            AnswerText::fromString($questionModel->question_answer),
            AnswerStatus::fromString($questionModel->attempt->status)
        );
        $question->answer(
            AnswerText::fromString($command->answerText())
        );

        $attempt = $questionModel->attempt;
        $attempt->user_answer = $command->answerText();
        $attempt->status = $question->status()->asString();
        $attempt->save();

        return $question->status();
    }
}
