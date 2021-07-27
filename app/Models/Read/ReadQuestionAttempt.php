<?php

declare(strict_types=1);

namespace App\Models\Read;

use App\Models\Question;
use App\Models\QuestionAttempt;
use Illuminate\Contracts\Support\Arrayable;

final class ReadQuestionAttempt implements Arrayable
{
    private Question $question;

    private QuestionAttempt $attempt;

    public function __construct(
        Question $question,
        QuestionAttempt $attempt
    ) {
        $this->question = $question;
        $this->attempt = $attempt;
    }

    public function toArray()
    {
        return [
            'question_id' => $this->question->id,
            'question_text' => $this->question->question_text,
            'user_answer' => $this->attempt->user_answer,
            'status' => $this->attempt->status,
        ];
    }
}
