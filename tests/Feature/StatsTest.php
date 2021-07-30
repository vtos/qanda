<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Domain\AnswerQuestion\AnswerStatus;

/**
 * This test implicitly tests our custom query builder to fetch questions attempts with a specific status.
 */
class StatsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_count_questions_with_correct_answers(): void
    {
        // Questions with incorrect answers.
        Question::factory()
            ->count(3)
            ->sequence(function($sequence) {
                return [
                    'question_answer' => 'Correct answer ' . $sequence->index,
                ];
            })->hasAttempt(
                [
                    'user_answer' => 'Incorrect answer',
                    'status' => AnswerStatus::incorrect()->asString(),
                ]
            )->create();

        // A couple of questions answered correctly.
        Question::factory()
            ->count(2)
            ->hasAttempt(function(array $attributes, Question $question) {
                return [
                    'user_answer' => $question->question_answer,
                    'status' => AnswerStatus::correct()->asString(),
                ];
            })->create();

        // And some not answered.
        Question::factory()
            ->count(4)
            ->hasAttempt(
                [
                    'status' => AnswerStatus::notAnswered()->asString(),
                ]
            )->create();

        $this->assertEquals(
            2,
            QuestionAttempt::whereHasCorrectAnswer()->count()
        );
    }

    /**
     * @test
     */
    public function it_can_count_questions_with_answers(): void
    {
        // Questions with incorrect answers.
        Question::factory()
            ->count(3)
            ->sequence(function($sequence) {
                return [
                    'question_answer' => 'Correct answer ' . $sequence->index,
                ];
            })->hasAttempt(
                [
                    'user_answer' => 'Incorrect answer',
                    'status' => AnswerStatus::incorrect()->asString(),

                ]
            )->create();

        // And some not answered.
        Question::factory()
            ->count(2)
            ->hasAttempt(
                [
                    'status' => AnswerStatus::notAnswered()->asString(),
                ]
            )->create();

        $this->assertEquals(
            3,
            QuestionAttempt::whereAnswered()->count()
        );
    }
}
