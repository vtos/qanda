<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function attempt()
    {
        return $this->hasOne(QuestionAttempt::class)
            ->withDefault(
                [
                    'user_answer' => '',
                    'status' => 'not_answered', // TODO: use status value object instead.
                ]
            );
    }
}
