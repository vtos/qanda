<?php

declare(strict_types=1);

namespace App\Domain\AnswerQuestion;

/**
 * A DTO containing data related to question answer.
 */
final class AnswerQuestion
{
    private int $questionId;

    private string $answerText;

    public function __construct(int $questionId, string $answerText)
    {
        $this->questionId = $questionId;
        $this->answerText = $answerText;
    }

    public function questionId(): int
    {
        return $this->questionId;
    }

    public function answerText(): string
    {
        return $this->answerText;
    }
}
