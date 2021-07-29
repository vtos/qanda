<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\AnswerQuestion\AnswerStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QuestionAttempt extends Model
{
    protected $table = 'questions_attempts';

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function scopeAnsweredCount(Builder $query): Builder
    {
        return QuestionAttempt::query()->where('status', '<>', AnswerStatus::notAnswered()->asString());
    }

    public function scopeCorrectCount(Builder $query): Builder
    {
        return QuestionAttempt::query()->where('status', AnswerStatus::correct()->asString());
    }
}
