<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionAttempt extends Model
{
    protected $table = 'questions_attempts';

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
