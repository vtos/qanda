<?php

declare(strict_types=1);

namespace App\Domain\AnswerQuestion;

use Webmozart\Assert\Assert;

/**
 * A value object describing the status property of a given answer.
 */
final class AnswerStatus
{
    private const NOT_ANSWERED = 'not_answered';

    private const CORRECT = 'correct';

    private const INCORRECT = 'incorrect';

    private string $statusOption;

    private function __construct(string $statusOption)
    {
        Assert::inArray(
            $statusOption,
            [
                self::NOT_ANSWERED,
                self::CORRECT,
                self::INCORRECT,
            ]
        );
        $this->statusOption = $statusOption;
    }

    public function asString(): string
    {
        return $this->statusOption;
    }

    public function isCorrect(): bool
    {
        return self::CORRECT === $this->statusOption;
    }

    public static function fromString(string $statusOption): self
    {
        return new self($statusOption);
    }

    public static function notAnswered(): self
    {
        return new self(self::NOT_ANSWERED);
    }

    public static function correct(): self
    {
        return new self(self::CORRECT);
    }

    public static function incorrect(): self
    {
        return new self(self::INCORRECT);
    }
}
