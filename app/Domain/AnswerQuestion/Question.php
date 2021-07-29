<?php

declare(strict_types=1);

namespace App\Domain\AnswerQuestion;

use App\Domain\Common\AnswerText;

final class Question
{
    private AnswerText $correctAnswer;

    private AnswerStatus $status;

    public function __construct(AnswerText $correctAnswer, AnswerStatus $status)
    {
        $this->correctAnswer = $correctAnswer;
        $this->status = $status;
    }

    public function answer(AnswerText $answerProvided): void
    {
        if ($this->status->isCorrect()) {
            throw CouldNotAnswerQuestion::becauseAlreadyHasCorrectAnswer();
        }

        $answerProvided->equals($this->correctAnswer)
            ? $this->status = AnswerStatus::correct()
            : $this->status = AnswerStatus::incorrect();
    }

    public function status(): AnswerStatus
    {
        return $this->status;
    }
}
