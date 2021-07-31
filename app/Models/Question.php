<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\AnswerQuestion\AnswerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Question extends Model
{
    use HasFactory;

    public function attempt()
    {
        return $this->hasOne(QuestionAttempt::class)
            ->withDefault(
                [
                    'user_answer' => '',
                    'status' => AnswerStatus::notAnswered()->asString(),
                ]
            );
    }

    /**
     * A query to list all the created questions.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeList(Builder $query): Builder
    {
        return $query->select(
            [
                'question_text',
                'question_answer',
            ]
        );
    }

    /**
     * A query to list all questions with answer attempts.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeWithAttempts(Builder $query): Builder
    {
        return $query->with('attempt:id,user_answer,status,question_id');
    }
}
