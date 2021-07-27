<?php

declare(strict_types=1);

namespace App\Domain\AnswerQuestion;

use InvalidArgumentException;

final class AnswerText
{
    private string $text;

    private function __construct(string $text)
    {
        if (empty(trim($text))) {
            throw new InvalidArgumentException('Answer text cannot be empty.');
        }
        $this->text = $text;
    }

    public function equals(AnswerText $textToCompare): bool
    {
        return 0 === strcmp(
            $this->text,
            $textToCompare->asString()
        );
    }

    public function asString(): string
    {
        return $this->text;
    }

    public static function fromString(string $text): self
    {
        return new self($text);
    }
}
