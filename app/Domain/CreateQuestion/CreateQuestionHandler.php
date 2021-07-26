<?php

declare(strict_types=1);

namespace App\Domain\CreateQuestion;

use App\Models\Question;

final class CreateQuestionHandler
{
    private Question $questionModel;

    public function __construct(Question $questionModel)
    {
        $this->questionModel = $questionModel;
    }

    public function handle(CreateQuestion $createQuestion): void
    {
        $this->questionModel->question_text = $createQuestion->questionText();
        $this->questionModel->question_answer = $createQuestion->questionAnswer();

        $this->questionModel->save();
    }
}
