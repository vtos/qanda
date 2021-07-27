<?php

declare(strict_types=1);

namespace App\Domain\AnswerQuestion;

use LogicException;

final class CouldNotAnswerQuestion extends LogicException
{
    public static function becauseAlreadyHasCorrectAnswer(): self
    {
        throw new self('Answering questions which are already correct is not allowed.');
    }
}
