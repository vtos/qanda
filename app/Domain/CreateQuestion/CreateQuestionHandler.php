<?php

declare(strict_types=1);

namespace App\Domain\CreateQuestion;

use App\Domain\Common\AnswerText;
use App\Models\Question;

final class CreateQuestionHandler
{
    public function handle(CreateQuestion $createQuestion): void
    {
        // Passing the command data through value objects which perform the validation.
        $questionText = QuestionText::fromString($createQuestion->questionText());
        $questionAnswer = AnswerText::fromString($createQuestion->questionAnswer());

        $questionModel = new Question();
        $questionModel->question_text = $questionText->asString();
        $questionModel->question_answer = $questionAnswer->asString();

        $questionModel->save();
    }
}
