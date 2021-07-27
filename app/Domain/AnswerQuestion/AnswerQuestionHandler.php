<?php

declare(strict_types=1);

namespace App\Domain\AnswerQuestion;

final class AnswerQuestionHandler
{
    public function handle(AnswerQuestion $command): void
    {
        $question = new Question();
        $question->answer(
            AnswerText::fromString($command->answerText())
        );
    }
}
