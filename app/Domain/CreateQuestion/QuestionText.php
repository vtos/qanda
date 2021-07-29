<?php

declare(strict_types=1);

namespace App\Domain\CreateQuestion;

use Webmozart\Assert\Assert;

final class QuestionText
{
    private string $text;

    private function __construct(string $text)
    {
        Assert::notEmpty($text);
        $this->text = $text;
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
