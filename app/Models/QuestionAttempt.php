<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\AnswerQuestion\AnswerStatus;

class QuestionAttempt extends Model
{
    use HasFactory;

    protected $table = 'questions_attempts';

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function newEloquentBuilder($query): QuestionAttemptQueryBuilder
    {
        return new QuestionAttemptQueryBuilder($query);
    }

    /**
     * Make use of value object from the domain.
     */
    public function getStatusAttribute($value): AnswerStatus
    {
        return AnswerStatus::fromString($value);
    }

    /**
     * A shortcut method to make status checking less verbose in the calling code.
     */
    public function hasCorrectAnswer(): bool
    {
        return $this->status->isCorrect();
    }
}
