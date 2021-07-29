<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\AnswerQuestion\AnswerStatus;
use Illuminate\Database\Eloquent\Builder;

/**
 * This class allows for having specific queries based on some domain logic.
 */
class QuestionAttemptQueryBuilder extends Builder
{
    public function whereAnswered(): self
    {
        return $this->where('status', '<>', AnswerStatus::notAnswered()->asString());
    }

    public function whereHasCorrectAnswer(): self
    {
        return $this->where('status', AnswerStatus::correct()->asString());
    }
}
