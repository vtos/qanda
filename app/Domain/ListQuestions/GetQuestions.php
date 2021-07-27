<?php

declare(strict_types=1);

namespace App\Domain\ListQuestions;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Read\Question as ReadQuestion;

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

    public function attempts(): Collection
    {

    }
}
