<?php

declare(strict_types=1);

namespace App\Domain\CreateQuestion;

/**
 * Class to represent a DTO (Data Transfer Object) to contain data required for question creation.
 */
final class CreateQuestion
{
    private string $questionText;

    private string $questionAnswer;

    public function __construct(string $questionText, string $questionAnswer)
    {
        $this->questionText = $questionText;
        $this->questionAnswer = $questionAnswer;
    }

    public function questionText(): string
    {
        return $this->questionText;
    }

    public function questionAnswer(): string
    {
        return $this->questionAnswer;
    }
}
