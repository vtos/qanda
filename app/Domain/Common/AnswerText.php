<?php

declare(strict_types=1);

namespace App\Domain\Common;

use Webmozart\Assert\Assert;

final class AnswerText
{
    private string $text;

    private function __construct(string $text)
    {
        Assert::notEmpty($text);
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
