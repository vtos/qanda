<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\AnswerQuestion\AnswerStatus;
use App\Models\Question;
use App\Models\QuestionAttempt;

class QuestionAttemptFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = QuestionAttempt::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question_id' => Question::factory(),
            'user_answer' => '',
            'status' => AnswerStatus::notAnswered()->asString(),
        ];
    }
}
