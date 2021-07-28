<?php

declare(strict_types=1);

namespace App\Domain\ListQuestions;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Question;

final class GetQuestions
{
    /**
     * Returns a collection of read models for questions.
     */
    public function all(): Collection
    {
        return Question::all(
            [
                'question_text',
                'question_answer',
            ]
        );
    }

    public function withAttempts(): Collection
    {
        return Question::with('attempt')
            ->get(
                [
                    'id',
                    'question_text',
                    'user_answer',
                    'status',
                ]
            );
    }
}
