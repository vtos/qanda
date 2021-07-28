<?php

declare(strict_types=1);

namespace App\Domain\CreateQuestion;

use App\Models\Question;

final class CreateQuestionHandler
{
    public function handle(CreateQuestion $createQuestion): void
    {
        $questionModel = new Question();
        $questionModel->question_text = $createQuestion->questionText();
        $questionModel->question_answer = $createQuestion->questionAnswer();

        $questionModel->save();
    }
}
