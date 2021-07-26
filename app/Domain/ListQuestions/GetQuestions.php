<?php

declare(strict_types=1);

namespace App\Domain\ListQuestions;

use App\Models\Read\Question as ReadQuestion;
use Illuminate\Database\Eloquent\Collection;

final class GetQuestions
{
    /**
     * Returns a collection of read models for questions.
     */
    public function all(): Collection
    {
        return ReadQuestion::all(
            [
                'question_text',
                'question_answer',
            ]
        );
    }
}
